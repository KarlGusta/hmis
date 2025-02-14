<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/PatientQueue.php');

class Appointment {
    private $db;
    private $table = 'appointments';

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function createAppointment($data) {
        try {
            $query = "INSERT INTO {$this->table} (patient_id, doctor_id, department_id, appointment_datetime,
                      reason, status, notes, created_by, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                      
            $params = [
                $data['patient_id'],
                $data['doctor_id'],
                $data['department_id'],
                $data['appointment_datetime'],
                $data['reason'],
                $data['status'] ?? 'scheduled',
                $data['notes'],
                $_SESSION['user_id']
            ];          

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to create appointment: " . $e->getMessage());
        }
    }

    public function getAllAppointments() {
        try {
            $query = "SELECT a.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                             d.specialization
                      FROM {$this->table} a
                      JOIN patients p ON a.patient_id = p.id
                      JOIN doctors d ON a.doctor_id = d.id
                      ORDER BY a.appointment_datetime DESC";

            return $this->db->fetchAll($query);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch appointments: " . $e->getMessage());
        }
    }

    public function getAppointmentsByDoctor($doctor_id) {
        try {
            $query = "SELECT a.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name
                      FROM {$this->table} a
                      JOIN patients p ON a.patient_id = p.id
                      WHERE a.doctor_id = :doctor_id
                      ORDER BY a.appointment_datetime DESC";

            return $this->db->fetchAll($query, [':doctor_id' => $doctor_id]);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch doctor appointments: " . $e->getMessage());
        }
    }

    public function getAppointmentsByPatient($patient_id) {
        try {
            $query = "SELECT a.*,
                             CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                             d.specialization
                      FROM {$this->table} a
                      JOIN doctors d ON a.doctor_id = d.id
                      WHERE a.patient_id = :patient_id
                      ORDER BY a.appointment_datetime DESC";

            return $this->db->fetchAll($query, [':patient_id' => $patient_id]);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch patient appointments: " . $e->getMessage());
        }
    }

    public function getAppointment($id) {
        try {
            $query = "SELECT a.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                             d.specialization
                      FROM {$this->table} a
                      JOIN patients p ON a.patient_id = p.id
                      JOIN doctors d ON a.doctor_id = d.id
                WHERE a.id = ?";
        
            return $this->db->fetchOne($query, [$id]);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch appointment: " . $e->getMessage());
        }
    }

    public function checkIn($data) {
        try {
            $this->db->beginTransaction();

            // 1. Update appointment status and check-in time
            $statusQuery = "UPDATE {$this->table}
                            SET status = 'checked_in',
                                check_in_time = NOW(),
                                updated_at = NOW()
                            WHERE id = ?";

            $this->db->executeQuery($statusQuery, [$data['appointment_id']]);
            
            // 2. Insert vital signs
            $vitalsQuery = "INSERT INTO vital_signs
                            (appointment_id, blood_pressure, temperature,
                             heart_rate, symptoms, notes, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";

            $vitalsParams = [
                $data['appointment_id'],
                $data['vital_signs']['blood_pressure'],
                $data['vital_signs']['temperature'],
                $data['vital_signs']['heart_rate'],
                $data['symptoms'],
                $data['notes']
            ];              
            
            $this->db->executeQuery($vitalsQuery, $vitalsParams);

            // 3. Get appointment details for queue
            $appointmentDetails = $this->getAppointment($data['appointment_id']);
            
            // 4. Create new PatientQueue instance and add to queue
            $patientQueue = new PatientQueue($this->db);
            $queueData = [
                'patient_id' => $appointmentDetails['patient_id'],
                'called_by' => $appointmentDetails['doctor_id'],
                'priority' => $data['priority'],
                'symptoms' => $data['symptoms'],
                'notes' => $data['notes']
            ];
            
            $patientQueue->addToQueue($queueData);

            // 5. Log the status change
            $this->logStatusChange($data['appointment_id'], 'scheduled', 'checked_in');

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Failed to check in appointment: " . $e->getMessage());
        }
    }
    
    // Record vital signs for an appointment
    public function recordVitalSigns($appointmentId, $vitalSigns) {
        try {
            $query = "UPDATE {$this->table}
                      SET blood_pressure = ?,
                          temperature = ?,
                          heart_rate = ?,
                          updated_at = NOW()
                      WHERE id = ?";

            $params = [
                $vitalSigns['blood_pressure'] ?? null,
                $vitalSigns['temperature'] ?? null,
                $vitalSigns['heart_rate'] ?? null,
                $appointmentId
            ];          

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to record vital signs: " . $e->getMessage());
        }
    }

    public function updateAppointment($id, $data) {
        try {
            $query = "UPDATE {$this->table}
                      SET patient_id = :patient_id,
                          doctor_id = :doctor_id,
                          appointment_datetime = :appointment_datetime,
                          reason = :reason,
                          status = :status,
                          notes = :notes,
                          updated_at = NOW()
                      WHERE id = :id";

            $params = [
                ':id' => $id,
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':appointment_datetime' => $data['appointment_datetime'],
                ':reason' => $data['reason'],
                ':status' => $data['status'],
                ':notes' => $data['notes']
            ];          

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            throw new Exception("Failed to update appointment: " . $e->getMessage());
        }
    }

    public function updateStatus($id, $status) {
        try {
            // First, get the current appointment to check if it exists
            $currentAppointment = $this->getAppointment($id);
            if (!$currentAppointment) {
                throw new Exception("Appointment not found");
            }

            // Prepare the base query with named parameters
            $query = "UPDATE appointments 
                     SET status = ?,
                         updated_at = NOW()";
            
            // Initialize params array with basic parameters
            $params = [$status];

            // Add additional fields based on status
            switch ($status) {
                case 'checked_in':
                    $query .= ", check_in_time = NOW()";
                    break;
                case 'in_progress':
                    $query .= ", start_time = NOW()";
                    break;
                case 'completed':
                    $query .= ", end_time = NOW()";
                    break;
                case 'cancelled':
                    $query .= ", cancelled_at = NOW(), cancelled_by = ?";
                    $params[] = $_SESSION['user_id'] ?? null;
                    break;
            }
            
            // Complete the query with WHERE clause
            $query .= " WHERE id = ?";
            $params[] = $id;

            // Execute the update query
            $result = $this->db->executeQuery($query, $params);
            
            if ($result === false) {
                throw new Exception("Failed to update appointment status");
            }

            // Log the status change in appointment_history
            $this->logStatusChange($id, $currentAppointment['status'], $status);
            
            return true;

        } catch (Exception $e) {
            error_log("Database error in updateStatus: " . $e->getMessage());
            throw new Exception("Failed to update appointment status: " . $e->getMessage());
        }
    }

    /**
     * Log appointment status changes in the appointment_history table
     */
    private function logStatusChange($appointmentId, $previousStatus, $newStatus) {
        try {
            $query = "INSERT INTO appointment_history 
                     (appointment_id, action, previous_status, new_status, 
                      performed_by, created_at)
                     VALUES 
                     (:appointment_id, 'status_changed', :previous_status, 
                      :new_status, :performed_by, NOW())";

            $params = [
                ':appointment_id' => $appointmentId,
                ':previous_status' => $previousStatus,
                ':new_status' => $newStatus,
                ':performed_by' => $_SESSION['user_id'] ?? null
            ];

            $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            // Log the error but don't stop the main status update
            error_log("Failed to log appointment history: " . $e->getMessage());
        }
    }

    public function deleteAppointment($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            return $this->db->executeQuery($query, [':id' => $id]);
        } catch (Exception $e) {
            throw new Exception("Failed to delete appointment: " . $e->getMessage());
        }
    }

    public function checkAvailability($doctor_id, $datetime) {
        try {
            // Convert datetime string to DateTime object for proper comparison
            $appointmentTime = new DateTime($datetime);
            
            // Check for appointments within the same hour
            $startTime = clone $appointmentTime;
            $startTime->modify('-30 minutes');
            $endTime = clone $appointmentTime;
            $endTime->modify('+30 minutes');

            $query = "SELECT COUNT(*) as count
                      FROM {$this->table} 
                      WHERE doctor_id = ?
                      AND appointment_datetime BETWEEN ? AND ?
                      AND status NOT IN ('cancelled', 'completed')";

            $result = $this->db->fetchOne($query, [
                $doctor_id,
                $startTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s')
            ]);
            
            return $result['count'] == 0;
        } catch (Exception $e) {
            throw new Exception("Failed to check availability: " . $e->getMessage());
        }
    }

    public function getTodayAppointments() {
        try {
            $query = "SELECT a.*,
                             CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                             CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                      FROM {$this->table} a
                      JOIN patients p ON a.patient_id = p.id
                      JOIN doctors d ON a.doctor_id = d.id
                      WHERE DATE(a.appointment_datetime) = CURDATE()
                      AND a.status NOT IN ('completed', 'cancelled', 'no_show')
                      ORDER BY a.appointment_datetime ASC";

            return $this->db->fetchAll($query);          
        } catch (Exception $e) {
            throw new Exception("Failed to fetch today's appointments: " . $e->getMessage());
        }
    }
}
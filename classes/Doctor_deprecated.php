<?php
require_once(__DIR__ . '/../config/database.php');

class Doctor {
    private $db;
    private $table = 'doctors';

    public function __construct() {
        $this->db = new DatabaseConnection();        
    }

    public function createDoctor($data) {
        try {
            $query = "INSERT INTO {$this->table} (name, specialization, email, phone, license_number, joining_date, status, created_at) VALUES (:name, :specialization, :email, :phone, :license_number, :joining_date, :status, NOW())";

            $params = [
                ':name' => $data['name'],
                ':specialization' => $data['specialization'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':license_number' => $data['license_number'],
                ':joining_date' => $data['joining_date'],
                ':status' => $data['status'] ?? 'active' 
            ];

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error creating doctor: " . $e->getMessage());
            throw new Exception("Failed to create doctor profile");
        }
    }

    public function updateDoctor($doctorId, $data) {
        try {
            $updateFields = [];
            $params = [':id' => $doctorId];

            foreach ($data as $key -> $value) {
                if (in_array($key, ['name', 'specialization', 'email', 'phone', 'status'])) {
                    $updateFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = :id";

            return $this->db->executeQuery($query, $params);
        } catch (Exception $e) {
            error_log("Error updating doctor: " . $e->getMessage());
            throw new Exception("Failed to update doctor profile");
        }
    }

    public function getDoctor($doctorId) {
        try {
            $query = "SELECT d.*, (SELECT COUNT(*) FROM appointments a WHERE a.doctor_id = d.id AND DATE(a.appointment_datetime) = CURDATE()) as today_appointments FROM {$this->table} d WHERE d.id = :id";

            return $this->db->fetchOne($query, [':id' => $doctorId]);
        } catch (Exception $e) {
            error_log("Error fetching doctor: " . $e->getMessage());
            throw new Exception("Failed to fetch doctor details");
        }        
    }

    public function setSchedule($doctorId, $scheduleData) {
        try {
            // Delete existing schedule
            $query = "DELETE FROM doctor_schedules WHERE doctor_id = :doctor_id";
            $this->db->executeQuery($query, [':doctor_id' => $doctorId]);

            // Insert new schedule
            $query = "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, break_start, break_end) VALUES (:doctor_id, :day_of_week, :start_time, :end_time, :break_start, :break_end)";

            foreach ($scheduleData as $schedule) {
                $params = [
                    ':doctor_id' => $doctorId,
                    ':day_of_week' => $schedule['day_of_week'],
                    ':start_time' => $schedule['start_time'],
                    ':end_time' => $schedule['end_time'],
                    ':break_start' => $schedule['break_start'] ?? null,
                    ':break_end' => $schedule['break_end'] ?? null
                ];

                $this->db->executeQuery($query, $params);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error setting doctor schedule: " . $e->getMessage());
            throw new Exception("Failed to set doctor schedule");
        }
    }

    public function getSchedule($doctorId) {
        try {
            $query = "SELECT * FROM doctor_schedules WHERE doctor_id = :doctor_id ORDER BY day_of_week ASC";

            return $this->db->fetchAll($query, [':doctor_id' => $doctorId]);
        } catch (Exception $e) {
            error_log("Error fetching doctor schedule: " . $e->getMessage());
            throw new Exception("failed to fetch doctor schedule");
        }
    }

    public function getAvailableSlots($doctorId, $date) {
        try {
            $dayOfWeek = date('w', strtotime($date));

            // Get doctor's schedule for the day
            $query = "SELECT * FROM doctor_schedules WHERE doctor_id = :doctor_id AND day_of_week = :day_of_week";

            $schedule = $this->db->fetchOne($query, [
                ':doctor_id' => $doctorId,
                ':day_of_week' => $dayOfWeek  
            ]);

            if (!$schedule) {
                return [];
            }

            // Get booked appointments
            $query = "SELECT appointment_datetime FROM appointments WHERE doctor_id = :doctor_id AND DATE(appointment_datetime) = :date AND status != 'cancelled'";

            $bookedSlots = $this->db->fetchAll($query, [
                ':doctor_id' => $doctorId,
                ':date' => $date
            ]);

            // Generate available slots
            $slots = [];
            $current = strtotime($schedule['start_time']);
            $end = strtotime($schedule['end_time']);
            $interval = 30 * 60; // 30 minutes in seconds

            while ($current < $end) {
                $timeSlot = date('H:i:s', $current);

                // Skip break time
                if ($schedule['break_start'] && $schedule['break_end']) {
                    $breakStart = strtotime($schedule['break_start']);
                    $breakEnd = strtotime($schedule['break_end']);
                    if ($current >= $breakStart && $current < $breakEnd) {
                        $current = $breakEnd;
                        continue;
                    }
                }

                // Check if slot is available
                $isBooked = false;
                foreach ($bookedSlots as $bookedSlot) {
                    if (strtotime($bookedSlot['appointment_datetime']) == strtotime($date . ' ' . $timeSlot)) {
                        $isBooked = true;
                        break;
                    }
                } 

                if (!$isBooked) {
                    $slots[] = $timeSlot;
                }

                $current += $interval;
            }

            return $slots;
        } catch (Exception $e) {
            error_log("Error getting available slots: " . $e->getMessage());
            throw new Exception("Failed to get available slots"); 
        }
    }    
}
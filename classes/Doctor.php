<?php
require_once __DIR__ . '/../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Doctor {
    private $db;
    private $doctor_id;

    public function __construct($db) {
        $this->db = $db;
    }

    private function createDoctorContact($doctor_id, $data) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO doctor_contacts (
                doctor_id, phone, email
            ) VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "iss",
            $doctor_id,
            $data['phone'],
            $data['email']
        );

        $stmt->execute();
    }

    private function createDefaultSchedule($doctor_id) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time)
             VALUES (?, ?, ?, ?)"
        );

        $start_time = "09:00:00";
        $end_time = "17:00:00";

        foreach ($this->getWorkingDays() as $day) {
            $day_of_week = $day; // Create variable to pass by reference
            $stmt->bind_param("iiss", $doctor_id, $day_of_week, $start_time, $end_time);
            $stmt->execute();
        }
    }

    private function getWorkingDays() {
        return [1, 2, 3, 4, 5, 6, 7]; // Monday to Sunday
    }

    public function getDoctorDetails($doctor_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT d.*, u.email, u.username
             FROM doctors d
             JOIN users u ON d.user_id = u.id
             WHERE d.id = ?"
        );

        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateProfile($data) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "UPDATE doctors SET
                 specialization = ?,
                 qualification = ?,
                 experience_years = ?,
                 consultation_fee = ?,
                 available_days = ?,
                 available_times = ?
                 WHERE id =?"
            );

            $available_days = implode(',', $data['available_days']);
            $available_times = json_encode($data['available_times']);

            $stmt->bind_param(
                "ssiissi",
                $data['specialization'],
                $data['qualification'],
                $data['experience_years'],
                $data['consultation_fee'],
                $available_days,
                $available_times,
                $data['doctor_id']
            );

            $result = $stmt->execute();

            if ($result) {
                $this->db->conn->commit();
                return true;
            }

            $this->db->conn->rollback();
            return false;

        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }

    public function getAllDoctors() {
        try {
            $stmt = $this->db->conn->prepare(
                "SELECT d.*, u.email, u.username, dc.phone
                 FROM doctors d
                 JOIN users u ON d.user_id = u.id
                 JOIN doctor_contacts dc ON d.id = dc.doctor_id
                 WHERE d.status = 'active'
                 ORDER BY d.id DESC"
            );

            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getSchedule($doctor_id) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM doctor_schedules
             WHERE doctor_id = ?
             ORDER BY day_of_week"
        );

        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateSchedule($schedule_data) {
        try {
            $this->db->conn->begin_transaction();

            $stmt = $this->db->conn->prepare(
                "UPDATE doctor_schedules
                 SET start_time = ?, end_time = ?,
                 break_start = ?, break_end = ?,
                 max_appointments = ?, slot_duration = ?
                 WHERE id = ?"
            );

            foreach ($schedule_data as $schedule) {
                $stmt->bind_param(
                    "ssssiis",
                    $schedule['start_time'],
                    $schedule['end_time'],
                    $schedule['break_start'],
                    $schedule['break_end'],
                    $schedule['max_appointments'],
                    $schedule['slot_duration'],
                    $schedule['id']
                );
                $stmt->execute();
            }

            $this->db->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw $e;
        }
    }
}
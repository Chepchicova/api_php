<?php
class ServiceRequest {
    private $conn;
    private $table = "service_requests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (customer_id, issue_description, status)
             VALUES (:customer_id, :issue_description, :status)"
        );
        $stmt->bindValue(":customer_id", $data['customer_id'], PDO::PARAM_INT);
        $stmt->bindValue(":issue_description", $data['issue_description'], PDO::PARAM_STR);
        $stmt->bindValue(":status", $data['status'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
             SET customer_id = :customer_id,
                 issue_description = :issue_description,
                 status = :status
             WHERE id = :id"
        );
        $stmt->bindValue(":customer_id", $data['customer_id'], PDO::PARAM_INT);
        $stmt->bindValue(":issue_description", $data['issue_description'], PDO::PARAM_STR);
        $stmt->bindValue(":status", $data['status'], PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

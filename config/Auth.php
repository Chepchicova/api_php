<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkApiKey($headers) {
        if (!isset($headers['X-API-Key'])) {
            http_response_code(401);
            echo json_encode(["error" => "unregistered user"]);
            exit;
        }

        $rawKey = $headers['X-API-Key'];

        $stmt = $this->conn->prepare("SELECT api_key FROM api_keys WHERE is_active = 1");
        $stmt->execute();

        $valid = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($rawKey, $row['api_key'])) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid API key"]);
            exit;
        }
    }
}

<?php
class Payments extends BaseModel  {
protected $conn;
protected $table_name = "payments";

public $payment_id;
public $order_id;
public $payment_type;
public $payment_status;
public $payment_date;
public $createdDate;
public $createdUser;
public $modDate;
public $modUser;
public $lockstate;

public function __construct($conn) {
    parent::__construct($conn, $this->table_name);
}

private function validate() {
    $missingFields = [];
    if (count($missingFields) > 0) {
        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
    }
    return true;
}

public function create() {
    $this->validate();
    $query = "INSERT INTO $this->table_name (order_id, payment_type, payment_status, payment_date) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssss', $this->order_id, $this->payment_type, $this->payment_status, $this->payment_date);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function readAll() {
    $query = "SELECT * FROM $this->table_name";
    $result = $this->conn->query($query);
    return $result;
}

public function read($where = "", $params = [], $types = "") {
    if (!empty($where)) {
        $query = "SELECT * FROM $this->table_name WHERE $where";
    } else {
        $query = "SELECT * FROM $this->table_name";
    }
    $stmt = $this->conn->prepare($query);
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

public function update() {
    $this->validate();
    $query = "UPDATE $this->table_name SET order_id = ?, payment_type = ?, payment_status = ?, payment_date = ?, modDate = NOW() WHERE payment_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssss', $this->order_id, $this->payment_type, $this->payment_status, $this->payment_date, $this->payment_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE payment_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->payment_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

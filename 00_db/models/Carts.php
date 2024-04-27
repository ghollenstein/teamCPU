<?php
class Carts extends BaseModel  {
protected $conn;
protected $table_name = "carts";

public $cart_id;
public $user_id;
public $session_id;
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
    $query = "INSERT INTO $this->table_name (user_id, session_id) VALUES (?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ss', $this->user_id, $this->session_id);
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
    $query = "UPDATE $this->table_name SET user_id = ?, session_id = ?, modDate = NOW() WHERE cart_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sss', $this->user_id, $this->session_id, $this->cart_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE cart_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->cart_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

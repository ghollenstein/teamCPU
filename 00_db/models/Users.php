<?php
class Users extends BaseModel  {
protected $conn;
protected $table_name = "users";

public $user_id;
public $firstname;
public $lastname;
public $password;
public $email;
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
    if (!isset($this->password) || $this->password === '') {
        $missingFields[] = 'password';
    }
    if (!isset($this->email) || $this->email === '') {
        $missingFields[] = 'email';
    }
    if (count($missingFields) > 0) {
        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
    }
    return true;
}

public function create() {
    $this->validate();
    $query = "INSERT INTO $this->table_name (firstname, lastname, password, email, createdDate, createdUser, modDate, modUser, lockstate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssssss', $this->firstname, $this->lastname, $this->password, $this->email, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate);
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
    $query = "UPDATE $this->table_name SET firstname = ?, lastname = ?, password = ?, email = ?, createdDate = ?, createdUser = ?, modUser = ?, lockstate = ?, modDate = NOW() WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssssss', $this->firstname, $this->lastname, $this->password, $this->email, $this->createdDate, $this->createdUser, $this->modUser, $this->lockstate, $this->user_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->user_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

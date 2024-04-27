<?php
class Addresses extends BaseModel  {
protected $conn;
protected $table_name = "addresses";

public $address_id;
public $user_id;
public $address_type;
public $street;
public $city;
public $state;
public $postal_code;
public $country;
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
    if (!isset($this->address_type) || $this->address_type === '') {
        $missingFields[] = 'address_type';
    }
    if (!isset($this->street) || $this->street === '') {
        $missingFields[] = 'street';
    }
    if (!isset($this->city) || $this->city === '') {
        $missingFields[] = 'city';
    }
    if (!isset($this->postal_code) || $this->postal_code === '') {
        $missingFields[] = 'postal_code';
    }
    if (!isset($this->country) || $this->country === '') {
        $missingFields[] = 'country';
    }
    if (count($missingFields) > 0) {
        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
    }
    return true;
}

public function create() {
    $this->validate();
    $query = "INSERT INTO $this->table_name (user_id, address_type, street, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssss', $this->user_id, $this->address_type, $this->street, $this->city, $this->state, $this->postal_code, $this->country);
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
    $params = [];
    $types = '';
    $updateParts = [];
    if (isset($this->user_id)) {
        $updateParts[] = 'user_id = ?';
        $params[] = $this->user_id;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->address_type)) {
        $updateParts[] = 'address_type = ?';
        $params[] = $this->address_type;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->street)) {
        $updateParts[] = 'street = ?';
        $params[] = $this->street;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->city)) {
        $updateParts[] = 'city = ?';
        $params[] = $this->city;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->state)) {
        $updateParts[] = 'state = ?';
        $params[] = $this->state;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->postal_code)) {
        $updateParts[] = 'postal_code = ?';
        $params[] = $this->postal_code;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->country)) {
        $updateParts[] = 'country = ?';
        $params[] = $this->country;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (!empty($updateParts)) {
        $query = "UPDATE $this->table_name SET " . implode(', ', $updateParts) . " WHERE address_id = ?";
        $params[] = $this->address_id;
        $types .= 'i'; // Assuming the primary key is an integer
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->affected_rows;
    } else {
        throw new Exception('No fields to update');
    }
}
public function delete() {
    $query = "DELETE FROM $this->table_name WHERE address_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->address_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

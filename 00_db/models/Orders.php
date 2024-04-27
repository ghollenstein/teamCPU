<?php
class Orders extends BaseModel  {
protected $conn;
protected $table_name = "orders";

public $order_id;
public $address_id_delivery;
public $address_id_billing;
public $user_id;
public $order_date;
public $total_price;
public $status;
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
    $query = "INSERT INTO $this->table_name (address_id_delivery, address_id_billing, user_id, order_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssss', $this->address_id_delivery, $this->address_id_billing, $this->user_id, $this->order_date, $this->total_price, $this->status);
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
    if (isset($this->address_id_delivery)) {
        $updateParts[] = 'address_id_delivery = ?';
        $params[] = $this->address_id_delivery;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->address_id_billing)) {
        $updateParts[] = 'address_id_billing = ?';
        $params[] = $this->address_id_billing;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->user_id)) {
        $updateParts[] = 'user_id = ?';
        $params[] = $this->user_id;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->order_date)) {
        $updateParts[] = 'order_date = ?';
        $params[] = $this->order_date;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->total_price)) {
        $updateParts[] = 'total_price = ?';
        $params[] = $this->total_price;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->status)) {
        $updateParts[] = 'status = ?';
        $params[] = $this->status;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (!empty($updateParts)) {
        $query = "UPDATE $this->table_name SET " . implode(', ', $updateParts) . " WHERE order_id = ?";
        $params[] = $this->order_id;
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
    $query = "DELETE FROM $this->table_name WHERE order_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->order_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

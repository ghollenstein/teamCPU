<?php
class Order_items extends BaseModel  {
protected $conn;
protected $table_name = "order_items";

public $order_id;
public $product_id;
public $quantity;
public $price;
public $tax;
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
    if (!isset($this->order_id) || $this->order_id === '') {
        $missingFields[] = 'order_id';
    }
    if (!isset($this->product_id) || $this->product_id === '') {
        $missingFields[] = 'product_id';
    }
    if (count($missingFields) > 0) {
        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
    }
    return true;
}

public function create() {
    $this->validate();
    $query = "INSERT INTO $this->table_name (order_id, product_id, quantity, price, tax) VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssss', $this->order_id, $this->product_id, $this->quantity, $this->price, $this->tax);
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
    if (isset($this->order_id)) {
        $updateParts[] = 'order_id = ?';
        $params[] = $this->order_id;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->product_id)) {
        $updateParts[] = 'product_id = ?';
        $params[] = $this->product_id;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->quantity)) {
        $updateParts[] = 'quantity = ?';
        $params[] = $this->quantity;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->price)) {
        $updateParts[] = 'price = ?';
        $params[] = $this->price;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->tax)) {
        $updateParts[] = 'tax = ?';
        $params[] = $this->tax;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (!empty($updateParts)) {
        $query = "UPDATE $this->table_name SET " . implode(', ', $updateParts) . " WHERE product_id = ?";
        $params[] = $this->product_id;
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
    $query = "DELETE FROM $this->table_name WHERE product_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->product_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

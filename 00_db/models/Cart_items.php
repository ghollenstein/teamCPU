<?php
class Cart_items extends BaseModel  {
protected $conn;
protected $table_name = "cart_items";

public $cart_id;
public $product_id;
public $quantity;
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
    if (!isset($this->cart_id) || $this->cart_id === '') {
        $missingFields[] = 'cart_id';
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
    $query = "INSERT INTO $this->table_name (cart_id, product_id, quantity, createdDate, createdUser, modDate, modUser, lockstate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssssss', $this->cart_id, $this->product_id, $this->quantity, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate);
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
    $query = "UPDATE $this->table_name SET cart_id = ?, product_id = ?, quantity = ?, createdDate = ?, createdUser = ?, modDate = ?, modUser = ?, lockstate = ? WHERE product_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssssss', $this->cart_id, $this->product_id, $this->quantity, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate, $this->product_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE product_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->product_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

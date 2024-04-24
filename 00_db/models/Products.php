<?php
class Products {
private $conn;
private $table_name = "products";

public $product_id;
public $name;
public $description;
public $image;
public $tax;
public $price;
public $stock;
public $createdDate;
public $createdUser;
public $modDate;
public $modUser;
public $lockstate;

public function __construct($conn) {
    $this->conn = $conn;
}

private function validate() {
    $missingFields = [];
    if (!isset($this->name) || $this->name === '') {
        $missingFields[] = 'name';
    }
    if (!isset($this->tax) || $this->tax === '') {
        $missingFields[] = 'tax';
    }
    if (!isset($this->price) || $this->price === '') {
        $missingFields[] = 'price';
    }
    if (!isset($this->stock) || $this->stock === '') {
        $missingFields[] = 'stock';
    }
    if (count($missingFields) > 0) {
        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
    }
    return true;
}

public function create() {
    $this->validate();
    $query = "INSERT INTO $this->table_name (name, description, image, tax, price, stock, createdDate, createdUser, modDate, modUser, lockstate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssssssss', $this->name, $this->description, $this->image, $this->tax, $this->price, $this->stock, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate);
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
    $query = "UPDATE $this->table_name SET name = ?, description = ?, image = ?, tax = ?, price = ?, stock = ?, createdDate = ?, createdUser = ?, modDate = ?, modUser = ?, lockstate = ? WHERE product_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssssssssss', $this->name, $this->description, $this->image, $this->tax, $this->price, $this->stock, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate, $this->product_id);
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

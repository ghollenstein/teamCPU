<?php
class Order_items extends BaseModel  {
protected $conn;
protected $table_name = "order_items";

public $item_id;
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
    $this->item_id= $stmt->insert_id;
    return $stmt->insert_id;
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

public function get($id=0) {
    $result = $this->read("item_id=?", [$id], 'i');
    if(isset($result[0])) $this->mapData($result[0]);
    return $result;
}

public function update() {
    $this->validate();
$lockstate = $this->lockstate ? $this->lockstate : 0;
$modUser = $this->modUser ? $this->modUser : 0;
    $query = "UPDATE $this->table_name SET order_id = ?, product_id = ?, quantity = ?, price = ?, tax = ?, modDate = NOW(), lockstate = ?, modUser = ? WHERE item_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssssss', $this->order_id, $this->product_id, $this->quantity, $this->price, $this->tax, $lockstate , $modUser, $this->item_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE item_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->item_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

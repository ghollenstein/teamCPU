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
    $this->order_id= $stmt->insert_id;
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
    $result = $this->read("order_id=?", [$id], 'i');
    if(isset($result[0])) $this->mapData($result[0]);
    return $result;
}

public function update() {
    $this->validate();
$lockstate = $this->lockstate ? $this->lockstate : 0;
$modUser = $this->modUser ? $this->modUser : 0;
    $query = "UPDATE $this->table_name SET address_id_delivery = ?, address_id_billing = ?, user_id = ?, order_date = ?, total_price = ?, status = ?, modDate = NOW(), lockstate = ?, modUser = ? WHERE order_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssssssss', $this->address_id_delivery, $this->address_id_billing, $this->user_id, $this->order_date, $this->total_price, $this->status, $lockstate , $modUser, $this->order_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE order_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->order_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

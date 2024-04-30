<?php
class Product_categories extends BaseModel  {
protected $conn;
protected $table_name = "product_categories";

public $product_category_id;
public $product_id;
public $category_id;
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
    $query = "INSERT INTO $this->table_name (product_id, category_id) VALUES (?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ss', $this->product_id, $this->category_id);
    $stmt->execute();
    $this->product_category_id= $stmt->insert_id;
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
    $result = $this->read("product_category_id=?", [$id], 'i');
    if(isset($result[0])) $this->mapData($result[0]);
    return $result;
}

public function update() {
    $this->validate();
$lockstate = $this->lockstate ? $this->lockstate : 0;
$modUser = $this->modUser ? $this->modUser : 0;
    $query = "UPDATE $this->table_name SET product_id = ?, category_id = ?, modDate = NOW(), lockstate = ?, modUser = ? WHERE product_category_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sssss', $this->product_id, $this->category_id, $lockstate , $modUser, $this->product_category_id);
    $stmt->execute();
    return $stmt->affected_rows;
}

public function delete() {
    $query = "DELETE FROM $this->table_name WHERE product_category_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $this->product_category_id);
    $stmt->execute();
    return $stmt->affected_rows;
}
}

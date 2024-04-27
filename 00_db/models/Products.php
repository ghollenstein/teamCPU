<?php
class Products extends BaseModel  {
protected $conn;
protected $table_name = "products";

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
    parent::__construct($conn, $this->table_name);
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
    $query = "INSERT INTO $this->table_name (name, description, image, tax, price, stock) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssss', $this->name, $this->description, $this->image, $this->tax, $this->price, $this->stock);
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
    if (isset($this->name)) {
        $updateParts[] = 'name = ?';
        $params[] = $this->name;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->description)) {
        $updateParts[] = 'description = ?';
        $params[] = $this->description;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->image)) {
        $updateParts[] = 'image = ?';
        $params[] = $this->image;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->tax)) {
        $updateParts[] = 'tax = ?';
        $params[] = $this->tax;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->price)) {
        $updateParts[] = 'price = ?';
        $params[] = $this->price;
        $types .= 's'; // Adjust based on the actual expected type
    }
    if (isset($this->stock)) {
        $updateParts[] = 'stock = ?';
        $params[] = $this->stock;
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

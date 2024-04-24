<?php
class Carts
{
    private $conn;
    private $table_name = "carts";

    public $cart_id;
    public $user_id;
    public $createdDate;
    public $createdUser;
    public $modDate;
    public $modUser;
    public $lockstate;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function validate()
    {
        $missingFields = [];
        if (count($missingFields) > 0) {
            throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
        }
        return true;
    }

    public function create()
    {
        $this->validate();
        $query = "INSERT INTO $this->table_name (user_id, createdDate, createdUser, modDate, modUser, lockstate) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssss', $this->user_id, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function readAll()
    {
        $query = "SELECT * FROM $this->table_name";
        $result = $this->conn->query($query);
        return $result;
    }

    public function read($where = "", $params = [], $types = "")
    {
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

    public function update()
    {
        $this->validate();
        $query = "UPDATE $this->table_name SET user_id = ?, createdDate = ?, createdUser = ?, modDate = ?, modUser = ?, lockstate = ? WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssssss', $this->user_id, $this->createdDate, $this->createdUser, $this->modDate, $this->modUser, $this->lockstate, $this->cart_id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function delete()
    {
        $query = "DELETE FROM $this->table_name WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $this->cart_id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

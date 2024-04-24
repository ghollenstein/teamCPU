<?php
class BaseModel
{
    protected $conn;
    protected $table;

    public function __construct($conn, $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    public function create()
    {
        // Generic create logic
    }

    public function read($where = '', $params = [], $types = '')
    {
        // Generic read logic
    }

    public function readAll()
    {
        // Generic read logic
    }

    public function update()
    {
        // Generic update logic
    }

    public function delete()
    {
        // Generic delete logic
    }
    private function validate($id)
    {
        // Generic delete logic
    }
}

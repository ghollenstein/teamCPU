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


    /**
     * Mappt die Daten aus einem Array dynamisch auf Modelleigenschaften.
     * @param array $data Assoziatives Array mit Schlüsseln, die den Eigenschaftsnamen entsprechen.
     */
    public function mapData(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}

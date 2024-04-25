<?php
class Sql
{
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function executeSQL($sql, $params = [], $types = '', $fetch = false)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if ($fetch) {
            $result = $stmt->get_result();
            $fieldinfo = $result->fetch_fields();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return [
                'fieldinfo' => $fieldinfo,
                'data' => $data
            ];
        } else {
            return $stmt->affected_rows;
        }
    }

    public function convertDbResultToJson($result)
    {
        $data = [];
        // Get column metadata from the result set
        $fieldinfo = $result['fieldinfo'];

        foreach ($result['data'] as $row) {
            $formatted = [];
            $formatted = [];
            foreach ($row as $key => $value) {
                $type = null;
                // Loop through field info to match the column name
                foreach ($fieldinfo as $info) {
                    if ($info->name == $key) {
                        $type = $info->type;
                        break;
                    }
                }
                // Based on the type, cast the value
                switch ($type) {
                    case MYSQLI_TYPE_DECIMAL:
                    case MYSQLI_TYPE_NEWDECIMAL:
                    case MYSQLI_TYPE_FLOAT:
                    case MYSQLI_TYPE_DOUBLE:
                        $formatted[$key] = (float) $value;
                        break;
                    case MYSQLI_TYPE_TINY:
                    case MYSQLI_TYPE_SHORT:
                    case MYSQLI_TYPE_LONG:
                    case MYSQLI_TYPE_INT24:
                    case MYSQLI_TYPE_LONGLONG:
                    case MYSQLI_TYPE_YEAR:
                        $formatted[$key] = (int) $value;
                        break;
                    default:
                        $formatted[$key] = $value; // Default case leaves it as string
                }
            }
            $data[] = $formatted;
        }
        return $data;
    }
}

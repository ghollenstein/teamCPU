<?php
require_once '../class/Database.php';

$dbInstance = Database::getInstance();
$conn = $dbInstance->getConnection();
$modelDir = "./models/";

if (!is_dir($modelDir)) {
    mkdir($modelDir);
}

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array(MYSQLI_NUM)) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    $result = $conn->query("DESCRIBE $table");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    $primaryKey = "";
    $nonAutoIncrementFields = [];
    $requiredFields = [];

    $classContent = "<?php\nclass " . ucfirst($table) . " extends BaseModel  {\n";
    $classContent .= "protected \$conn;\nprotected \$table_name = \"$table\";\n\n";

    foreach ($columns as $column) {
        $classContent .= "public \${$column['Field']};\n";
        if ($column['Key'] === 'PRI') {
            $primaryKey = $column['Field'];
        }
        if ($column['Extra'] !== 'auto_increment' && $column['Default'] === null && $column['Field'] !== 'modDate') {
            $nonAutoIncrementFields[] = $column;
        }
        if ($column['Null'] === 'NO' && $column['Default'] === null && $column['Extra'] !== 'auto_increment') {
            $requiredFields[] = $column['Field'];
        }
    }

    $classContent .= "\npublic function __construct(\$conn) {\n    parent::__construct(\$conn, \$this->table_name);\n}\n\n";

    // Validation method
    $classContent .= "private function validate() {\n";
    $classContent .= "    \$missingFields = [];\n";
    foreach ($requiredFields as $field) {
        $classContent .= "    if (!isset(\$this->$field) || \$this->$field === '') {\n        \$missingFields[] = '$field';\n    }\n";
    }
    $classContent .= "    if (count(\$missingFields) > 0) {\n        throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', \$missingFields));\n    }\n";
    $classContent .= "    return true;\n}\n\n";

    // Create
    // Erzeugen der INSERT-Anweisung
    $insertFields = array_map(function ($col) {
        return $col['Field'];
    }, $nonAutoIncrementFields);
    $insertPlaceholders = array_map(function ($col) {
        return '?';
    }, $nonAutoIncrementFields);
    $classContent .= "public function create() {\n";
    $classContent .= "    \$this->validate();\n";
    $classContent .= "    \$query = \"INSERT INTO \$this->table_name (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $insertPlaceholders) . ")\";\n";
    $classContent .= "    \$stmt = \$this->conn->prepare(\$query);\n";
    $fieldValues = array_map(function ($col) {
        return '$this->' . $col['Field'];
    }, $nonAutoIncrementFields);
    $classContent .= "    \$stmt->bind_param('" . str_repeat("s", count($nonAutoIncrementFields)) . "', " . implode(', ', $fieldValues) . ");\n";
    $classContent .= "    \$stmt->execute();\n";
    $classContent .= "    return \$stmt->affected_rows;\n";
    $classContent .= "}\n\n";

    // Read
    $classContent .= "public function readAll() {\n";
    $classContent .= "    \$query = \"SELECT * FROM \$this->table_name\";\n";
    $classContent .= "    \$result = \$this->conn->query(\$query);\n";
    $classContent .= "    return \$result;\n";
    $classContent .= "}\n\n";

    // Modified read method
    $classContent .= "public function read(\$where = \"\", \$params = [], \$types = \"\") {\n";
    $classContent .= "    if (!empty(\$where)) {\n";
    $classContent .= "        \$query = \"SELECT * FROM \$this->table_name WHERE \$where\";\n";
    $classContent .= "    } else {\n";
    $classContent .= "        \$query = \"SELECT * FROM \$this->table_name\";\n";
    $classContent .= "    }\n";
    $classContent .= "    \$stmt = \$this->conn->prepare(\$query);\n";
    $classContent .= "    if (!empty(\$params) && !empty(\$types)) {\n";
    $classContent .= "        \$stmt->bind_param(\$types, ...\$params);\n";
    $classContent .= "    }\n";
    $classContent .= "    \$stmt->execute();\n";
    $classContent .= "    \$result = \$stmt->get_result();\n";
    $classContent .= "    return \$result->fetch_all(MYSQLI_ASSOC);\n";
    $classContent .= "}\n\n";

    // Erzeugen der UPDATE-Anweisung, ohne modDate zu setzen
    // Update method
$classContent .= "public function update() {\n";
    $classContent .= "    \$params = [];\n";
    $classContent .= "    \$types = '';\n";
    $classContent .= "    \$updateParts = [];\n";
    
    foreach ($nonAutoIncrementFields as $col) {
        if ($col['Field'] !== 'modDate') { // Exclude modDate from being manually updated
            $classContent .= "    if (isset(\$this->{$col['Field']})) {\n";
            $classContent .= "        \$updateParts[] = '{$col['Field']} = ?';\n";
            $classContent .= "        \$params[] = \$this->{$col['Field']};\n";
            $classContent .= "        \$types .= 's'; // Adjust based on the actual expected type\n";
            $classContent .= "    }\n";
        }
    }
    
    $classContent .= "    if (!empty(\$updateParts)) {\n";
    $classContent .= "        \$query = \"UPDATE \$this->table_name SET \" . implode(', ', \$updateParts) . \" WHERE $primaryKey = ?\";\n";
    $classContent .= "        \$params[] = \$this->$primaryKey;\n";
    $classContent .= "        \$types .= 'i'; // Assuming the primary key is an integer\n";
    $classContent .= "        \$stmt = \$this->conn->prepare(\$query);\n";
    $classContent .= "        \$stmt->bind_param(\$types, ...\$params);\n";
    $classContent .= "        \$stmt->execute();\n";
    $classContent .= "        return \$stmt->affected_rows;\n";
    $classContent .= "    } else {\n";
    $classContent .= "        throw new Exception('No fields to update');\n";
    $classContent .= "    }\n";
    $classContent .= "}\n";
    


    // Delete
    $classContent .= "public function delete() {\n";
    $classContent .= "    \$query = \"DELETE FROM \$this->table_name WHERE $primaryKey = ?\";\n";
    $classContent .= "    \$stmt = \$this->conn->prepare(\$query);\n";
    $classContent .= "    \$stmt->bind_param('s', \$this->$primaryKey);\n";
    $classContent .= "    \$stmt->execute();\n";
    $classContent .= "    return \$stmt->affected_rows;\n";
    $classContent .= "}\n}\n";

    // Now, output the class content to a file
    file_put_contents($modelDir . ucfirst($table) . ".php", $classContent);
}

echo "Models generated successfully.\n";

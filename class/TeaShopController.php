<?php

class TeaShopController
{
    private $dbConnection;

    public function __construct()
    {
        $this->dbConnection = Database::getInstance()->getConnection();
    }

    public function handleRequest($entity, $action, $data)
    {
        $className = ucfirst($entity); // Convert entity to ClassName
        if (!class_exists($className)) {
            throw new Exception("No model found for entity: " . $entity);
        }

        $model = new $className($this->dbConnection, $data);
        if (!method_exists($model, $action)) {
            throw new Exception("Action not recognized for entity: " . $entity);
        }

        return $model->$action($data);
    }
}

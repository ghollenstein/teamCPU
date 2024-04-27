<?php
class Account
{
    protected $conn;
    protected $userModel;
    protected $addressModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->userModel = new Users($conn);
        $this->addressModel = new Addresses($conn);
    }

    public function registerUser($userData)
    {
        $this->conn->begin_transaction();
        try {
            // Setzen der Benutzerdaten
            $this->userModel->mapData($userData);
            $this->userModel->password = password_hash($userData['password'], PASSWORD_DEFAULT); // Verschlüsseln des Passworts

            // Benutzer erstellen
            if ($this->userModel->create() === 0) {
                throw new Exception("Benutzer Anlage fehlgeschlagen!");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getUserData()
    {
        $userId = $_SESSION['user_id'];
        try {
            // Abfragen der Benutzerdaten basierend auf der user_id
            $user = $this->userModel->read("user_id = ?", [$userId], "i");
            return $user;
        } catch (Exception $e) {
            throw new Exception("Benutzerdaten konnten nicht abgerufen werden: " . $e->getMessage());
        }
    }

    public function updateUser($userData)
    {
        // Start transaction
        $this->conn->begin_transaction();
        try {
            // Load current user data
            $currentUser = $this->getUserData()[0];
            if (!$currentUser) {
                throw new Exception("Current user data could not be loaded.");
            }
            $userData['modUser'] = $_SESSION['user_id'];

            // Map current user data to model
            $this->userModel->mapData($currentUser);

            // Map new user data, potentially overwriting the old data
            $this->userModel->mapData($userData);

            // Password check and update
            if (!empty($userData['password1']) && !empty($userData['password2'])) {
                if ($userData['password1'] !== $userData['password2']) {
                    throw new Exception("Die Passwörter stimmen nicht überein.");
                }
                $this->userModel->password = password_hash($userData['password1'], PASSWORD_DEFAULT);
            }

            // Attempt to update the user data in the database
            if ($this->userModel->update() === 0) {
                throw new Exception("Benutzeraktualisierung fehlgeschlagen!");
            }

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}

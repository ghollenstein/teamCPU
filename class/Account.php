<?php
class Account
{
    protected $conn;
    protected $userModel;
    protected $addressModel;
    protected $controller;

    public function __construct($conn, $controller)
    {
        $this->conn = $conn;
        $this->controller = $controller;
        $this->userModel = new Users($conn);
        $this->addressModel = new Addresses($conn);
    }

    public function registerUser($userData)
    {
        $this->conn->begin_transaction();
        try {
            // Validierung der E-Mail-Adresse
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Ungültige E-Mail-Adresse!");
            }

            // Validierung des Passworts
            if (strlen($userData['password']) < 8) {
                throw new Exception("Das Passwort muss mindestens 8 Zeichen lang sein!");
            }

            if (
                !preg_match('/[A-Z]/', $userData['password']) ||
                !preg_match('/[a-z]/', $userData['password']) ||
                !preg_match('/[0-9]/', $userData['password']) ||
                !preg_match('/[\^£$%&*()}{@#~?><>,|=_+!-]/', $userData['password'])
            ) {
                throw new Exception("Das Passwort muss Großbuchstaben, Kleinbuchstaben, Zahlen und Sonderzeichen enthalten!");
            }

            // Prüfen, ob die Passwörter übereinstimmen
            if ($userData['password'] !== $userData['reg_password_2']) {
                throw new Exception("Die Passwörter stimmen nicht überein!");
            }

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

    public function getOrders()
    {
        $db = new Sql($this->conn);

        $sql = "select o.createdDate, o.order_id , o.total_price , o.status,p.name , oi.price as netto, (oi.tax/100+1)*oi.price as brutto, oi.quantity  from orders o
        inner join order_items oi on oi.order_id =o.order_id 
        left join products p on p.product_id =oi.product_id  
        where o.user_id =? and o.lockstate =0 ";

        return $db->executeSQL($sql, [$_SESSION['user_id']], 'i', true);
    }
}

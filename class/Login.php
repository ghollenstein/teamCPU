<?php

class Login
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function authenticate($username, $password)
    {
        $stmt = $this->dbConnection->prepare("SELECT user_id,email,firstname, lastname, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $this->setSession($user);
                return true;
            }
        }
        return false;
    }

    private function setSession($user)
    {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['logged_in'] = true;  // Setzen des Login-Status
    }

    public function logout()
    {
        session_unset();  // Entfernt alle Session-Variablen
        session_destroy();
        header("Location: index.php?page=meinkonto");
    }

    public function isUserLoggedIn()
    {
        return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'];
    }
}

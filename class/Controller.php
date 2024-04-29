<?php
class Controller
{
    public $params = [];
    private $db;
    private $session;
    private $debug = false;
    public $login;
    public $account;
    public $order;

    public function __construct($debug = false)
    {
        $exceptionHandler = new ExceptionHandler();
        $exceptionHandler->register();
        $this->debug = $debug;
        $this->initParams();
        $this->session = $this->initSession();
        $this->db = Database::getInstance()->getConnection();
        $this->login = new Login($this->db);
        $this->account = new Account($this->db, $this);
        $this->order = new Order($this->db, $this);
    }


    private function initParams()
    {
        $this->params = [
            'POST' => filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS),
            'GET' => filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS),
            'Method' => filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW),
            'URI' => filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL)
        ];
    }

    private function initSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }

    public function addFeedback($message, $type = 'info')
    {
        if (!isset($_SESSION['feedback'])) {
            $_SESSION['feedback'] = [];
        }
        $_SESSION['feedback'][] = ['message' => $message, 'type' => $type];
    }

    public function getFeedback()
    {
        $feedback = $_SESSION['feedback'] ?? [];
        unset($_SESSION['feedback']);
        return $feedback;
    }

    public function getOrderHistory()
    {
        return $this->account->getOrders();
    }
    public function getAddresses()
    {
        return $this->account->getAddresses();
    }

    public function handleActions()
    {
        $action = $this->params['POST']['action'] ?? $this->params['GET']['action'] ?? null;
        $method = $this->params['Method'];

        switch ($action) {
            case 'login':
                if ($method === 'POST') $this->handleLogin();
                break;
            case 'logout':
                if ($method === 'GET') $this->login->logout();
                break;
            case 'register':
                if ($method === 'POST') $this->handleRegistration();
                break;
            case 'accountUpdate':
                if ($method === 'POST') $this->handleAccountUpdate();
                break;
            case 'processCheckout':
                if ($method === 'POST') $this->handleOrder();
                break;
        }
    }

    private function handleOrder()
    {

        try {
            $this->order->saveOrder($this->params);
            $this->addFeedback("Bestellung erfolgreich erstellt!", "success");
            header("Location: index.php?page=meinkonto");
            exit;
        } catch (Exception $e) {
            $this->addFeedback("Fehler: " . $e->getMessage(), "error");
        }
    }

    private function handleAccountUpdate()
    {
        try {
            $this->account->updateUser($this->params['POST']);
            $this->addFeedback("Kontodaten erfolgreich aktualisiert.", "success");
            header("Location: index.php?page=meinkonto");
            exit;
        } catch (Exception $e) {
            $this->addFeedback("Aktualisierung fehlgeschlagen: " . $e->getMessage(), "error");
        }
    }
    private function handleRegistration()
    {
        $email = $this->params['POST']['email'] ?? '';
        $password = $this->params['POST']['password'] ?? '';


        try {
            $this->account->registerUser($this->params['POST']);
            $this->login->authenticate($email, $password);
            $this->addFeedback("Registrierung erfolgreich, Sie sind jetzt eingeloggt.", "success");
            header("Location: index.php?page=meinkonto");
            exit;
        } catch (Exception $e) {
            $this->addFeedback("Registrierung fehlgeschlagen: " . $e->getMessage(), "error");
        }
    }

    private function handleLogin()
    {
        $username = $this->params['POST']['username'] ?? null;
        $password = $this->params['POST']['password'] ?? null;
        if ($username && $password && $this->login->authenticate($username, $password)) {
            $this->addFeedback("Login erfolgreich.", "success");
        } else {
            $this->addFeedback("Login fehlgeschlagen.", "error");
        }
    }

    public function servePage()
    {
        $page = $this->params['GET']['page'] ?? 'start';
        $filePath = "content/{$page}.php";

        if (file_exists($filePath)) {
            include $filePath;
        } else {
            include 'content/error404.php';
        }
    }

    private function debugOutput()
    {
        if ($this->debug) {
            echo "<pre>Params: ";
            print_r(htmlentities(print_r($this->params, true)));
            echo "</pre>";
        }
    }

    public function getPostVar($name, $default = "")
    {

        if (isset($_POST[$name])) {
            echo htmlspecialchars($_POST[$name]);
        } else {
            echo $default;
        }
    }
}

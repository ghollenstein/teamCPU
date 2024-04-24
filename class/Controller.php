<?php
class Controller
{
    private $params = [];
    private $db;
    private $session;
    private $debug = false;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
        $this->initParams();
        $this->session = $this->initSession();
        $this->db = Database::getInstance()->getConnection();
    }

    private function initParams()
    {
        // Use filter_input_array to sanitize and validate input
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

    public function route()
    {
        $page = $this->params['GET']['page'] ?? 'start'; // Default to 'start' if not set
        $filePath = "content/{$page}.php";

        if (file_exists($filePath)) {
            include $filePath;
        } else {
            include 'content/error404.php';
        }

        $this->debugOutput();
    }

    private function debugOutput()
    {
        if ($this->debug) {
            echo "<pre>Params: ";
            print_r(htmlentities(print_r($this->params, true)));
            echo "</pre>";
        }
    }
}

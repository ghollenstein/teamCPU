<?php
class ExceptionHandler
{

    /**
     * Registriert den globalen Exception Handler.
     */
    public function register()
    {
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Unregistriert den globalen Exception Handler.
     */
    public function unregister()
    {
        restore_exception_handler();
    }

    /**
     * Bearbeitet Exceptions und stellt sie benutzerfreundlich dar, inklusive eines Stacktraces.
     *
     * @param Exception $exception Die zu bearbeitende Exception.
     */
    public function handleException($exception)
    {
        http_response_code(500);
        echo "<div style='background-color: #ffcccc; padding: 10px; border: 1px solid red; font-family: sans-serif; font-size:0.9em'>";
        echo "<strong>Exception:</strong> " . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "<br><br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine();
        echo "<pre><strong>Stack Trace:</strong><br>" . htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";

        // Loggen der Exception
        error_log("Unhandled Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine(), 0);

        // Hier könnte weiterer Code folgen, um die Exception in einer Datenbank zu speichern oder Entwickler zu benachrichtigen.
    }
}

<?php 


// Creo la classe Logger con il pattern Singleton
// così mi assicuro che non venga creata un'istanza
// di Logger ogni volta

class Logger {
    private static $instance;
    private $log_file;

    private function __construct($log_file) {
        $this->log_file = $log_file;
    }

    public static function getInstance($log_file = "logs/log.txt") {
        if (!self::$instance) {
            self::$instance = new self($log_file);
        }
        return self::$instance;
    }

    public function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $log_line = "[$timestamp] [$level]: $message" . PHP_EOL;
        
        file_put_contents($this->log_file, $log_line, FILE_APPEND);
    }
}
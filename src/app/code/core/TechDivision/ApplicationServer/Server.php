<?php

namespace TechDivision\ApplicationServer;

class Server {

    const MAX_HANDLERS = 1;

    protected static $_instance = null;
    
    protected $_handlers = array();

    protected function __construct() {
        
        for ($i = 0; $i < self::MAX_HANDLERS; $i++) {
            $this->handlers[$i] = new Handler("handler-$i", $this);
            $this->handlers[$i]->start(true);
            error_log('Successfully started handler ' . $this->handlers[$i]->getName());
        }
    }

    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new Server();
        }
        return self::$_instance;
    }

    public static function start() {
        while (true) {
            sleep(1);
        }
    }

}
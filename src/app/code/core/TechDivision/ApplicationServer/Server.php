<?php

namespace TechDivision\ApplicationServer;

use TechDivision\PersistenceContainer\Interfaces\Handler;
use Zend\Config\Factory;

class Server {

    protected static $_instance = null;
    
    protected $_handlers = array();
    
    protected $_config = null;

    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new Server();
        }
        return self::$_instance;
    }

    public function start() {
        
        $this->initConfiguration();
        $this->initHandlers();
        
        while (true) {
            sleep(1);
        }
    }
    
    public function addHandler(Handler $handler) {
        return $this->_handlers[get_class($handler)] = $handler;
    }
    
    public function initConfiguration() {
        $this->_config = Factory::fromFile('cfg/appserver.xml', true);
    }
    
    public function initHandlers() {

        foreach ($this->getConfig()->handlers as $handler) {
            
            $params = $handler->params->toArray();
            
            $handlerInstance = $this->newInstance($handler->type, $params);
            
            $this->addHandler($handlerInstance)->start(true);
            
            error_log("Successfully started handler {$handler->type}");
        }        
    }
    
    public function getConfig() {
        return $this->_config;
    }
    
    public function newInstance($className, $params) {    
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($params);
    }
}
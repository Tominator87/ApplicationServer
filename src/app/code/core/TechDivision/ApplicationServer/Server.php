<?php

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\ContainerThread;

class Server {
    
    public function __construct() {
        $this->configurations = array('/appserver/containers/container');
    }
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
        
        foreach ($this->configurations as $configuration) {
            
            $thread = new ContainerThread($configuration);
            $thread->start();

            error_log("Successfully started container");
        }
    }
    
    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     * 
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array()) { 
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}
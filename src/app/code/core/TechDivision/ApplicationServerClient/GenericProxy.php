<?php

namespace TechDivision\ApplicationServerClient;

use TechDivision\ApplicationServerClient\Client;

class GenericProxy {
    
    protected $_className = null;
    
    protected $_client = null;
    
    public function __construct($className) {
        $this->_className = $className;
        $this->_client = new Client();
    }
    
    public function __call($method, $params) {
        return $this->_client->call($this->_className, $method, $params);
    }
}
<?php

namespace TechDivision\ApplicationServer;

class InitialContext
{
    
    protected $_attributes = array();
    
    public function setAttribute($key, $value) {
        $this->_attributes[$key] = $value;
    }
    
    public function getAttribute($key) {
        if (array_key_exists($key, $this->_attributes)) {
            return $this->_attributes[$key];
        }
    }
    
    public function newReflectionClass($className) {
        return new \ReflectionClass($className);
    }
    
    public function newInstance($className, array $args = array()) { 
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}
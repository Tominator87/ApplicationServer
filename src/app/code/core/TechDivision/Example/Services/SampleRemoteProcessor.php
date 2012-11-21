<?php

namespace TechDivision\Example\Services;

// use TechDivision\Example\Entities\Sample;
use TechDivision\ApplicationServerClient\GenericProxy;

class SampleRemoteProcessor extends GenericProxy {
    
    public function __construct() {
        parent::__construct('TechDivision\Example\Services\SampleProcessor');
    }
    
    /*
    public function load($id) {
        return $this->_client->call($this->_class, 'load', array($id));
    }
    
    public function persist(Sample $entity) {
        return $this->_client->call($this->_class, 'persist', array($entity));
    }
    
    public function findAll() {
        return $this->_client->call($this->_class, 'findAll', array());
    }
    */
}
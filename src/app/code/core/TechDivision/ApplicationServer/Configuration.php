<?php

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerConfiguration;

class Configuration implements ContainerConfiguration {
    
    protected $workerNumber = 0;
    
    protected $address = '0.0.0.0';
    
    protected $port = 8585;
    
    public function __construct($workerNumber = 1, $address = '0.0.0.0', $port = 8585) {
        $this->setWorkerNumber($workerNumber);
        $this->setAddress($address);
        $this->setPort($port);
    }
    
    public function equals(ContainerConfiguration $configuration) {
         return $this === $configuration;
    }
    
    public function setWorkerNumber($workerNumber) {
        $this->workerNumber = $workerNumber;
    }
    
    public function getWorkerNumber() {
        return $this->workerNumber;
    }
    
    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setPort($port) {
        $this->port = $port;
    }
    
    public function getPort() {
        return $this->port;
    }
}
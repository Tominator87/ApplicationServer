<?php

namespace TechDivision\ApplicationServerClient;

class Client {
    
    protected $_socket = null;
    
    public function __construct() {
        $this->_socket = new \Net_Socket();
        $this->_socket->setBlocking(false);
        $this->_socket->connect('192.168.250.128', 8585, false, 1);
    }
    
    public function __destruct() {
        $this->getSocket()->disconnect();
    }
    
    public function getSocket() {
        return $this->_socket;
    }
    
    public function call($class, $method, array $params) {
        // create the remote method
        $remoteMethod = serialize(array($class, $method, $params));
        // serialize the remote method and write it to the socket
        $written = $this->getSocket()->writeLine($remoteMethod);
        // ckeck if the remote method has successfully been written to the socket
        if (\PEAR::isError($written)) {
            throw new \Exception($written->getMessage());
        }
        // read the reploy
        $serialized = $this->getSocket()->readLine();
        // unserialize the response
        $response = unserialize($serialized);
        // if an exception returns, throw it again
        if ($response instanceof \Exception) {
            throw $response;
        }
        // return the data
        return $response;
    }
}
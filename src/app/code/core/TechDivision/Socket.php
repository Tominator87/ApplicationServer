<?php

/**
 * TechDivision_Socket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision;

/**
 * The socket implementation.
 *
 * @package     TechDivision
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Socket {
    
    public $resource = null;
    
    protected $address = '127.0.0.1';
    
    protected $port = 0;

    /**
     * A maximum of backlog incoming connections will be queued for processing.
     *
     * If a connection request arrives with the queue full the client may receive an error with an indication
     * of ECONNREFUSED, or, if the underlying protocol supports retransmission, the request may be ignored
     * so that retries may succeed.
     *
     * @var int
     */
    protected $backlog = 100;
    
    public function __construct($resource = null) {
        $this->resource = $resource;
    }
    
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }
    
    public function getPort() {
        return $this->port;
    }
    
    public function setBacklog($backlog) {
        $this->backlog = $backlog;
        return $this;
    }
    
    public function getBacklog() {
        return $this->backlog;
    }
    
    public function create() {
        
        if (($this->resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \Exception('SOCKET: Couldn\'t create socket: [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
    }
    
    public function setBlock() {
        
        if (socket_set_block($this->resource) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \Exception('SOCKET: Couldn\'t set socket blocking: [' . $errorcode . '] ' . $errormsg);
            
        }
        
        return $this;
    }
    
    public function setNoBlock() {
        
        if (socket_set_nonblock($this->resource) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \Exception('SOCKET: Couldn\'t set socket no blocking: [' . $errorcode . '] ' . $errormsg);
            
        }
        
        return $this;
    }
    
    public function setReuseAddr() {
 
        if (socket_set_option($this->resource, SOL_SOCKET, SO_REUSEADDR, 1) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            socket_close($this->resource);

            throw new \Exception('SOCKET: Could not set option \'SO_REUSEADDR\' on socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
    }
    
    public function setReceiveTimeout(array $timeout = array("sec" => 0, "usec" => 100)) {
        
        if (socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, $timeout) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            socket_close($this->resource);

            throw new \Exception('SOCKET: Could not set option \'SO_RCVTIMEO\' on socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
        
    }
    
    public function setLinger(array $linger = array('l_onoff' => 1, 'l_linger' => 1)) {
        
        if (socket_set_option($this->resource, SOL_SOCKET, SO_LINGER, $linger) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            socket_close($this->resource);

            throw new \Exception('SOCKET: Could not set option \'SO_LINGER\' on socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
    }
    
    public function close() {
        
        if (socket_close($this->resource) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't shutdown socket : [$errorcode] $errormsg");
            
        }
        
        return $this;
    }
    
    public function shutdown() {
        
        if (socket_shutdown($this->resource) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't shutdown socket : [$errorcode] $errormsg");
        }
        
        return $this;
    }
    
    public function connect() {
        
        if (socket_connect($this->resource, $this->getAddress(), $this->getPort()) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't connect socket : [$errorcode] $errormsg");
        }
        
        return $this;
    }
    
    public function send($data) {
        
        $bytesSend = socket_write($this->resource, $data);
        
        if ($bytesSend === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't send data : [$errorcode] $errormsg");
        }
        
        return $bytesSend;
    }
    
    public function bind() {
        
        // bind the source address
        if (socket_bind($this->resource, $this->getAddress(), $this->getPort()) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            socket_close($this->resource);
            
            throw new \Exception("SOCKET: Could not bind socket : [$errorcode] $errormsg");
        }
        
        return $this;
    }
    
    public function listen() {

        if (socket_listen($this->resource, $this->getBacklog()) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            socket_close($this->resource);

            throw new \Exception('SOCKET: Could not listen on socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
    }
    
    public function select(&$read, &$write, &$except, $timeoutSeconds = null, $timeoutMicroseconds = 0) {

        // now call select - blocking call
        if (socket_select($read, $write, $except, $timeoutSeconds, $timeoutMicroseconds) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \Exception('SOCKET: Could not select socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return $this;
    }
    
    public function accept() {
        
        $client = socket_accept($this->resource);

        if ($client === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \Exception('SOCKET: Could not accept socket : [' . $errorcode . '] ' . $errormsg);
        }
        
        return new Socket($client);
    }
    
    public function read($length, $type = PHP_BINARY_READ) {
        
        if (($result = socket_read($this->resource, $length, $type)) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't read from socket : [$errorcode] $errormsg");
        }
        
        return $result;
    }
        
    public function getSockName(&$address, &$port) {
        
        if (socket_getsockname($this->resource, $address, $port) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't load socket information : [$errorcode] $errormsg");
        }
        
        return $this;
    }
        
    public function getPeerName(&$address, &$port) {
        
        if (socket_getpeername($this->resource, $address, $port) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            
            throw new \Exception("SOCKET: Couldn't load remote socket information : [$errorcode] $errormsg");
        }
        
        return $this;
    }
}
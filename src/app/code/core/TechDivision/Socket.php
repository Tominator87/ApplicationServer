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

    // *nix socket error number for Resource Temporarily Unavailable state
    const SOCKET_ERROR_RESOURCE_TEMPORARILY_UNAVAILABLE = 11;

    // times to retry reading from one socket if error 11 occurs
    const SOCKET_READ_RETRY_COUNT = 10;

    // time to wait between two read attempts on one socket in microseconds (here: 0.1 sec)
    const SOCKET_READ_RETRY_WAIT_TIME_USEC = 100000;

    /**
     * The socket resource.
     * @var resource
     */
    protected $resource = null;
    
    /**
     *
     * @var type 
     */
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
        $this->setResource($resource);
    }

    public function setResource($resource) {
        $this->resource = $resource;
    }

    public function getResource() {
        return $this->resource;
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

    /**
     * OO Wrapper for PHP's socket_read function. Except for the "Resource temporarily
     * unavailable" error, only an Exception is thrown. Typed exception is thrown to
     * allow handling of that error, since it should be treated in a special way.
     *
     * @param $length
     * @param int $type
     * @throws \Exception
     * @param int $type
     * @return string
     */
    public function read($length, $type = PHP_BINARY_READ) {

        // record the number of read attempts
        $readAttempts = 0;

        while (($result = socket_read($this->resource, $length, $type)) === false) {

            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            // for regular workflow, it is necessary to allow retrying a read operation on a socket which
            // signnals that it is not ready yet. At least under Linux, this is an acceptable
            // and normal condition. It is not sufficient to just end the connection and continue in the
            // main loop, since the original client connection would hang indefinitely until a timeout
            // occurs. Instead, it is necessary to retry reading while the socket is not ready yet, which
            // is usually no longer than a a few msecs.
            if ($errorcode == self::SOCKET_ERROR_RESOURCE_TEMPORARILY_UNAVAILABLE
                && $readAttempts++ < self::SOCKET_READ_RETRY_COUNT)
            {
                // sleep for a certain time
                usleep(self::SOCKET_READ_RETRY_WAIT_TIME_USEC);
                // continue with the next read attempt
                continue;
            }

            // on any other socket error, or if the max. number of read attempts has been reached, throw exception
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

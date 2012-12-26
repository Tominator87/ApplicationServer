<?php

/**
 * TechDivision_PersistenceContainerClient_Context_ContextConnection
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Context;

use TechDivision\PersistenceContainerClient\Context\ContextSession;
use TechDivision\PersistenceContainerClient\Interfaces\Connection;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * Connection implementation to invoke a remote method call over a socket.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ContextConnectionQueue implements Connection {
    
    /**
     * The unique numeric key for the sending message queue. 
     * @var integer
     */
    protected $sendKey = 8585;
    
    /**
     * The unique numeric key for the receiving message queue. 
     * @var integer
     */
    protected $receiveKey = 8586;
    
    /**
     * The message type of messages to receive.
     * @var integer
     */
    protected $messageType = 0;

    /**
     * The ArrayObject for the sessions.
     * @var ArrayObject
     */
    protected $sessions = null;

    /**
     * The client socket instance.
     * @var \TechDivision\Socket\Client
     */
    protected $client = null;

    /**
     * The server socket instance.
     * @var \TechDivision\Socket\Server
     */
    protected $server = null;

    /**
     * Initializes the connection.
     * 
     * @return void
     */
    public function __construct() {
        
        // initialize the ArrayObject for the sessions
        $this->sessions = new \ArrayObject();
    }
    
    public function __destruct() {
        // do nothing here
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::connect()
     */
    public function connect() {
        $this->setSocket(msg_get_queue($this->sendKey));
        $this->setServer(msg_get_queue($this->receiveKey));
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::disconnect()
     */
    public function disconnect() {
        // do nothing here
    }

    /**
     * Sets the socket to use for the client connection, a Socket instance by default.
     * 
     * @param resource $socket
     */
    public function setSocket($socket) {
        $this->socket = $socket;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::getSocket()
     */
    public function getSocket() {
        return $this->socket;
    }

    /**
     * Sets the socket to use for the connection, a Socket instance by default.
     * 
     * @param resource $server The server instance
     */
    public function setServer($server) {
        $this->server = $server;
        return $this;
    }

    /**
     * Returns the internal socket server instance.
     * 
     * @return resource The server instance
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * Set's the server's IP address for the client to connect to.
     * 
     * @param string $address The server's IP address to connect to
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * Returns the client socket's IP address.
     * 
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     *  Set's  the server's port for the client to connect to.
     * 
     * @param int $port The server's port to connect to
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * Returns the client socket's port.
     * 
     * @return string
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::send()
     */
    public function send(RemoteMethod $remoteMethod) {
        
        // prepare the remote method
        $remoteMethod->setAddress('0.0.0.0');
        $remoteMethod->setPort($port = rand(1, time()));
        
        // serialize the remote method and write it to the socket
        msg_send($this->getSocket(), 1, $remoteMethod, true);
        
        // intialize a new variable that contains the response
        $response = null;
        
        // read the response
        msg_receive($this->getServer(), $port, $this->messageType, 1024000, $response, true);
        
        // if an exception returns, throw it again
        if ($response instanceof \Exception) {
            throw $response;
        }
        
        // return the data
        return $response;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::createContextSession()
     */
    public function createContextSession() {
        return $this->sessions[] = $session = new ContextSession($this);
    }

}
<?php

/**
 * TechDivision\PersistenceContainerClient\Context\ContextConnection
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Context;

use TechDivision\Socket\Client;
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
class ContextConnectionSingleSocket implements Connection {

    /**
     * The client socket's IP address.
     * @var string
     */
    protected $address = '127.0.0.1';

    /**
     * The client socket's port.
     * @var integer
     */
    protected $port = 8585;

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
     * Initializes the connection.
     * 
     * @return void
     */
    public function __construct() {
        // initialize the ArrayObject for the sessions
        $this->sessions = new \ArrayObject();
    }
    
    /**
     * Close the sockets that will not be needed anymore.
     * 
     * @return void
     */
    public function __destruct() {
        $this->getSocket()->close();
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::connect()
     */
    public function connect() {
        // start the client
        $client = new Client($this->getAddress(), $this->getPort());
        $this->setSocket($client->start()->setBlock());
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::disconnect()
     */
    public function disconnect() {
    }

    /**
     * Sets the socket to use for the client connection, a Socket instance by default.
     * 
     * @param \TechDivision\Socket\Client $socket
     */
    public function setSocket(Client $socket) {
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

        $socket = $this->getSocket();

        $remoteMethod->setAddress($this->getAddress());
        $remoteMethod->setPort($this->getPort());

        // serialize the remote method and write it to the socket
        $socket->sendLine(serialize($remoteMethod));

        // read the response
        $serialized = $socket->readLine();

        // unserialize the response
        $response = unserialize($serialized);
        
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
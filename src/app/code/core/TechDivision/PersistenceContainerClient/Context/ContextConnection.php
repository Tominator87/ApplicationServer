<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision\PersistenceContainerClient
 */

namespace TechDivision\PersistenceContainerClient\Context;

use TechDivision\Socket\Client;
use TechDivision\Socket\Server;
use TechDivision\PersistenceContainerClient\Context\ContextSession;
use TechDivision\PersistenceContainerClient\Interfaces\Connection;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * Connection implementation to invoke a remote method call over a socket.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class ContextConnection implements Connection {

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
        $this->getSocket()->close();
        $this->getServer()->close();
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Connection::connect()
     */
    public function connect() {

        // start the client
        $client = new Client($this->getAddress(), $this->getPort());
        $this->setSocket($client->start());

        // get the local IP address
        $serverAddress = gethostbyname(gethostname());

        // create a server with the local IP address
        $server = new Server($serverAddress);
        $this->setServer($server->start());
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
     * Sets the socket to use for the connection, a Socket instance by default.
     * 
     * @param \TechDivision\Socket\Server $server The server instance
     */
    public function setServer(Server $server) {
        $this->server = $server;
        return $this;
    }

    /**
     * Returns the internal socket server instance.
     * 
     * @return TechDivision\Socket\Server The server instance
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

        $address = $this->getServer()->getAddress();
        $port = $this->getServer()->getPort();

        $this->getServer()->getSockName($address, $port);

        $remoteMethod->setAddress($address);
        $remoteMethod->setPort($port);

        // serialize the remote method and write it to the socket
        $this->getSocket()->sendLine(serialize($remoteMethod));

        // read the response
        $serialized = $this->getServer()->readLine();

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
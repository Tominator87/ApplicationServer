<?php

/**
 * TechDivision\PersistenceContainerClient\Interfaces\Connection
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Interfaces;

/**
 * The interface for the remote connection.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface Connection {

    /**
     * Creates the connection to the container.
     * 
     * @return void
     */
    public function connect();

    /**
     * Shutdown the connection to the container.
     * 
     * @return void
     */
    public function disconnect();

    /**
     * Sends the remote method call to the container instance.
     * 
     * @param TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod $remoteMethod
     * @return mixed The response from the container
     */
    public function send(RemoteMethod $remoteMethod);

    /**
     * Initializes a new session instance.
     * 
     * @return TechDivision\PersistenceContainerClient\Interfaces\Session The session instance
     */
    public function createContextSession();

    /**
     * Returns the socket the connection is based on.
     * 
     * @return \TechDivision\Socket\Client The socket instance
     */
    public function getSocket();
}
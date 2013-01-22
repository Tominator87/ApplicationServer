<?php

/**
 * TechDivision\PersistenceContainerClient\Context\ContextSession
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Context;

use TechDivision\PersistenceContainerClient\Interfaces\Session;
use TechDivision\PersistenceContainerClient\Interfaces\Connection;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;
use TechDivision\PersistenceContainerClient\Proxy\InitialContext;

/**
 * The interface for the remote connection.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ContextSession implements Session {

    /**
     * The connection instance.
     * @var TechDivision\PersistenceContainerClient\Interfaces\Connection
     */
    protected $_connection = null;

    /**
     * The session ID used for the connection.
     * @var string
     */
    protected $_sessionId = null;

    /**
     * 
     * @param TechDivision\PersistenceContainerClient\Interfaces\Connection $connection
     * @todo Refactor session handling
     */
    public function __construct(Connection $connection) {
        $this->_connection = $connection;
        // check if already a session id exists in the session
        if (($this->_sessionId = session_id()) == null) {
            // if not, create a unique ID
            $this->_sessionId = uniqid();
        }
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Session::getSessionId()
     */
    public function getSessionId() {
        return $this->_sessionId;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Session::send()
     * @todo Refactor to replace check for 'setSession' method, e. g. check for an interface
     */
    public function send(RemoteMethod $remoteMethod) {
        // connect to the container
        $this->_connection->connect();
        $response = $this->_connection->send($remoteMethod);
        // check if a proxy has been returned
        if (method_exists($response, 'setSession')) {
            $response->setSession($this);
        }
        // close the connection
        $this->_connection->disconnect();
        return $response;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\Session::createInitialContext()
     */
    public function createInitialContext() {
        $initialContext = new InitialContext();
        $initialContext->setSession($this);
        return $initialContext;
    }

}
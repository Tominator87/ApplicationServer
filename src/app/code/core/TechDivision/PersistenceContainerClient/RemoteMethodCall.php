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

namespace TechDivision\PersistenceContainerClient;

use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * Abstract base class of the Maps.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class RemoteMethodCall implements RemoteMethod {

    /**
     * The class name to invoke the method on.
     * @var string
     */
    protected $className = null;

    /**
     * The method name to invoke on the class
     * @var string
     */
    protected $methodName = null;

    /**
     * Parameters for the method.
     * @var array
     */
    protected $parameters = array();

    /**
     * The session ID to use for the method call.
     * @var string
     */
    protected $sessionId = null;

    /**
     * The client's socket server IP address to send the response to.
     * @var string 
     */
    protected $address = '127.0.0.1';

    /**
     * The client's socket server port to send the response to.
     * @var integer 
     */
    protected $port = 0;

    /**
     * Initialize the instance with the necessary params.
     * 
     * @param string $className The class name to invoke the method on
     * @param string $methodName The method name to invoke
     * @param string $sessionId The session ID to use for the method call
     * @return void
     */
    public function __construct($className, $methodName, $sessionId) {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->sessionId = $sessionId;
    }

    /**
     * Adds passed parameter to the array with the parameters.
     * 
     * @param string $key The parameter name
     * @param mixed $value The parameter value
     */
    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getParameter()
     */
    public function getParameter($key) {
        return $this->parameters[$key];
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getParameters()
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getClassName()
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getMethodName()
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getSessionId()
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * Sets the client's socket server IP address.
     * 
     * @param integer $address The client's socket server IP address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getAddress()
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Sets the client's socket server port.
     * 
     * @param integer $port The client's socket server port
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod::getPort()
     */
    public function getPort() {
        return $this->port;
    }

}
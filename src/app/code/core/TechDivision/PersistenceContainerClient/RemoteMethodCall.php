<?php

/**
 * TechDivision\PersistenceContainerClient\RemoteMethodCall
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient;

use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * Abstract base class of the Maps.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
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
    public function __construct($className, $methodName, $sessionId = null) {
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
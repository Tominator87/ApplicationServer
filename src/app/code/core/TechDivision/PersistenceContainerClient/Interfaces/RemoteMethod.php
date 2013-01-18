<?php

/**
 * TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Interfaces;

/**
 * Interface for all remote methods.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface RemoteMethod {

    /**
     * Returns the method name to invoke on the class.
     * 
     * @return string The method name
     */
    public function getMethodName();

    /**
     * Returns the class name to invoke the method on.
     * 
     * @return string The class name
     */
    public function getClassName();

    /**
     * Returns the parameter with the passed key.
     * 
     * @param string $key The name of the parameter to return
     * @return mixed The parameter's value
     */
    public function getParameter($key);

    /**
     * Returns the parameters for the method.
     * 
     * @return array The method's parameters
     */
    public function getParameters();

    /**
     * Returns the session ID to use for the method call.
     * 
     * @return string The session ID
     */
    public function getSessionId();

    /**
     * Returns the client's server socket IP address.
     * 
     * @return string The client's server socket IP address
     */
    public function getAddress();

    /**
     * Returns the client's server socket port.
     * 
     * @return string The client's server socket port
     */
    public function getPort();
}
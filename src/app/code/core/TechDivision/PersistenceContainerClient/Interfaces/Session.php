<?php

/**
 * TechDivision_PersistenceContainerClient_Interfaces_Session
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Interfaces;

/**
 * The interface for the session.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface Session {

    /**
     * Returns the ID of the session to use.
     * 
     * @return string The session ID
     */
    public function getSessionId();

    /**
     * Invokes the remote method over the connection.
     *  
     * @param TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod $remoteMethod The remote method call to invoke
     * @return mixed the method return value
     */
    public function send(RemoteMethod $remoteMethod);

    /**
     * Creates a remote inital context instance.
     * 
     * @return TechDivision\PersistenceContainerClient\Interfaces\RemoteObject The proxy for the inital context
     */
    public function createInitialContext();
}
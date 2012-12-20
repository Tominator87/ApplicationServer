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

namespace TechDivision\PersistenceContainerClient\Interfaces;

/**
 * Interface for all remote methods.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
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
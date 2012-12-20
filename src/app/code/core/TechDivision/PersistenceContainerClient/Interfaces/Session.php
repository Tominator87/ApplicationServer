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
 * The interface for the session.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
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
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
 * The interface for the remote connection.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
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
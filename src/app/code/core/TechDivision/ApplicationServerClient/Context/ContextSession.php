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
 * @package TechDivision\ApplicationServerClient
 */

namespace TechDivision\ApplicationServerClient\Context;

use TechDivision\ApplicationServerClient\Interfaces\Session;
use TechDivision\ApplicationServerClient\Interfaces\Connection;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;
use TechDivision\ApplicationServerClient\Proxy\InitialContext;

/**
 * The interface for the remote connection.
 *
 * @package TechDivision\ApplicationServerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class ContextSession implements Session {

	/**
	 * The connection instance.
	 * @var TechDivision\ApplicationServerClient\Interfaces\Connection
	 */
	protected $_connection = null;
	
	/**
	 * The session ID used for the connection.
	 * @var string
	 */
	protected $_sessionId = null;
	
	/**
	 * 
	 * @param TechDivision\ApplicationServerClient\Interfaces\Connection $connection
	 * @throws Exception
	 */
	public function __construct(Connection $connection) {
		$this->_connection = $connection;
		// check if alread a session id exists in the session
		if (($this->_sessionId = session_id()) == null) {
			throw new \Exception("No session available");
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Session::getSessionId()
	 */
	public function getSessionId() {
		return $this->_sessionId;
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Session::send()
	 * @todo Refactor to replace check for 'setSession' method, e. g. check for an interface
	 */
	public function send(RemoteMethod $remoteMethod)
    {
    	// connect to the container
		$this->_connection->connect();
		$response = $this->_connection->send($remoteMethod);
        
        error_log(var_export($response, true));
        
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
	 * @see TechDivision\ApplicationServerClient\Interfaces\Session::createInitialContext()
	 */
	public function createInitialContext()
	{
	    $initialContext = new InitialContext();
	    $initialContext->setSession($this);
	    return $initialContext;
	}
}
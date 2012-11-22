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

use TechDivision\ApplicationServerClient\Context\ContextSession;
use TechDivision\ApplicationServerClient\Interfaces\Connection;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;

/**
 * Connection implementation to invoke a remote method call over a socket.
 *
 * @package TechDivision\ApplicationServerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class ContextConnection implements Connection
{

	/**
	 * The socket server connection parameters.
	 * @var string
	 */
	const SERVER = "172.16.0.130";
	const PORT = 8585;
	const PERSISTENT = false;
	const TIMEOUT = 1;

	/**
	 * The ArrayObject for the sessions.
	 * @var ArrayObject
	 */
	protected $_sessions = null;
	
	/**
	 * The socket instance.
	 * @var Net_Socket
	 */
	protected $_socket = null;

	/**
	 * Initializes the connection.
	 * 
	 * @return void
	 */
	public function __construct() {
		// initialize the ArrayObject for the sessions
		$this->_sessions = new \ArrayObject();
		// initialize the socket instance
		$socket = new \Net_Socket();
		$socket->setBlocking(false);
		$this->setSocket($socket);
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Connection::connect()
	 */
	public function connect() {
		// initialize the socket
		$this->getSocket()->connect(self::SERVER, self::PORT, self::PERSISTENT, self::TIMEOUT);
		// check if the connection was successfull
		if (\PEAR::isError($this->getSocket())) {
			throw new \Exception($this->getSocket()->getMessage());
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Connection::disconnect()
	 */
	public function disconnect() {
		$this->getSocket()->disconnect();
	}
	
	/**
	 * Sets the socket to use for the connection, a Net_Socket instance by default.
	 * 
	 * @param \Net_Socket $socket
	 */
	public function setSocket(\Net_Socket $socket) {
		$this->_socket = $socket;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Connection::getSocket()
	 */
	public function getSocket() {
		return $this->_socket;
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Connection::send()
	 */
	public function send(RemoteMethod $remoteMethod) {
		// serialize the remote method and write it to the socket
		$written = $this->getSocket()->writeLine(serialize($remoteMethod));
		// ckeck if the remote method has successfully been written to the socket
		if (\PEAR::isError($written)) {
			throw new \Exception($written->getMessage());
		}
		// read the reploy
		$serialized = $this->getSocket()->readLine();
		// unserialize the response
		$response = unserialize($serialized);
		// if an exception returns, throw it again
		if ($response instanceof \Exception) {
			throw $response;
		}
		// return the data
		return $response;	
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\Connection::createContextSession()
	 */
	public function createContextSession() {
		return $this->_sessions[] = $session = new ContextSession($this);
	}
}
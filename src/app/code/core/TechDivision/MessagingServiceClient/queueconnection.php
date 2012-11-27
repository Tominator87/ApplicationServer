<?php

    /**
     * $Header: mqclient/queuesession.php
     * $Revision: 0.0.2
     * $Date: 2008-10-15
     *
     * ====================================================================
     *
     * License:    GNU General Public License
     *
     * Copyright (c) 2004 struts4php.org.  All rights reserved.
     * Note: Original work copyright to respective authors
     *
     * This file is part of struts4php.
     *
     * struts4php is free software; you can redistribute it and/or
     * modify it under the terms of the GNU General Public License
     * as published by the Free Software Foundation; either version 2
     * of the License, or (at your option) any later version.
     *
     * struts4php is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program; if not, write to the Free Software
     * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
     * USA.
     */

	require_once "Net/Socket.php";
	require_once "collections/arraylist.php";
	require_once "mqclient/queue.php";
	require_once "mqclient/queuesession.php";
	require_once "mqclient/queueresponse.php";
	require_once "mqclient/interfaces/message.php";
	 
    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.5 $ $Date: 2009-01-03 13:11:54 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class QueueConnection {
		
		/**
		 * TREU if the connection was already established, else FALSE.
		 * @var boolean
		 */
		private $connected = false;

		/**
		 * Holds the IP address or domain name of the server the message queue is running on.
		 * @var string
		 */
		private $server = "127.0.0.1";
		
		/**
		 * Holds the port for the connection.
		 * @var integer
		 */
		private $port = 9090;
		
		/**
		 * Holds the flag to use a persistent server connection, if yes the flag is TRUE.
		 * @var boolean
		 */
		private $persistent = true;
		
		/**
		 * Holds the connection timeout in seconds or null for no timeout.
		 * @var integer
		 */
		private $timeout = null;

		/**
		 * Holds an ArrayList with the initialized sessions.
		 * @var ArrayList
		 */
		private $sessions = null;
		
		/**
		 * Holds the socket for the connection.
		 * @var Net_Socket
		 */
		private $socket = null;
		
		/**
		 * The actual connection id.
		 * @var integer
		 */
		private $id = 0;
		
		/**
		 * Is TRUE if the connection should be validated, else FALSE.
		 * @var boolean
		 */
		private $validate = false;
		
		/**
		 * Initializes the QueueConnection and the
		 * socket.
		 * 
		 * @return void
		 */
		public function __construct() {
			$this->sessions = new ArrayList();
			$this->socket = new Net_Socket();
		}

		/**
		 * Initializes the connection by starting
		 * the socket.
		 * 
		 * @return void
		 * @throws Exception Is thrown if connection can't be established
		 */
		protected function connect() {
			// check if the connection was already established
			if(!$this->connected) { 
				// if not, try to connect to the MessageQueue
				if(($isError = PEAR::isError($this->socket->connect($this->server, $this->port, $this->persistent, $this->timeout))) === true) {
					throw new Exception("Can't connect to socket $this->server:$this->port");
				}
				// read the response from the Socket
				if($read = PEAR::isError($response = $this->socket->readLine()) === true) {
					throw new Exception($response->getMessage());
				}
				// check if the connection should be validated
				if($this->validate === true) {
					// check the QueueResponse
					$queueResponse = QueueResponse::parse(new String($response));
					// parse and return the connection id
					$this->id = Integer::parseInteger(new String($queueResponse->getMessage()));
				}
				// set the the connected flag
				$this->connected = true;
			}
		}

		/**
		 * Sets the IP address or domain name of the server the 
		 * message queue is running on.
		 * 
		 * @param string $server Holds the server to connect to
		 * @return void
		 */
		public function setServer($server) {
			$this->server = $server;
		}

		/**
		 * Sets  the port for the connection.
		 * 
		 * @param integer $port Holds the port for the connection
		 * @return void
		 */
		public function setPort($port) {
			$this->port = $port;
		}

		/**
		 * Sets  the connection timeout in seconds.
		 * 
		 * @param integer $port Holds the connection timeout
		 * @return void
		 */
		public function setTimeout($timeout) {
			$this->timeout = $timeout;
		}

		/**
		 * Sets  the flag to use a persistent server 
		 * connection to TRUE.
		 * 
		 * @param boolean $persistent Holds  the flag to use a persistent server connection or not
		 * @return void
		 */
		public function setPersistent($persistent = true) {
			$this->persistent = $persistent;
		}
		
		/**
		 * Closes the connection to the server by
		 * closing the socket.
		 * 
		 * @return void
		 * @throws Exception Is thrown if connection can't be closed
		 */
		public function disconnect() {
			if(($isError = PEAR::isError($disconnected = $this->socket->disconnect())) === true) {
				throw new Exception($disconnected->getMessage());
			}
			// set the disconnected flag to false
			$this->connected = false;
		}
		
		/**
		 * Has to be invoked to validate that the 
		 * connection was successfully established.
		 * 
		 * @return void
		 */
		public function validate() {
			$this->validate = true;
		}

		/**
		 * Sends a Message to the server by writing
		 * it to the socket.
		 * 
		 * @param Message $message Holds the message to send
		 * @param boolean $validateResponse If this flag is true, the QueueConnection waits for the MessageQueue response and validates it 
		 * @return QueueResponse The response of the MessageQueue, or null
		 */
		public function send(Message $message, $validateResponse) {
			// throw an exception if the connection is not established
			if(!$this->connected) {
				throw new Exception("Can't send message because connection is not established");
			}
			// write the Message to the MessageQueue
			if(($written = PEAR::isError($this->socket->writeLine(serialize($message)))) === true) {
				throw new Exception($written->getMessage());
			}
			// check if the QueueResponse has to be validated
			if($validateResponse === true) { // if yes
				// read the response from the Socket
				if($read = PEAR::isError($response = $this->socket->readLine()) === true) {
					throw new Exception($read->getMessage());
				}
				// return the QueueResponse
				return QueueResponse::parse(new String($response));
			}
			// return without to wait and validate the QueueResponse
			return;
		}

		/**
		 * Initializes a new QueueSession instance, registers it 
		 * in the ArrayList with the open sessions and returns it.
		 * 
		 * @return QueueSession The initialized QueueSession instance
		 */
		public function createQueueSession() {
			// establish a connection
			$this->connect();
			// initialize and register the session
			$this->sessions->add($session = new QueueSession($this));
			// return the session
			return $session;
		}
		
		/**
		 * Returns the connection id.
		 * 
		 * @return integer The unique id
		 */
		public function getId() {
			return $this->id;	
		}
		
		/**
		 * Returns TRUE if the connection is established,
		 * else FALSE.
		 * 
		 * @return boolean TRUE if the connection is established, else FALSE
		 */
		public function isConnected() {
			return $this->connected;		
		}
	}

?>
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

	require_once "mqclient/queue.php";
	require_once "mqclient/queuesender.php";
	require_once "mqclient/queueconnection.php";
	require_once "mqclient/interfaces/message.php";
	 
    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.4 $ $Date: 2009-01-03 13:11:54 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class QueueSession {

		/**
	 	 * Holds the QueueConnection instance to use for the server connect.
	 	 * @var QueueConnection
	 	 */
		private $connection = null;
		
		/**
		 * Holds the unique session id.
		 * @var string
		 */
		private $id = null;

		/**
		 * Initializes the session with the QueueConnection instance
		 * to use for the server connection.
		 * 
		 * @param QueueConnection $connection Holds the QueueConnection instance to use
		 * @return void
		 */
		public function __construct(QueueConnection $connection) {
			// initialize the internal connection
			$this->connection = $connection;
			// generate and return the unique session id
			return $this->id = md5(uniqid(rand(), true));
		}

		/**
		 * Sends the passed Message instance to the server,
		 * using the QueueConnection instance.
		 * 
		 * @param Message $message The message to send
		 * @param boolean $validateResponse If this flag is true, the QueueConnection waits for the MessageQueue response and validates it 
		 * @return QueueResponse The response of the MessageQueue, or null
		 */
		public function send(Message $message, $validateResponse) {
			return $this->connection->send($message, $validateResponse);
		}

		/**
		 * Creates and returns a new QueueSender instance for sending 
		 * the Message to the server.
		 * 
		 * @param Queue $queue the Queue instance to use for sending the message
		 * @return QueueSender The initialized QueueSender instance
		 */
		public function createSender(Queue $queue) {
			return new QueueSender($this, $queue);
		}
		
		/**
		 * Returns the session id.
		 * 
		 * @return integer The unique id
		 */
		public function getId() {
			return $this->id;	
		}
	}

?>

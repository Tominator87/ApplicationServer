<?php

    /**
     * $Header: mqclient/queuesender.php
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

	require_once "mqclient/interfaces/message.php";
	require_once "mqclient/queue.php";
	require_once "mqclient/queuesession.php";
	require_once "mqclient/queueconnectionfactory.php";
	 
    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.4 $ $Date: 2009-01-03 13:11:54 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class QueueSender {

		/**
		 * Holds the Queue instance used for sending the message.
		 * @var Queue
		 */
		private $queue = null;
		
		/**
		 * Holds the QueueSession instance for sending the message.
		 * @var QueueSession
		 */
		private $session = null;

		/**
		 * Initializes the QueueSender with the QueueSession and Queue instance
		 * to use for sending the Message to the server.
		 * 
		 * @param QueueSession $session The QueueSession instance for sending the message
		 * @param Queue $queue The Queue instance used for sending the message
		 * @return void
		 */
		public function __construct(QueueSession $session, Queue $queue) {
			$this->session = $session;
			$this->queue = $queue;
		}

		/**
		 * Sends the passed Message to the server.
		 * 
		 * @param Message $message the Message to send
		 * @param boolean $validateResponse If this flag is true, the QueueConnection waits for the MessageQueue response and validates it 
		 * @return QueueResponse The response of the MessageQueue, or null
		 */
		public function send(Message $message, $validateResponse = false) {
			$message->setDestination($this->queue);
			$message->setSessionId($this->session->getId());
			return $this->session->send($message, $validateResponse);
		}
	}

?>
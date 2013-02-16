<?php

    /**
     * $Header: /var/lib/mqserver/deploy/dummy-queues/cachecounterreceiver.php
     * $Revision: 0.1
     * $Date: 2009-05-28
     *
     * ====================================================================
     *
     * License:    GNU General Public License
     *
     * Copyright (c) 2005 TechDivision GbR.  All rights reserved.
     * Note: Original work copyright to respective authors
     *
     * This file is part of mqueue.
     *
     * mqueue is free software; you can redistribute it and/or
     * modify it under the terms of the GNU General Public License
     * as published by the Free Software Foundation; either version 2
     * of the License, or (at your option) any later version.
     *
     * mqueue is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program; if not, write to the Free Software
     * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
     * USA.
     */

	require_once "lang/string.php";
	require_once "mqclient/interfaces/messagereceiver.php";
	require_once "mqclient/interfaces/message.php";
	require_once "mqclient/receiver/abstractreceiver.php";

    /**
     * This is the dummy implementation of 
     * a Receiver.
     * 
     * @package mqserver
     * @subpackage deploy
     * @author wagnert <tw@struts4php.org>
     * @version $Revision: 1.3 $ $Date: 2008-12-23 15:56:05 $
     * @copyright struts4php
     * @link http://www.struts4php.org
     */
	class CacheCounterReceiver extends AbstractReceiver {

		/**
		 * The logger instance.
		 * @var Log4PHP
		 */
		private $logger = null;
		
		/**
		 * Initializes the receiver with the initializing 
		 * TalkbackHandler.
		 * 
		 * @param TalkbackHandler $talkbackHandler The initializing TalkbackHandler
		 * @return void
		 * @see AbstractReceiver::__construct(TalkbackHandler $talkbackHandler = null)
		 */
		public function __construct(TalkbackHandler $talkbackHandler = null) {
			// call the constructor of the superclass
			AbstractReceiver::__construct($talkbackHandler);
			// initialize the logger
			$this->logger = Logger::forObject($this, "META-INF/log4php.properties");
		}
		
		/**
		 * @see MessageReceiver::onMessage(Message $message, String $sessionId)
		 */
		public function onMessage(Message $message, String $sessionId) {
			// log that a Message was received
			$this->logger->info($logMessage = "Successfully received / finished message");
			// initialize the MessageMonitor
			$message->setMessageMonitor($monitor = new MessageMonitor(new Integer(1), new String($logMessage)));
			$monitor->setRowCount(new Integer(1));
			// update the MessageMonitor
			$this->updateMonitor($message);
		}
	}

?>
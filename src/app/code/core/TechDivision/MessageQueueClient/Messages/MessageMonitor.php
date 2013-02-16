<?php

    /**
     * $Header: mqclient/messagemonitor.php
     * $Revision: 0.0.2
     * $Date: 2009-04-02
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

	require_once "lang/string.php";
	require_once "lang/integer.php";
	 
    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class MessageMonitor {
		
		/**
		 * The target counter for monitoring the message.
		 * @var Integer
		 */
		private $target = null;
		
		/**
		 * The row counter for monitoring the message.
		 * @var Integer
		 */
		private $rowCount = null;
		
		/**
		 * The log message for monitoring the message.
		 * @var String
		 */
		private $logMessage = null;

		/**
		 * Initializes the queue with the name to use.
		 * 
		 * @param String $name Holds the queue name to use
		 * @return void
		 */
		public function __construct(Integer $target, String $logMessage) {
			$this->target = $target;
			$this->logMessage = $logMessage;
			$this->rowCount = new Integer(0);
		}

		/**
		 * Sets the log message.
		 * 
		 * @param String $logMessage The log message
		 * @return void
		 */
		public function setLogMessage(String $logMessage) {
			$this->logMessage = $logMessage;
		}

		/**
		 * Returns the row counter.
		 * 
		 * @param Integer $rowCount The row counter
		 * @return void
		 */
		public function setRowCount(Integer $rowCount) {
			$this->rowCount = $rowCount;
		}

		/**
		 * Returns the log message.
		 * 
		 * @return String The log message
		 */
		public function getLogMessage() {
			return $this->logMessage;
		}

		/**
		 * Returns the row counter.
		 * 
		 * @return Integer The row counter
		 */
		public function getRowCount() {
			return $this->rowCount;
		}

		/**
		 * Returns the target counter.
		 * 
		 * @return Integer The target counter
		 */
		public function getTarget() {
			return $this->target;
		}
	}

?>
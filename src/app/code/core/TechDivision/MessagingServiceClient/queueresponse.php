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
	 
	require_once "lang/string.php";

    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.1 $ $Date: 2008-12-31 12:39:41 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class QueueResponse {
		
		/**
		 * Holds the response parts.
		 * @var array
		 */
		private $regs = array();
		
		/**
		 * Initializes the QueueResponse with the parts 
		 * splitted from the MessageQueue response.
		 *  
		 * @param array $regs The respons parts
		 * @return void
		 */
		private function __construct($regs) {
			$this->regs = $regs;
		}
		
		/**
		 * Returns true if the Message was successfully delivered
		 * to the MessageQueue.
		 * 
		 * @return boolean True if the Message was successfully delivered, else false
		 */
		public function success() {
			// if the Message was delivered successfully return true
			if(($code = $this->regs[2]) == 200) {
				return true;
			}
			// else false
			return false;
		}
		
		/**
		 * Returns the status from the
		 * MessageQueue response.
		 * 
		 * @return integer The status itself
		 */
		public function getStatus() {
			return $this->regs[2];
		}
		
		/**
		 * Returns the status message from the
		 * MessageQueue response.
		 * 
		 * @return string The status message
		 */
		public function getMessage() {
			return $this->regs[3];
		}
		
		/**
		 * Parses the MessageQueue response passed as String and 
		 * initializes the QueueResponse with the splitted values.
		 * 
		 * @param String $response The response from the MessageQueue
		 * @return QueueResponse The initialized instance
		 * @static
		 * @throws Exception Is thrown if an invalid response was sent from the server
		 */
		public static function parse(String $response) {
			// initialize the array for the MessageQueue response parts
			$regs = array();
	        // split lines
	        $lines = explode("\r\n", $response->stringValue());
			// split the response into parts		
	        if(!preg_match("'(MQ/[^ ]+) ([^ ]+) ([^ ]+)'", $lines[0], $regs)) {
	            throw new Exception("Invalid MessageQueue response " . $response->stringValue());
	        }
		    // initialize the QueueResponse with the response parts
		  	return new QueueResponse($regs);      
		}
	}

?>
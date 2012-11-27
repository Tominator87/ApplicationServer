<?php

    /**
     * $Header: mqclient/queueconnectionfactory.php
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

	require_once "mqclient/queueconnection.php";
	 
    /**
     * @package	mqclient
     * @author	wagnert <tw@struts4php.org>
     * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
     * @copyright struts4php.org
     * @link www.struts4php.org
     */
	class QueueConnectionFactory {
		
		/**
		 * Private constructor to use class only in static context.
		 * 
		 * @return  void
		 */
		private function __construct() { /* Marks class as utility */ }
		
		/**
		 * Returns the QueueConnection instance as singleton.
		 * 
		 * @return QueueConnection The singleton instance
		 */
		public static function createQueueConnection() {
			return new QueueConnection();
		}
	}

?>
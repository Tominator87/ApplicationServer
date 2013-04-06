<?php

namespace TechDivision\Logger\Interfaces;


/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Logger is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Logger is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Logger
 */

/**
 * This is the interface for all logger
 * types.
 *
 * @package TechDivision_Logger
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
interface Logger {

	/**
	 * Logs to the system logger defined in php.ini.
	 * @var integer
	 * @see http://de2.php.net/manual/de/function.error-log.php
	 */
	const LOG_TYPE_SYSTEM = 0;

	/**
	 * Logs to configured email address.
	 * @var integer
	 * @see http://de2.php.net/manual/de/function.error-log.php
	 */
	const LOG_TYPE_MAIL = 1;

	/**
	 * Logs to a custom file.
	 * @var integer
	 * @see http://de2.php.net/manual/de/function.error-log.php
	 */
	const LOG_TYPE_CUSTOM_FILE = 3;

	/**
	 * Logs to the console.
	 * @var integer
	 */
	const LOG_TYPE_CONSOLE = 4;


	/**
	 * Logs an error message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function error($message);

	/**
	 * Logs an emergeny message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function emergency($message);

	/**
	 * Logs an alert message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function alert($message);

	/**
	 * Logs a critical error message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function critical($message);

	/**
	 * Logs a warning in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function warning($message);

	/**
	 * Logs a notice in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function notice($message);

	/**
	 * Logs an info message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function info($message);

	/**
	 * Logs a debug message in the log target.
	 *
	 * @param string $message Holds the message to log
	 * @return void
	 */
	public function debug($message);

	/**
	 * This method logs the passed message with the
	 * also passed log level to the logging target.
	 *
	 * @param string $message Holds the message to log
	 * @param integer $level Holds the log level that should be used
	 * @return void
	 */
	public function log($message, $level = 3);
}
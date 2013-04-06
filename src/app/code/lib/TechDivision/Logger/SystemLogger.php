<?php

namespace TechDivision\Logger;

use TechDivision\Logger\AbstractLogger;

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
 * This class is a logger implementation for PHP and sends all log
 * messages to the logfile defined in the php.ini configuration file.
 *
 * @package TechDivision_Logger
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class SystemLogger extends AbstractLogger
{

    /**
     * The constructor initialize the logger instance with the
     * classname and the properties from the configuraion file.
     *
     * @param string $classname Holds the classname for log message
     * @param string $logLevel Holds loglevel which should be maximal logged
     * @return void
     */
    public function __construct(
        $classname, $logLevel)
    {
        // initialize the superclass
        AbstractLogger::__construct($classname, $logLevel);
    }

    /**
     * This method logs the passed message with the
     * also passed log level to the logging target.
     *
     * @param string $message Holds the message to log
     * @param integer $level Holds the log level that should be used
     * @param integer $line Holds the line where the message was logged
     * @param string $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @throws LoggerException
     *        Is thrown if the log message can not be written to the system log
     */
    public function log($message, $level = 3, $line = null, $method = null)
    {
        // if the passed log level is equal or smaller
        if ($level <= $this->getLogLevel()) {
            // initialize the log time
            $time = time();
            // log the message passed as parameter
            $written = error_log(
                $this->message(
                    $message,
                    $level,
                    $line,
                    $method,
                    $time
                ),
                SystemLogger::LOG_TYPE_SYSTEM
            );
            // check if the message was successfully written
            if ($written === false) {
                throw new TechDivision_Logger_Exceptions_LoggerException(
                    'Error while writing log message to system log'
                );
            }
            // return the timestamp when the message was logged
            return $time;
        }
    }
}
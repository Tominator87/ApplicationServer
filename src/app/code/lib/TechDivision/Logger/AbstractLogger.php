<?php

namespace TechDivision\Logger;

use TechDivision\Logger\Interfaces\Logger as LoggerInterface;
use TechDivision\Logger\Exceptions\InvalidLogLevelException;
use TechDivision\Logger\Exceptions\InvalidLogTypeException;
use TechDivision\Logger\Exceptions\LoggerException;

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
 * This class is a logger implementation for
 * PHP.
 *
 * @package TechDivision_Logger
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class AbstractLogger
    implements LoggerInterface
{

    /**
     * Holds the constant for the log level property value.
     * @var string
     */
    const LOG_LEVEL = 'log_level';

    /**
     * Holds the classname of the class to log for.
     * @var string
     */
    protected $_classname = null;

    /**
     * Holds the log level
     * @var integer
     */
    protected $_logLevel = null;


    /**
     * Holds an array with possible log levels.
     * @var array
     */
    public static $levels = array(
        Logger::LOG_EMERG => "emergency",
        Logger::LOG_ALERT => "alert",
        Logger::LOG_CRIT => "critical",
        Logger::LOG_ERR => "error",
        Logger::LOG_WARNING => "warning",
        Logger::LOG_NOTICE => "notice",
        Logger::LOG_INFO => "info",
        Logger::LOG_DEBUG => "debug"
    );

    /**
     * The constructor initialize the logger instance with the
     * classname and the Properties from the configuraion file.
     *
     * @param string $classname Holds the classname for log message
     * @param string $logLevel Holds loglevel which should be maximal logged
     * @return void
     */
    public function __construct(
        $classname, $logLevel)
    {
        // set the classname
        $this->setClassname($classname);
        $this->setLogLevel($logLevel);
    }

    /**
     * Logs an error message to the log target..
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the error occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see TechDivision_Logger_Interfaces_Logger::log($message, $level)
     */
    public final function error($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_ERR,
            $line,
            $method
        );
    }

    /**
     * Logs an emergeny message to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the emergency message occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see TechDivision_Logger_Interfaces_Logger::log($message, $level)
     */
    public final function emergency($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_EMERG,
            $line,
            $method
        );
    }

    /**
     * Logs an alert message to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the alert message occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function alert($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_ALERT,
            $line,
            $method
        );
    }

    /**
     * Logs a critical error message to the log target..
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the critical message occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function critical($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_CRIT,
            $line,
            $method
        );
    }

    /**
     * Logs a warning to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the warning occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function warning($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_WARNING,
            $line,
            $method
        );
    }

    /**
     * Logs a notice to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the notic occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function notice($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_NOTICE,
            $line,
            $method
        );
    }

    /**
     * Logs an info message to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the info message occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function info($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_INFO,
            $line,
            $method
        );
    }

    /**
     * Logs a debug message to the log target.
     *
     * @param $message Holds the message to log
     * @param $line Holds the line where the debug message occurs
     * @param $method The origin method
     * @return integer Timestamp with the log date as UNIX timestamp
     * @see LoggerInterface::log($message, $level)
     */
    public final function debug($message, $line = null, $method = null)
    {
        return $this->log(
            $message,
            Logger::LOG_DEBUG,
            $line,
            $method
        );
    }

    /**
     * This method builds the log message based on the passed
     * parameters and returns it as string.
     *
     * @param string $message Holds the message
     * @param integer $level Holds the actual log level
     * @param integer $line Holds the line where the message was send from
     * @param integer $method The origin method
     * @param integer $time Holds the time when the message was created
     * @return string Holds the generated log message
     */
    public final function message(
        $message,
        $level,
        $line = null,
        $method = null,
        $time = null)
    {
        // initialize the time
        if (empty($time)) {
            $time = time();
        }
        // build and return the log message
        $stream = "";
        // add the classname/method to the log message
        if (empty($method)) {
            $stream .= $this->getClassname();
        } else {
            $stream .= $method;
        }
        // add the log level to the log message
        $stream .= "[" . AbstractLogger::$levels[$level] . "] " .
                   date("Y-m-d H:i:s", $time) . " ";
        // add the line to the log message
        if (!empty($line)) {
            $stream .= "- line " . $line . " ";
        }
        $stream .= $message;
        return $stream;
    }


    /**
     * Sets the classname.
     *
     * @param string $classname
     *        The classname to use
     * @return LoggerInterface
     *        The logger instance itself
     */
    public function setClassname($classname)
    {
        $this->_classname = $classname;
        return $this;
    }

    /**
     * Returns the classname.
     *
     * @return string The classname to log
     */
    public function getClassname()
    {
        return $this->_classname;
    }

    /**
     * Checks if the passed log level is valid
     * and sets it.
     *
     * @param integer $logLevel The log level to set
     * @return LoggerInterface
     *        The logger instance itself
     * @throws InvalidLogLevelException
     *        Is thrown if an invalid log level was passed
     */
    public function setLogLevel($logLevel)
    {
        // check if a valid log level was passed
        if (!array_key_exists($logLevel, self::$levels)) {
            throw new InvalidLogLevelException(
                "Found invalid log level $logLevel"
            );
        }
        // set the log level and return the instance
        $this->_logLevel = $logLevel;
        return $this;
    }

    /**
     * Returns the actual log level.
     *
     * @return integer The actual log level
     */
    public function getLogLevel()
    {
        return $this->_logLevel;
    }
}
<?php

namespace TechDivision\Logger;

use TechDivision\Logger\Interfaces\Logger as LoggerInterface;
use TechDivision\Logger\Exceptions\InvalidLogTypeException;

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
class Logger
{

    /**
     * System is unusable.
     * @var integer
     */
    const LOG_EMERG = 0;

    /**
     * Immediate action required.
     * @var integer
     */
    const LOG_ALERT = 1;

    /**
     * Critical conditions.
     * @var integer
     */
    const LOG_CRIT = 2;

    /**
     * Error conditions.
     * @var integer
     */
    const LOG_ERR = 3;

    /**
     * Warning conditions.
     * @var integer
     */
    const LOG_WARNING = 4;

    /**
     * Normal but significant.
     * @var integer
     */
    const LOG_NOTICE = 5;

    /**
     * Informational.
     * @var integer
     */
    const LOG_INFO = 6;

    /**
     * Debug-level messages.
     * @var integer
     */
    const LOG_DEBUG = 7;

    /**
     * The log type to use.
     * @var string
     */
    const LOG_TYPE = 'log_type';

    /**
     * The default log level
     * @var string
     */
    const DEFAULT_LOG_LEVEL = 7;

    /**
     * Holds the logger instance if singleton is requested
     * @var Logger
     */
    public static $instance = null;

    /**
     * Private constructor to make this class static class.
     *
     * @return void
     */
    private function __construct()
    {
        // Marks this class as util
    }

    /**
     * Returns the logger instance as singleton.
     *
     * @param string $classname Holds the class for logging purposes
     * @param string $logType Holds logtype of the created logclass
     * @param string $logLevel Holds loglevel which should be maximal logged
     * @param array $logTypeProperties Holds the needed configurations for the logtype
     * @return TechDivision_Logger_Interfaces_Logger The instance
     */
    public static function forClass($classname, $logType, $logLevel = self::DEFAULT_LOG_LEVEL, $logTypeProperties = array())
    {
        // return a new instance
        return Logger::_create(
            $classname,
            $logType,
            $logLevel,
            $logTypeProperties
        );
    }

    /**
     * Returns a new logger for the passed object.
     *
     * @param string $classname Holds the class for logging purposes
     * @param string $confFile Holds the path to the configuration file
     * @param array $logTypeProperties Holds the needed configurations for the logtype
     * @return TechDivision_Logger_Interfaces_Logger The instance
     */
    public static function forObject($object, $logType, $logLevel = self::DEFAULT_LOG_LEVEL, $logTypeProperties = array())
    {
        // get the classname
        $obj = new ReflectionObject($object);
        // return a new logger instance
        return Logger::forClass(
            $obj->getName(),
            $logType,
            $logLevel,
            $logTypeProperties
        );
    }


    /**
     * This method returns the logger depending on the type
     * specified in the configuration file.
     *
     * @param string $classname Holds the classname for the log message
     * @param string $logType Holds logtype of the created logclass
     * @param string $logLevel Holds loglevel which should be maximal logged
     * @return LoggerInterface The instance
     * @throws InvalidLogTypeException
     *        Is thrown if an invalid log type is requested
     */
    protected static function _create(
        $classname,
        $logType,
        $logLevel,
        $logTypeProperties = array()
          )
    {
        // get the type of the logger to return
        $type = (integer)$logType;
        // initialize the logger
        $logger = null;
        // get an instance of the requested type
        switch ($type) {
            case LoggerInterface::LOG_TYPE_SYSTEM:
                $logger = new SystemLogger(
                    $classname,
                    $logLevel
                );
                break;
            case LoggerInterface::LOG_TYPE_MAIL:
                $logger = new MailLogger(
                    $classname,
                    $logLevel,
                    $logTypeProperties['mailAdressFrom'],
                    $logTypeProperties['mailAdressTo']
                );
                break;
            case LoggerInterface::LOG_TYPE_CUSTOM_FILE:
                $logger = new CustomFileLogger(
                    $classname,
                    $logLevel,
                    $logTypeProperties['filename']
                );
                break;
            case LoggerInterface::LOG_TYPE_CONSOLE:
                $logger = new ConsoleLogger(
                    $classname,
                    $logLevel
                );
                break;

            default:
                throw new
                InvalidLogTypeException(
                    'Log type ' . $type .
                    ' defined in property file is not valid'
                );
        }
        // return the instance
        return $logger;
    }
}
<?php

namespace TechDivision\Logger;

use TechDivision\Logger\AbstractLogger;
use TechDivision\Logger\Exceptions\LoggerException;

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
class CustomFileLogger extends AbstractLogger
{

    /**
     * Holds the constant for the property value that holds the custom
     * file name to write the log messages to.
     * @var string
     */
    const LOG_CUSTOM_FILE = "log_custom_file";

    /**
     * Holds the custom log file path and name
     * @var string
     */
    private $logCustomFile = null;

    /**
     * The constructor initialize the logger instance with the
     * classname and the properties from the configuraion file.
     *
     * @param string $classname Holds the classname for log message
     * @param string $logLevel Holds loglevel which should be maximal logged
     * @return void
     */
    public function __construct(
        $classname, $logLevel, $filename)
    {
        // initialize the superclass
        AbstractLogger::__construct($classname, $logLevel);
        // get the custom log file name from the configuration file
        $this->logCustomFile = $filename;
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
     *        Is thrown if the log message can not be written to the custom file
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
                ) . PHP_EOL,
                CustomFileLogger::LOG_TYPE_CUSTOM_FILE,
                $this->logCustomFile
            );
            // check if the log message was written successfully
            if ($written === false) {
                // if not, throw an exception
                throw new LoggerException(
                    'Error while writing log message to custom file'
                );
            }
            // return the timestamp when the message was logged
            return $time;
        }
    }
}
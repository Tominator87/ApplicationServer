<?php

namespace TechDivision\MessagingServiceClient\Messages;

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
    private $_target = null;

    /**
     * The row counter for monitoring the message.
     * @var Integer
     */
    private $_rowCount = null;

    /**
     * The log message for monitoring the message.
     * @var String
     */
    private $_logMessage = null;

    /**
     * Initializes the queue with the name to use.
     * 
     * @param String $name Holds the queue name to use
     * @return void
     */
    public function __construct(Integer $target, String $logMessage) {
        $this->_target = $target;
        $this->_logMessage = $logMessage;
        $this->_rowCount = new Integer(0);
    }

    /**
     * Sets the log message.
     * 
     * @param String $logMessage The log message
     * @return void
     */
    public function setLogMessage(String $logMessage) {
        $this->_logMessage = $logMessage;
    }

    /**
     * Returns the row counter.
     * 
     * @param Integer $rowCount The row counter
     * @return void
     */
    public function setRowCount(Integer $rowCount) {
        $this->_rowCount = $rowCount;
    }

    /**
     * Returns the log message.
     * 
     * @return String The log message
     */
    public function getLogMessage() {
        return $this->_logMessage;
    }

    /**
     * Returns the row counter.
     * 
     * @return Integer The row counter
     */
    public function getRowCount() {
        return $this->_rowCount;
    }

    /**
     * Returns the target counter.
     * 
     * @return Integer The target counter
     */
    public function getTarget() {
        return $this->_target;
    }

}
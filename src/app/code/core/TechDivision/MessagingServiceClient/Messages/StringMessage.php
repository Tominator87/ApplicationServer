<?php

namespace TechDivision\MessagingServiceClient\Messages;

/**
 * The implementation for sending a message containing
 * data encapsulated in a String.
 * 
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
class StringMessage extends AbstractMessage {

    /**
     * The message id as hash value.
     * @var string
     */
    private $_messageid = null;

    /**
     * The message itself.
     * @var String
     */
    private $_message = null;

    /**
     * Initializes the message with the String
     * to send to the queue.
     * 
     * @param String $message The String with the data to send
     * @return void
     */
    public function __construct(String $message) {
        // initialize the String sent with the message
        $this->_message = $message;
        // initialize the message id
        $this->_messageId = md5(uniqid(rand(), true));
    }

    /**
     * Returns the message id.
     * 
     * @return string The message id as hash value
     */
    public function getMessageId() {
        return $this->_messageId;
    }

    /**
     * The message itself.
     * 
     * @return String The message itself
     */
    public function getMessage() {
        return $this->_message;
    }

    /**
     * Returns the message as string.
     * 
     * @return string The message as string
     */
    public function __toString() {
        return $this->_message->stringValue();
    }

}
<?php

namespace \TechDivision\MessageQueueClient\Messages;
 
/**
 * The implementation for sending a message containing
 * data encapsulated in a Integer.
 * 
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
class IntegerMessage extends AbstractMessage {

	/**
	 * The message id as hash value.
	 * @var string
	 */
	protected $messageId = null;
	
	/**
	 * The message itself.
	 * @var integer
	 */
	protected $message = null;

	/**
	 * Initializes the message with the Integer
	 * to send to the queue.
	 * 
	 * @param integer $message The Integer with the data to send
	 * @return void
	 */
	public function __construct($message) {
	    
	    if (is_integer($message)) {
	        
    		// initialize the Integer sent with the message
    		$this->message = $message;
    		
    		// initialize the message id
    		$this->messageId = md5(uniqid(rand(), true));
    		
    		return;
	    }
	    
	    throw new Exception("Message '$message' is not a valid integer");
	}

	/**
	 * Returns the message id.
	 * 
	 * @return string The message id as hash value
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * The message itself.
	 * 
	 * @return integer The message itself
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Returns the message as string.
	 * 
	 * @return string The message as string
	 */
	public function __toString() {
		return "'" . $this->message . "'";
	}
}
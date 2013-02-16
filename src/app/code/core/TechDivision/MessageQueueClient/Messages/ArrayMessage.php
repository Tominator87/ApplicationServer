<?php

namespace \TechDivision\MessageQueueClient\Messages;

use \TechDivision\MessageQueueClient\Messages\AbstractMessage;

/**
 * The implementation for sending a message containing
 * data encapsulated in an array.
 * 
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
class ArrayMessage extends AbstractMessage {

	/**
	 * The message id as hash value.
	 * @var string
	 */
	protected $messageId = null;
	
	/**
	 * The message itself.
	 * @var array
	 */
	protected $message = null;

	/**
	 * Initializes the message with the array
	 * to send to the queue.
	 * 
	 * @param array $message The array with the data to send
	 * @return void
	 */
	public function __construct(array $message) {
		// initialize the HashMap sent with the message
		$this->message = $message;
		// initialize the message id
		$this->messageId = md5(uniqid(rand(), true));
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
	 * @return array The message itself
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
		return serialize($this->message);
	}
}
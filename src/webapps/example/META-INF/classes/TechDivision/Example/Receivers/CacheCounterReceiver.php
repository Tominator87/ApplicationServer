<?php

/**
 * TechDivision\Example\Receivers\CacheCounterReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\Receivers;

use TechDivision\MessageQueueClient\Interfaces\Message;
use TechDivision\MessageQueueClient\Receiver\AbstractReceiver;
use TechDivision\MessageQueueClient\Messages\MessageMonitor;

/**
 * This is the dummy implementation of a message receiver.
 *
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class CacheCounterReceiver extends AbstractReceiver {
	
	/**
	 * @see MessageReceiver::onMessage(Message $message, $sessionId)
	 */
	public function onMessage(Message $message, $sessionId) {
		// log that a Message was received
		error_log($logMessage = "Successfully received / finished message");
		// initialize the message monitor
		$message->setMessageMonitor($monitor = new MessageMonitor(1, $logMessage));
		$monitor->setRowCount(1);
		// update the MessageMonitor
		$this->updateMonitor($message);
	}
}
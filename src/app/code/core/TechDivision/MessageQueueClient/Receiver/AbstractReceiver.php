<?php
	 
namespace TechDivision\MessageQueueClient\Receiver;
 
/**
 * The abstract superclass for all receivers.
 * 
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.4 $ $Date: 2009-01-03 13:11:54 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
abstract class AbstractReceiver implements MessageReceiver {
	
	/**
	 * The Worker that initialized the receiver.
	 * @var Worker
	 */
	protected $worker = null;
	
	/**
	 * Initializes the receiver with the initializing Worker.
	 *  
	 * @param Worker $worker The initializing Worker
	 * @return void
	 */
	public function __construct(Worker $worker = null) {
		// check if a Worker is passed
		if (!empty($worker)) { // if yes, set it
			$this->worker = $worker;
		}
	}
	
	/**
	 * Updates the message monitor over the 
	 * Worker's method.
	 * 
	 * @param Message $message The message to update the monitor for
	 * @return void
	 * @throws NullPointerException Is thrown if no Worker exists
	 */
	protected function updateMonitor(Message $message) {
		if (!empty($this->worker)) { 
		    // if a Worker exists update the monitor
			$this->worker->updateMonitor($message);
		} else { 
		    // else, throw an exception
			throw new NullPointerException("Necessary Worker does not exist");
		}
	}
}
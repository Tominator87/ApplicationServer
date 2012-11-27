<?php

namespace TechDivision\MessagingServiceClient\Messages;

/**
 * The abstract superclass for all messages.
 * 
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.4 $ $Date: 2009-01-03 13:11:54 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
abstract class AbstractMessage implements Message {

    /**
     * The unique session id.
     * @var string
     */
    protected $_sessionId = "";

    /**
     * The destination Queue to send the message to.
     * @var Queue
     */
    protected $_destination = null;

    /**
     * The parent message.
     * @var Message
     */
    protected $_parentMessage = null;

    /**
     * The monitor for monitoring the message.
     * @var MessageMonitor
     */
    protected $_messageMonitor = null;

    /**
     * The priority of the message, defaults to 'low'.
     * @var integer
     */
    protected $_priority = PriorityLow::KEY;

    /**
     * The state of the message, defaults to 'active'.
     * @var integer
     */
    protected $_state = MQStateActive::KEY;

    /**
     * The flag if the message should be deleted when finished or not.
     * @var boolean
     */
    protected $_locked = false;

    /**
     * @see Message::setSessionId($sessionId)
     */
    public function setSessionId($sessionId) {
        $this->_sessionId = $sessionId;
    }

    /**
     * @see Message::getSessionId()
     */
    public function getSessionId() {
        return $this->_sessionId;
    }

    /**
     * Sets the destination Queue.
     * 
     * @param Queue The destination
     * @return void
     */
    public function setDestination(Queue $destination) {
        $this->_destination = $destination;
    }

    /**
     * Returns the destination Queue.
     * 
     * @return Queue The destination Queue
     */
    public function getDestination() {
        return $this->_destination;
    }

    /**
     * Sets the priority of the message.
     * 
     * @param PriorityKey The priority to set the message to
     * @return void
     */
    public function setPriority(PriorityKey $priority) {
        $this->_priority = $priority->getPriority();
    }

    /**
     * Returns the priority of the message.
     * 
     * @return PriorityKey The priority of the message
     */
    public function getPriority() {
        return PriorityKeys::get($this->_priority);
    }

    /**
     * Sets the state of the message.
     * 
     * @param MQStateKey The new state 
     * @return void
     */
    public function setState(MQStateKey $state) {
        $this->_state = $state->getState();
    }

    /**
     * Returns the state of the message.
     * 
     * @return MQStateKey The message state
     */
    public function getState() {
        return MQStateKeys::get($this->_state);
    }

    /**
     * Sets the parent message.
     * 
     * @param Message $parentMessage The parent message
     * @return void
     */
    public function setParentMessage(Message $parentMessage) {
        $this->_parentMessage = $parentMessage;
    }

    /**
     * Returns the parent message.
     * 
     * @return Message The parent message
     * @see Message::getParentMessage()
     */
    public function getParentMessage() {
        return $this->_parentMessage;
    }

    /**
     * Sets the monitor for monitoring the
     * message itself.
     * 
     * @param MessageMonitor $messageMonitor The monitor
     * @return void
     */
    public function setMessageMonitor(MessageMonitor $messageMonitor) {
        $this->_messageMonitor = $messageMonitor;
    }

    /**
     * Returns the message monitor.
     * 
     * @return MessageMonitor The monitor
     * @see Message::getMessageMonitor()
     */
    public function getMessageMonitor() {
        return $this->_messageMonitor;
    }

    /**
     * Locks the message.
     * 
     * @return void
     */
    public function lock() {
        $this->_locked = true;
    }

    /**
     * Unlocks the message.
     *  
     * @return void
     */
    public function unlock() {
        $this->_locked = false;
    }

    /**
     * Returns the message lock flag.
     * 
     * @return boolean TRUE if the message is locked, else FALSE
     */
    public function isLocked() {
        return $this->_locked;
    }

}
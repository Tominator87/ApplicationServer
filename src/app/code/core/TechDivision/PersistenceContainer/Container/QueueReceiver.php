<?php

/**
 * TechDivision\PersistenceContainer\Container\QueueReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class QueueReceiver extends AbstractReceiver {
    
    /**
     * The queue as resource.
     * @var resource
     */
    protected $queue;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        
        // pass the container instance to the superclass
        parent::__construct($container);
        
        // use the port as key for the message queue
        $key = (integer) $this->getConfiguration()->getPort();
        
        // initialize the message queue to receive data
        $this->queue = msg_get_queue($key);
    }
    
    /**
     * Returns the socket instance used to send the data back to the client.
     * 
     * @return \TechDivision\Socket\Client
     */
    public function getQueue() {
        return $this->queue;
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {

        // start the ifinite loop and listen to clients
        while (true) {

            try {
                
                // initialize request and message type
                $line = '';
                $messageType = 0;
                
                // get the next request from the queue (blocking mode)
                msg_receive($this->getQueue(), 0, $messageType, 1024000, $line, false);

                // stack the request
                $this->stack($line);
                
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }
}
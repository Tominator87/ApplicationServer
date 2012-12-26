<?php

/**
 * TechDivision_PersistenceContainer_Container_QueueReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\ApplicationServer\Interfaces\ReceiverInterface;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class QueueReceiver implements ReceiverInterface {
    
    /**
     * The queue as resource.
     * @var resource
     */
    protected $queue;
    
    /**
     * The message type of messages to receive.
     * @var integer
     */
    protected $messageType = 0;
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Returns the refrence to the container instance.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container instance
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * Returns the receiver configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
     */
    public function getConfiguration() {
        return $this->getContainer()->getConfiguration()->getReceiver();
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {
        
        // use the port as key for the message queue
        $key = (integer) $this->getConfiguration()->getPort();
        
        // initialize the message queue to receive data
        $this->queue = msg_get_queue($key);

        // start the ifinite loop and listen to clients
        while (true) {

            try {
                
                // initialize the variable that contains the request
                $line = '';
                
                // get the next request from the queue (blocking mode)
                msg_receive($this->queue, 0, $this->messageType, 1024000, $line, false);

                // stack the request
                $this->getContainer()->stack($line);
                
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }
}
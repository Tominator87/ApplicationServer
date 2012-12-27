<?php

/**
 * TechDivision_PersistenceContainer_Container_QueueSender
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\ApplicationServer\Interfaces\SenderInterface;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class QueueSender implements SenderInterface {
    
    /**
     * The message queue resource
     * @var resource 
     */
    protected $queue;
    
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
     * Returns the sender configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
     */
    public function getConfiguration() {
        return $this->getContainer()->getConfiguration()->getSender();
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::prepare()
     */
    public function prepare($remoteMethod) {
        
        // initialize the queue
        $this->queue = msg_get_queue($remoteMethod->getPort());
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::sendLine()
     */
    public function sendLine($data) {
        msg_send($this->queue, 1, $data, false, true);
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::close()
     */
    public function close() {
        // do nothing here
    }
}
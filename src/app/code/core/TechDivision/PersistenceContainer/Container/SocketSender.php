<?php

/**
 * TechDivision_PersistenceContainer_Container_SocketSender
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\Interfaces\SenderInterface;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class SocketSender implements SenderInterface {
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * The socket instance use to send the data back to the client.
     * @var \TechDivision\Socket\Client 
     */
    protected $socket;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        
        // set the container
        $this->container = $container;
        
        // initialize the socket
        $this->socket = new Client();
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
     * Returns the socket instance used to send the data back to the client.
     * 
     * @return \TechDivision\Socket\Client
     */
    public function getSocket() {
        return $this->socket;
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

        // set port and address and send the data back to the client
        $this->getSocket()
             ->setAddress($remoteMethod->getAddress())
             ->setPort($remoteMethod->getPort())
             ->start();
        
        // return the sender instance
        return $this;
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::sendLine()
     */
    public function sendLine($data) {
        $this->getSocket()->sendLine(serialize($data));
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::close()
     */
    public function close() {
        $this->getSocket()->close();
    }
}
<?php

/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractContainer implements ContainerInterface {
    
    const CONFIGURATION_RECEIVER = '/container/receiver';
    
    const CONFIGURATION_PARAMETERS = '/container/receiver/params';
    
    const CONFIGURATION_STACKABLE = '/container/receiver/stackable';
    
    const CONFIGURATION_WORKER = '/container/receiver/worker';

    /**
     * Array with deployed applications.
     * @var array
     */
    protected $applications = array();

    /**
     * Initializes the server instance with the configuration.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration The container configuration
     * @return void
     */
    public function __construct($configuration) {

        // set the configuration
        $this->setConfiguration($configuration);

        // deploy applications
        $this->deploy();
    }

    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::start()
     */
    public function start() {
        $this->getReceiver()->start();
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::getReceiver()
     */
    public function getReceiver() {
        
        // create and return a new receiver instance
        return $this->newInstance($this->getReceiverType(), array($this));
    }
    
    /**
     * Sets the passed container configuration.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration for the container
     * @return \TechDivision\ServletContainer\Container The container instance itself
     * @todo Actually it's not possible to add interfaces as type hints for method parameters, this results in an infinite loop 
     */
    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Returns the actual container configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The actual container configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    public function getReceiverConfiguration() {
        return $this->getConfiguration()->getChilds(self::CONFIGURATION_RECEIVER);
    }
    
    public function getReceiverType() {
        return $this->getReceiverConfiguration()->getType();
    }
    
    public function getStackableType() {
        return $this->getConfiguration()->getChilds(self::CONFIGURATION_STACKABLE)->getType();
    }
    
    public function getWorkerType() {
        return $this->getConfiguration()->getChilds(self::CONFIGURATION_WORKER)->getType();
    }
    
    public function getParameters() {
        return $this->getConfiguration()->getChilds(self::CONFIGURATION_PARAMETERS);
    }
    
    /**
     * Returns an array with the deployed applications.
     * 
     * @return array The array with applications
     */
    public function getApplications() {
        return $this->applications;
    }
    
    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     * 
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array()) { 
        return InitialContext::get()->newInstance($className, $args);
    }
}
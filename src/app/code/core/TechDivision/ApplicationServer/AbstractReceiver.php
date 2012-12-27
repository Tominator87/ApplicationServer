<?php

/**
 * TechDivision_ApplicationServer_AbstractReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PersistenceContainer\Request;
use TechDivision\PersistenceContainer\RequestHandler;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ReceiverInterface;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractReceiver implements ReceiverInterface {
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * The number of parallel workers to handle client connections.
     * @var integer
     */
    protected $workerNumber = 1;

    /**
     * Array with the worker instances.
     * @var array
     */
    protected $workers = array();

    /**
     * Array for the incoming requests.
     * @var array
     */
    protected $work = array();
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        
        // set the container instance
        $this->container = $container;
        
        // load the receiver configuration
        $configuration = $this->getContainer()->getConfiguration()->getReceiver();

        // set the receiver configuration
        $this->setConfiguration($configuration);
        
        // set the configuration in the initial context
        InitialContext::get()->setAttribute(__CLASS__, $configuration);
        
        // enable garbage collector and check configuration
        $this->gcEnable()->checkConfiguration();
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
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::stack()
     */
    public function stack($line) {
        
        // unserialize the passed remote method
        $remoteMethod = unserialize($line);
        
        // check if a remote method has been passed
        if ($remoteMethod instanceof RemoteMethod) {
            // pass the line to the worker instance and process it
            $this->getRandomWorker()->stack($this->work[] = new Request($remoteMethod));
        } else {
            error_log('Invalid remote method call');           
        }

        // if garbage collection is enabled, force collection of cycles immediately
        if ($this->gcEnabled()) {
            error_log("Collected {$this->gc()} cycles");
        }

        // check of container configuration has to be reloaded
        $this->checkConfiguration();
    }

    /**
     * Returns a random worker.
     * 
     * @return \Worker The random worker instance
     */
    public function getRandomWorker() {
        
        // get a random worker number
        $randomWorker = rand(0, $this->getWorkerNumber() - 1);
        
        // check if the worker is already initialized
        if (!array_key_exists($randomWorker, $this->workers)) {
            $this->workers[$randomWorker] = new RequestHandler($this->getContainer());
            $this->workers[$randomWorker]->start();            
        }
        
        // return the random worker
        return $this->workers[$randomWorker];
    }

    /**
     * Set's the maximum number of workers to start.
     * 
     * @param integer $workerNumber The maximum number of worker's to start
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function setWorkerNumber($workerNumber) {
        $this->workerNumber = $workerNumber;
        return $this;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getWorkerNumber()
     */
    public function getWorkerNumber() {
        return $this->workerNumber;
    }
    
    /**
     * Sets the passed container configuration.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration for the container
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
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
    
    /**
     * Sets the new container configuration data.
     * 
     * @return void
     */
    public function reloadConfiguration() {
        $this->setWorkerNumber($this->getConfiguration()->getWorkerNumber());
    }
    
    /**
     * Check's if container configuration as changed, if yes, the 
     * configuration will be reloaded.
     * 
     * @return void
     */
    public function checkConfiguration() {
        
        // load the configuration from the initial context
        $nc = InitialContext::get()->getAttribute(__CLASS__);
        
        // check if configuration has changed
        if ($nc != null && !$this->getConfiguration()->equals($nc)) {
            $this->setConfiguration($nc)->reloadConfiguration();
        }
    }
    
    /**
     * Forces collection of any existing garbage cycles.
     * 
     * @return integer The number of collected cycles
     * @link http://php.net/manual/en/features.gc.collecting-cycles.php
     */
    public function gc() {
        return gc_collect_cycles();
    }
    
    /**
     * Returns TRUE if the PHP internal garbage collection is enabled.
     * 
     * @return boolean TRUE if the PHP internal garbage collection is enabled
     * @link http://php.net/manual/en/function.gc-enabled.php
     */
    public function gcEnabled() {
        return gc_enabled();
    }
    
    /**
     * Enables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-enable.php
     */
    public function gcEnable() {
        gc_enable();
        return $this;
    }
    
    /**
     * Disables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-disable.php
     */
    public function gcDisable() {
        gc_disable();
        return $this;
    }
}
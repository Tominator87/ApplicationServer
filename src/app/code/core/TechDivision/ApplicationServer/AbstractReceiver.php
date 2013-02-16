<?php

/**
 * TechDivision\ApplicationServer\AbstractReceiver
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
 * @package     TechDivision\ApplicationServer
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
    protected $workerNumber = 4;

    /**
     * Array with the worker instances.
     * @var array
     */
    protected $workers = array();
    
    /**
     * The worker type to use.
     * @var string
     */
    protected $workerType = '';
    
    /**
     * 
     * @var unknown
     */
    protected $stackableType = '';
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        
        // set the container instance
        $this->container = $container;
        
        // load the receiver configuration
        $configuration = $this->getContainer()->getReceiverConfiguration();

        // set the receiver configuration
        $this->setConfiguration($configuration);
        
        // set the configuration in the initial context
        InitialContext::get()->setAttribute(get_class($this), $configuration);
        
        // initialize configuration
        $this->checkConfiguration();
        
        // load the worker type
        $this->setWorkerType($this->getContainer()->getWorkerType());
            
        // load the stackable type
        $this->setStackableType($this->getContainer()->getStackableType());
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::stack()
     */
    public function stack(\Stackable $request) {
        
        // start a new worker and stack the request
        $this->newWorker()->stack($request);

        // check of container configuration has to be reloaded
        $this->checkConfiguration();
    }
    
    /**
     * Process the request by creating a new request instance (stackable)
     * and stack's it on one of the workers.
     * 
     * @return void
     */
    public function processRequest(\TechDivision\Socket $socket) {

        // create a new request instance
        $request = $this->newStackable(array($socket->getResource()));
        
        // initialize a new worker request instance
        $this->stack($request);   
    }
    
    /**
     * Create's and return's a new request instance (stackable) and
     * passes the the params to the constructor.
     * 
     * @param array $params Array with the params
     * @return \Stackable The request instance
     */
    public function newStackable($params) {
        return $this->newInstance($this->getStackableType(), $params);
    }

    /**
     * Returns a random worker.
     *
     * @return \Worker The random worker instance
     */
    public function newWorker($recursion = 0) {
        
        // get the maximum number of workers
        $workerNumber = $this->getWorkerNumber();

        // get a random worker number
        $randomWorker = rand(0, $workerNumber - 1);
        
        // check if the worker is already initialized
        if (array_key_exists($randomWorker, $this->workers) === false) {
            
            // initialize a new worker
            $this->workers[$randomWorker] = $this->newInstance($this->getWorkerType(), array($this->getContainer()));
            $this->workers[$randomWorker]->start();
            
        } else {
            
            if ($this->workers[$randomWorker]->isWorking() && $recursion < 10) {
                
                // raise number of allowed workers
                $this->setWorkerNumber($workerNumber++);
                
                // try to load another worker
                return $this->newWorker(++$recursion);
            }
        }

        // return the random worker
        return $this->workers[$randomWorker];
    }
    
    /**
     * Shutdown the receive by closing all workers.
     * 
     * @return void
     */
    public function shutdown() {
        foreach ($this->workers as $worker) {
            $worker->shutdown();
        }
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
     * Set's the worker's class name to use.
     * 
     * @param string $workerType The worker's class name to use
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function setWorkerType($workerType) {
        $this->workerType = $workerType;
        return $this;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getWorkerType()
     */
    public function getWorkerType() {
        return $this->workerType;
    }

    /**
     * Set's the stackable's class name to use.
     * 
     * @param string $stackableType The stackable's class name to use
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function setStackableType($stackableType) {
        $this->stackableType = $stackableType;
        return $this;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getStackableType()
     */
    public function getStackableType() {
        return $this->stackableType;
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
        $parameters = $this->getContainer()->getParameters();
        $this->setWorkerNumber((integer) $parameters->getWorkerNumber());
    }
    
    /**
     * Check's if container configuration as changed, if yes, the 
     * configuration will be reloaded.
     * 
     * @todo Refactor configuration reinitialization
     * @return void
     */
    public function checkConfiguration() {
        
        // load the configuration from the initial context
        $nc = InitialContext::get()->getAttribute(get_class($this));

        // check if configuration has changed
        if ($nc != null && !$this->getConfiguration()->equals($nc)) {
            $this->setConfiguration($nc)->reloadConfiguration();
        }
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
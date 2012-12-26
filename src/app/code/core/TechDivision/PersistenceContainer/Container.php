<?php

/**
 * TechDivision_PersistenceContainer_Container
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\PersistenceContainer\Request;
use TechDivision\PersistenceContainer\RequestHandler;
use TechDivision\PersistenceContainer\Application;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Container implements ContainerInterface {
    
    /**
     * XPath expression for the application configurations.
     * @var string
     */
    const XPATH_APPLICATIONS = '/appserver/applications/application';
    
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
     * Array with deployed applications.
     * @var array
     */
    protected $applications = array();

    /**
     * Array for the incoming requests.
     * @var array
     */
    protected $work = array();

    /**
     * Initializes the server instance with the storage.
     * 
     * @param integer $workerNumber Number of workers to start initially
     * @param string $address The container's IP address to listen to for incoming remote method calls
     * @param integer $port The container's port to listen to
     * @return void
     */
    public function __construct($configuration) {

        // set the configuration
        $this->setConfiguration($configuration);
        
        // set the configuration in the initial context
        InitialContext::get()->setAttribute(__CLASS__, $configuration);
        
        // set the number of workers to start
        $this->setWorkerNumber($configuration->getWorkerNumber());

        // enable garbage collector and deploy applications
        $this->gcEnable()->deploy();

        // create the worker instances
        for ($i = 0; $i < $this->getWorkerNumber(); $i++) {
            $this->workers[$i] = new RequestHandler($this);
            $this->workers[$i]->start();
        }
    }

    /**
     * Returns an array with available applications.
     * 
     * @return \TechDivision\Server The server instance
     * @todo Implement real deployment here
     */
    public function deploy() {

        // create the recursive directory iterator
        $di = new \RecursiveDirectoryIterator(getcwd() . '/app/code/local');
        
        // create the recursive iterator
        $it = new \RecursiveIteratorIterator($di);

        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {

            // check if file or subdirectory has been found
            if (!$it->isDot()) {

                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'appserver.xml') {

                    // initialize the SimpleXMLElement with the content of pointcut XML file
                    $sxe = new \SimpleXMLElement(file_get_contents($it->getSubPathName(), true));

                    // iterate over the found application nodes
                    foreach ($sxe->xpath(self::XPATH_APPLICATIONS) as $application) {

                        // load the application name and the path to the entities
                        $name = (string) $application->name;
                        $pathToEntities = (string) $application->pathToEntities;

                        // load the database connection information
                        foreach ($application->children() as $database) {
                            $connectionParameters = array(
                                'driver' => (string) $database->driver,
                                'user' => (string) $database->user,
                                'password' => (string) $database->password,
                                'dbname' => (string) $database->databaseName,
                            );
                        }

                        // initialize the application instance
                        $application = new Application($name);
                        $application->setConnectionParameters($connectionParameters);
                        $application->setPathToEntities(array($pathToEntities));

                        // add the application to the available applications
                        $this->applications[$application->getName()] = $application;
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }

        // return the server instance
        return $this;
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
        
        // load the receiver type from the configuration
        $receiverType = $this->getConfiguration()->getReceiver()->getType();
        
        // create and return a new receiver instance
        return $this->newInstance($receiverType, array($this));
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::getSender()
     */
    public function getSender() {

        // load the sender type from the configuration
        $senderType = $this->getConfiguration()->getSender()->getType();
        
        // create and return a new sender instance
        return $this->newInstance($senderType, array($this));
        
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerInterface::stack()
     */
    public function stack($line) {
                        
        // pass the line to the worker instance and process it
        $this->getRandomWorker()->stack($this->work[] = new Request($line));

        // if garbage collection is enabled, force collection of cycles immediately
        if ($this->gcEnabled()) {
            error_log("Collected {$this->gc()} cycles");
        }

        // check of container configuration has to be reloaded
        $this->checkConfiguration();
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
     * Sets the passed container configuration.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration for the container
     * @return \TechDivision\PersistenceContainer\Container The container instance itself
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

    /**
     * Set's the maximum number of workers to start.
     * 
     * @param integer $workerNumber The maximum number of worker's to start
     * @return \TechDivision\PersistenceContainer\Container The container instance
     */
    public function setWorkerNumber($workerNumber) {
        $this->workerNumber = $workerNumber;
        return $this;
    }
    
    /**
     * Return's the maximum number of workers to start.
     * 
     * @return integer The maximum number of worker's to start
     */
    public function getWorkerNumber() {
        return $this->workerNumber;
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
     * Returns a random worker.
     * 
     * @return \Worker The random worker instance
     */
    public function getRandomWorker() {
        
        // get a random worker number
        $randomWorker = rand(0, $this->getWorkerNumber() - 1);
        
        // check if the worker is already initialized
        if (!array_key_exists($randomWorker, $this->workers)) {
            $this->workers[$randomWorker] = new RequestHandler($this);
            $this->workers[$randomWorker]->start();            
        }
        
        // return the random worker
        return $this->workers[$randomWorker];
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
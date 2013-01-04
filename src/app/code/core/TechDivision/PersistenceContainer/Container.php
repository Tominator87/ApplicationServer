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
     * Array with deployed applications.
     * @var array
     */
    protected $applications = array();

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

        // deploy applications
        $this->deploy();
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
                        
                        $attributes = $application->attributes();
                        
                        $type = (string) $attributes['type'];
                        
                        if (empty($type)) {
                            $type = 'TechDivision\PersistenceContainer\Application';
                        } 

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
                        $application = $this->newInstance($type, array($name));
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
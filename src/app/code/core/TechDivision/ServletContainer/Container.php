<?php

/**
 * TechDivision\ServletContainer\Container
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Container implements ContainerInterface {

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
     * Returns an array with available applications.
     * 
     * @return \TechDivision\Server The server instance
     * @todo Implement real deployment here
     */
    public function deploy() {

        // gather all the deployed web applications
        foreach (new \FilesystemIterator(getcwd() . '/webapps') as $folder) {

            // check if file or subdirectory has been found
            if (is_dir($folder)) {

                // initialize the application name
                $name = basename($folder);

                // initialize the application instance
                $application = $this->newInstance('\TechDivision\ServletContainer\Application', array($name));
                $application->setWebappPath($folder->getPathname());

                // add the application to the available applications
                $this->applications[$application->getName()] = $application;
            }
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
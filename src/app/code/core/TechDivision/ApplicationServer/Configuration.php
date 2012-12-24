<?php

/**
 * TechDivision_ApplicationServer_Configuration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerConfiguration;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Configuration implements ContainerConfiguration {
    
    /**
     * The fully qualified class name of the container the configuration is for.
     * @var string
     */
    protected $containerType;
    
    /**
     * Maximum number of workers to start in the container.
     * @var integer
     */
    protected $workerNumber = 0;
    
    /**
     * The container's IP address to listen to.
     * @var string
     */
    protected $address = '0.0.0.0';
    
    /**
     * The container's port to listen to.
     * @var integer
     */
    protected $port = 8585;
    
    /**
     * Initializes the configuration with the container information necessary
     * to create and start a new instance.
     * 
     * @param string $containerType The fully qualified class name of the container the configuration is for
     * @param integer $workerNumber Maximum number of workers to start in the container
     * @param string $address The container's IP address to listen to
     * @param integer $port The container's port to listen to
     */
    public function __construct($containerType, $workerNumber = 1, $address = '0.0.0.0', $port = 8585) {
        $this->setContainerType($containerType);
        $this->setWorkerNumber($workerNumber);
        $this->setAddress($address);
        $this->setPort($port);
    }
    
    /**
     * Checks if the passed configuration is equal. If yes, the method
     * returns TRUE, if not FALSE.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration to compare to
     * @return boolean TRUE if the configurations are equal, else FALSE
     * @todo Actually it's not possible to add interfaces as type hints for method parameters, this results in an infinite loop 
     */
    public function equals($configuration) {
         return $this === $configuration;
    }
    
    /**
     * Set's the fully qualified class name of the container the 
     * configuration is for.
     * 
     * @param string The container type
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The configuration instance
     */
    public function setContainerType($containerType) {
        $this->containerType = $containerType;
        return $this;
    }
    
    /**
     * Returns the fully qualified container's class name.
     * 
     * @return string The container type 
     */
    public function getContainerType() {
        return $this->containerType;
    }
    
    /**
     * Set's the maximum number of workers to start in the container.
     * 
     * @param integer $workerNumber The maximum number of workers
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The configuration instance
     */
    public function setWorkerNumber($workerNumber) {
        $this->workerNumber = $workerNumber;
        return $this;
    }
    
    /**
     * Return's the maximum number of workers to start in the container.
     * 
     * @return integer The maximum number of workers
     */
    public function getWorkerNumber() {
        return $this->workerNumber;
    }
    
    /**
     * Set's the container's IP address to listen to.
     * 
     * @param string $address The IP address
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The configuration instance
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }
    
    /**
     * Return's the container's IP address to listen to.
     * 
     * @return string The container's IP address 
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * Set's the container's port to listen to.
     * 
     * @param string $address The IP address
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The configuration instance
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }
    
    /**
     * Return's the container's port to listen to.
     * 
     * @return integer The container's port
     */
    public function getPort() {
        return $this->port;
    }
}
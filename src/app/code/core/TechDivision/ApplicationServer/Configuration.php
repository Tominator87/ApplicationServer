<?php

/**
 * TechDivision\ApplicationServer\Configuration
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
     * The object type the configuration is related with.
     * @var string
     */
    protected $type;
    
    /**
     * The array with the child configurations.
     * @var array
     */
    protected $children = array();
    
    /**
     * The array with configuration parameters.
     * @var array
     */
    protected $data = array();
    
    /**
     * Initializes the configuration with the object type the
     * configuration is related with.
     * 
     * @param string $type The object type
     */
    public function __construct($type = 'root') {
        $this->type = $type;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration::equals()
     */
    public function equals($configuration) {
         return $this === $configuration;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration::getType()
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Adds a new child configuration.
     *
     * @param Configuration $configuration The child configuration itself
     * @return \TechDivision\ApplicationServer\Configuration The configuration instance
     */
    public function addChild($configuration) {
        $this->children[] = $configuration;
    }
    
    /**
     * Returns the child configuration with the passed type.
     * 
     * @param string $name The name of the configuration to return
     * @return Configuration The requested configuration
     */
    public function getChild($type) {
        foreach ($this->getChildren() as $child) {
            $reflectionClass = new \ReflectionClass($child->getType());
            if ($reflectionClass->implementsInterface($type)) {
                return $child;
            }
        }
    }
    
    /**
     * Returns all child configurations.
     * 
     * @return array The child configurations 
     */
    public function getChildren() {
        return $this->children;
    }
    
    /**
     * Adds the passed configuration value.
     * 
     * @param string $key Name of the configuration value
     * @param mixed $value The configuration value
     */
    public function setData($key, $value) {
        $this->data[$key] = $value;
    }
    
    /**
     * Returns the configuration value with the passed name.
     * 
     * @param string $key The name of the requested configuration value.
     * @return mixed The configuration value itself
     */
    public function getData($key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }
    
    /**
     * Wrapper method for getter/setter methods.
     * 
     * @param string $method The called method name
     * @param array $args The methods arguments
     * @return mixed The value if a getter has been invoked
     * @throws \Exception Is thrown if nor a getter/setter has been invoked
     */
    public function __call($method, $args) {
                
        // lowercase the first character of the member
        $key = lcfirst(substr($method, 3));
        
        // check if a getter/setter has been called
        switch (substr($method, 0, 3)) {
            case 'get':
                return $this->getData($key);
                break;
            case 'set':
                $this->setData($key, isset($args[0]) ? $args[0] : null);
                break;
            default:
                throw new \Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
        }
    }
    
    /**
     * Wrapper method for the sender configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The sender configuration
     */
    public function getSender() {
        return $this->getChild('\TechDivision\ApplicationServer\Interfaces\SenderInterface');
    }
    
    /**
     * Wrapper method for the receiver configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The sender configuration
     */
    public function getReceiver() {
        return $this->getChild('\TechDivision\ApplicationServer\Interfaces\ReceiverInterface');
    }
    
    /**
     * Wrapper method for the receiver's IP address.
     * 
     * @return string The receiver's IP address
     */
    public function getAddress() {
        return $this->getData('address');
    }
    
    /**
     * Wrapper method for the receiver's port.
     * 
     * @return string The receiver's port
     */
    public function getPort() {
        return $this->getData('port');
    }
    
    /**
     * Wrapper method to set the container's maximum worker number to start.
     * 
     * @param integer $workerNumber The maximum worker number
     * @return void
     */
    public function setWorkerNumber($workerNumber) {
        $this->setData('workerNumber', $workerNumber);
    }
    
    /**
     * Wrapper method for the container's maximum worker number to start.
     * 
     * @return integer The maximum worker number
     */
    public function getWorkerNumber() {
        return $this->getData('workerNumber');
    }
}
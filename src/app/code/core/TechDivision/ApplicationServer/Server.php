<?php

/**
 * TechDivision_ApplicationServer_Server
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\ContainerThread;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Server {
    
    /**
     * XPath expression for the container configurations.
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';
    
    /**
     * Initializes the containers found in the cfg/appserver.xml file.
     * 
     * @return array The array with the container configurations
     */
    public function loadConfigurations() {
        
        // initialize an array for the container configurations
        $configurations = array();
        
        // initialize the SimpleXMLElement with the content of pointcut XML file
        $sxe = simplexml_load_file('cfg/appserver.xml');

        // iterate over the found nodes
        foreach ($sxe->xpath(self::XPATH_CONTAINERS) as $container) {

            // load the application name and the path to the entities
            $configurationType = (string) $container->configurationType;

            // load the container initialization data
            foreach ($container->children() as $params) {
                $parameters = array(
                    'containerType' => (string) $params->containerType,
                    'workerNumber' => (integer) $params->workerNumber,
                    'host' => (string) $params->host,
                    'port' => (string) $params->port
                );
            }
            
            // create a new configuration instance
            $configuration = $this->newInstance($configurationType, $parameters);
            
            // add the configuration instance to the configurations
            $configurations[$configurationType] = $configuration;
        }
        
        // return the initialized configurations
        return $configurations;
    }
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
            
        // load the container configurations
        $configurations = $this->loadConfigurations();
        
        // start each container in his own thread
        foreach ($configurations as $configuration) {
            $thread = new ContainerThread($configuration);
            $thread->start();
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
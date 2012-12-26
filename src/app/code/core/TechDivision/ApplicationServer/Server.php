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

use TechDivision\ApplicationServer\Configuration;
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
     * The container configurations.
     * @var array
     */
    protected $configurations = array();
    
    /**
     * Initializes the containers found in the cfg/appserver.xml file.
     * 
     * @return array The array with the container configurations
     */
    public function loadConfigurations($parent, $sxe, $xpath) {

        // iterate over the found nodes
        foreach ($sxe->xpath($xpath) as $node) {
            
            // create a new configuration node
            $cnt = new Configuration((string) $node['type']);
            
            // load the container initialization data
            foreach ($node->children() as $name => $param) {
                
                // if params are specified, set the parameters
                if ($name == 'params') {
                    
                    // parse the params and add them to the configuration
                    foreach ($param as $key => $value) {
                        $methodName = 'set' . ucfirst($key);
                        $cnt->$methodName((string) $value);
                    }
                    
                } else {
                    // parse the configuration recursive
                    $this->loadConfigurations($cnt, $node, $name);
                }
            }
            
            // append the configuration node to the parent
            $parent->addChild($node->getName(), $cnt);
        }
    }
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
        
        // initialize the SimpleXMLElement with the content of pointcut XML file
        $sxe = simplexml_load_file('cfg/appserver.xml');
            
        // create a new root configuration node
        $cnt = new Configuration();
        
        // load the container configurations
        $this->loadConfigurations($cnt, $sxe, self::XPATH_CONTAINERS);
        
        // start each container in his own thread
        foreach ($cnt->getChildren() as $configuration) {
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
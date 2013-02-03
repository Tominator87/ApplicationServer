<?php

/**
 * TechDivision\ApplicationServer\Server
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
    const XPATH_CONTAINERS = '/appserver';
    
    /**
     * The container configurations.
     * @var array
     */
    protected $configurations = array();

    /**
     * Initialize the array for the running threads.
     * @var array
     */
    protected $threads = array();

    /**
     * Constructor to initialize the signal handlers for
     * a controlled shutdown of the server.
     * 
     * @return void
     */
    public function __construct() {
        /*
        // catch fatal error (rollback)
        register_shutdown_function(array($this, 'fatalErrorShutdown'));

        // catch Ctrl+C, kill and SIGTERM (rollback)
        pcntl_signal(SIGTERM, array($this, 'sigintShutdown'));
        pcntl_signal(SIGINT, array($this, 'sigintShutdown'));
        */
    }

    /**
     * Method to shutdown the threads wrapping each of the containers.
     * 
     * @return void
     */
    public function shutdown() {
    	
    	// iterate over all threads and stop each of them
        for ($i = 0; $i < sizeof($this->threads); $i++) {
            $this->threads[$i]->join();
        }
    	
        // stop the server and render a message
        die ("\nSuccessfully shutdown PHP Application Server");
    }

    /**
     * Method that is executed, when a fatal error occurs.
     *
     * @return void
     */
    public function fatalErrorShutdown() {
        $lastError = error_get_last();
        if (!is_null($lastError) && $lastError['type'] === E_ERROR) {
            $this->shutdown();
        }
    }

    /**
     * Method, that is executed, if script has been killed by:
     *
     * SIGINT: Ctrl+C
     * SIGTERM: kill
     *
     * @param int $signal
     */
    public function sigintShutdown($signal) {
        if ($signal === SIGINT || $signal === SIGTERM) {
            $this->shutdown();
        }
    }
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
        
        // initialize the SimpleXMLElement with the content XML configuration file
        $sxe = simplexml_load_file('cfg/appserver.xml');
        
        // load the container configurations
        $config = Configuration::loadFromFile('cfg/appserver.xml');
        
        // start each container in his own thread
        foreach ($config->getChilds('/appserver/containers/container') as $i => $configuration) {
            $this->threads[$i] = $this->newInstance('\TechDivision\ApplicationServer\ContainerThread', array($configuration));
            $this->threads[$i]->start();
        }

        // necessary for a controlled thread shutdown
        while (true) {
            sleep(1);
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
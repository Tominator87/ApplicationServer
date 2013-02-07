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

use TechDivision\Socket\Client;
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
        // catch Ctrl+C, kill and SIGTERM (rollback)
        pcntl_signal(SIGTERM, array($this, 'sigintShutdown'));
        pcntl_signal(SIGINT, array($this, 'sigintShutdown'));
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
        
        // initialize shutdown flag
        InitialContext::get()->setAttribute('shutdown', false);
        InitialContext::get()->setAttribute('shutdownComplete', false);
        
        // initialize the SimpleXMLElement with the content XML configuration file
        $sxe = simplexml_load_file('cfg/appserver.xml');
        
        // load the container configurations
        $this->configurations = Configuration::loadFromFile('cfg/appserver.xml');
        
        // start each container in his own thread
        foreach ($this->configurations->getChilds('/appserver/containers/container') as $i => $configuration) {
            $this->threads[$i] = $this->newInstance($configuration->getType(), array($configuration));
            $this->threads[$i]->start();
        }
        
        // shutdown after flag is set
        while (InitialContext::get()->getAttribute('shutdownComplete') === false) {
            sleep(1);
        }
    }

    /**
     * Method to shutdown the threads wrapping each of the containers.
     * 
     * @return void
     */
    public function shutdown() {
        
        // send signal to shutdown the server
        InitialContext::get()->setAttribute('shutdown', true);
        
        // send the shutdown request
        $this->sendShutdownRequest();
        
        // synchronize the threads
        for ($i = 0; $i < sizeof($this->threads); $i++) {
            $this->threads[$i]->join();
        }
        
        // send signal to shutdown the server
        InitialContext::get()->setAttribute('shutdownComplete', true);
    }

    /**
     * This method sends a shutdown request necessary to stop the 
     * inifinite loop in the receiver, because of a blocking socket.
     * 
     * @return void
     */
    public function sendShutdownRequest() {

        try {
            
            // send a shutdown request to all containers
            $containers = $this->configurations->getChilds('/appserver/containers/container');
            foreach ($containers as $container) {
                
                // load the containers socket information
                $params = current($container->getChilds('/container/receiver/params'));
                
                // send shutdown request
                $client = new Client($params->getAddress(), $params->getPort());
                $client->start()->setBlock();
                $client->sendLine(null);
                $client->readLine();        
            }

        } catch (\Exception $e) {
            error_log($e->__toString());
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
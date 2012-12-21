<?php

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

class Server {
    
    /**
     * Array with the initialized container instances.
     * @var array
     */
    protected $containers = array();

    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
        
        // initialize the containers
        $this->initContainers();
        
        // wait
        while (true) {
            sleep(1);
        }
    }
    
    /**
     * Adds the passed container to the Server.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container to add
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The initialized container instance
     */
    public function addContainer(ContainerInterface $container) {
        return $this->containers[get_class($container)] = $container;
    }
    
    /**
     * Initializes the containers found in the cfg/appserver.xml file.
     * 
     * @return \TechDivision\ApplicationServer\Server The server instance
     */
    public function initContainers() {
        
        // initialize the SimpleXMLElement with the content of pointcut XML file
        $sxe = new \SimpleXMLElement(file_get_contents('cfg/appserver.xml', true));

        // iterate over the found nodes
        foreach ($sxe->xpath('/appserver/containers/container') as $container) {

            // load the application name and the path to the entities
            $type = (string) $container->type;

            // load the container initialization data
            foreach ($container->children() as $params) {
                $parameters = array(
                    'workerNumber' => (integer) $params->workerNumber,
                    'host' => (string) $params->host,
                    'port' => (string) $params->port
                );
            }
            
            // create and start the container instance
            $containerInstance = $this->newInstance($type, $parameters);
            $containerInstance->start();
            
            // add the container to the server
            $this->addContainer($containerInstance);
            
            error_log("Successfully started container '$type'");
        }
        
        // return the instance itself
        return $this;
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
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}
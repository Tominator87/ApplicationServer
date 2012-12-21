<?php

namespace TechDivision\ApplicationServer;

use Doctrine\Common\ClassLoader;

class ContainerThread extends \Thread {
    
    protected $xpath;
    
    public function __construct($xpath) {
        $this->xpath = $xpath;
    }
    
    public function run() {
        
        // register class loader again, because we are in a thread
        $classLoader = new ClassLoader();
        $classLoader->register();
        
        $configuration = $this->getConfiguration();
        
        // load the container type
        $containerType = $configuration->getContainerType();
        
        error_log("Now starting container $containerType");
        
        // create and start the container instance
        $containerInstance = $this->newInstance($containerType, array($configuration));
        $containerInstance->start();
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
    
    /**
     * Initializes the containers found in the cfg/appserver.xml file.
     * 
     * @return \TechDivision\ApplicationServer\Server The server instance
     */
    public function getConfiguration() {
        
        // initialize the SimpleXMLElement with the content of pointcut XML file
        $sxe = new \SimpleXMLElement(file_get_contents('cfg/appserver.xml', true));

        // iterate over the found nodes
        foreach ($sxe->xpath($this->xpath) as $container) {

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
            
            // load the container configuration
            return $this->newInstance($configurationType, $parameters);
        }
    }
}
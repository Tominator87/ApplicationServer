<?php

namespace TechDivision\PersistenceContainer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServerClient\Proxy;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Container {

    /**
     * The singleton container instance
     * @var \TechDivision\PersistenceContainer\Container
     */
    protected static $_instance = null;
    
    /**
     * Array containing the deployed applications
     * @var array
     */
    protected $_applications = null;
    
    /**
     * The initial context instance
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $_initialContext = null;
    
    /**
     * Constructor that initializes the container and deploys all
     * applications found in the working directory.
     * 
     * @return void
     */
    public function __construct() {
        
        // intialize the initial context instance
        $this->_initialContext = new InitialContext();
    }
    
    /**
     * 
     */
    public function deploy() {

        error_log("Now starting deployment");
        
        // create the directory iterator
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(getcwd() . '/app/code/local')
        );
        
        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {
            
            if (!$it->isDot()) {
                
                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'appserver.xml') {
                    
                    // initialize the SimpleXMLElement with the content of pointcut XML file
                    $sxe = new \SimpleXMLElement(
                        file_get_contents($it->getSubPathName(), true)
                    );
                    
                    error_log("Now parsing file " . $it->getSubPathName());
                    
                    // iterate over the found nodes
                    foreach ($sxe->xpath('/appserver/applications/application') as $application) {
                        
                       $applicationName = (string) $application->name;
                       $pathToEntities = (string) $application->pathToEntities;
                    
                        error_log("Found application name $applicationName");
                        
                       foreach ($application->children() as $database) {
                        
                           $dbParams = array(
                               'driver'   => (string) $database->driver,
                               'user'     => (string) $database->user,
                               'password' => (string) $database->password,
                               'dbname'   => (string) $database->databaseName,
                           );
                       }
                       
                       $path = array($pathToEntities);

                       $config = Setup::createAnnotationMetadataConfiguration($path, true);

                       $entityManager = EntityManager::create($dbParams, $config);
                       
                       $applicationInstance = $this->newInstance('TechDivision\PersistenceContainer\Application', array($applicationName));
                       $applicationInstance->setInitialContext($this->getInitialContext());
                       $applicationInstance->setEntityManager($entityManager);

                       $this->addApplication($applicationInstance)->deploy();

                       error_log("Successfully initialized application $applicationName");
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }
        
        return $this;
    }

    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new Container();
        }
        return self::$_instance;
    }
    
    public function getInitialContext() {
        return $this->_initialContext;
    }
    
    public function newInstance($className, array $args = array()) {
        return $this->getInitialContext()->newInstance($className, $args);
    }
    
    public function addApplication($application) {
        return $this->_applications[$application->getName()] = $application;
    }
    
    public function getApplications() {
        return $this->_applications;
    }
    
    public function findApplication($className) {
        
        foreach ($this->getApplications() as $name => $application) {
            
            if (strpos($className, $name) !== false) {
                return $application;
            }
        }
        
        throw new \Exception("Can\'t find application for '$className'");
    }
    
    /**
     * 
     * @param type $className
     * @param type $sessionId
     * @param type $args
     * @return type
     */
    public function lookup($className, $sessionId, $args) {
        
        $reflectionClass = $this->getInitialContext()->newReflectionClass($className);
        
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Stateless')) {            
            return $reflectionClass->newInstanceArgs($args);
        }
        
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Statefull')) {
            
            $session = $this->getInitialContext()->getAttribute($sessionId);
            
            if (is_array($session)) {              
                if (array_key_exists($className, $session)) {
                    return $session[$className];
                }
            } else {
                $session = array();
            }
            
            $instance = $reflectionClass->newInstanceArgs($args);           
            $session[$className] = $instance;           
            $this->getInitialContext()->setAttribute($sessionId, $session);           
            return $instance;
        }
        
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Singleton')) {
            
            if ($this->getInitialContext()->getAttribute($className)) {
                return $this->getInitialContext()->getAttribute($className);
            }
            
            $instance = $reflectionClass->newInstanceArgs($args);            
            $this->getInitialContext()->setAttribute($className, $instance);           
            return $instance;
        }
    }

    /**
     * Invokes the passed remote method on the session bean
     * and returns the result.
     *  
     * @param \TechDivision\ApplicationServerClient\Interfaces\RemoteMethod $remoteMethod The remote method
     * @return mixed The result of the method invocation
     */
    public function handleRequest(RemoteMethod $remoteMethod) {
        
        // try to find the application
        $application = $this->findApplication($remoteMethod->getClassName());
        
        // load the remote method data 
        $sessionId = $remoteMethod->getSessionId();
        $className = $remoteMethod->getClassName();
        $methodName = $remoteMethod->getMethodName();
        $args = $remoteMethod->getParameters();
        
        // if a lookup has been requested return the proxy immediately
        if ($methodName == 'lookup') {
            return Proxy::create($className); 
        }
        
        // if not make a lookup for the session bean
        $instance = $this->lookup($className, $sessionId, array($application));
        
        // invoke the method on the session bean and return the result 
        return call_user_func_array(array($instance, $methodName), $args);   
    }
}
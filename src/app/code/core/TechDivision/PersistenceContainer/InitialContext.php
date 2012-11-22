<?php

namespace TechDivision\PersistenceContainer;

use TechDivision\ApplicationServerClient\Proxy;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
// use Doctrine\ORM\Tools\SchemaTool;

class InitialContext {

    protected static $_instance = null;
    
    protected $_applications = null;
    
    public function __construct() {
        
        // $config = Factory::fromDirectory('app/code/local');
        
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

                       /*
                       try {

                           $tool = new SchemaTool($entityManager);

                           $classes = array(
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\Assertion'),
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\Resource'),
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\Role'),
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\Rule'),
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\Sample'),
                               $entityManager->getClassMetadata('TechDivision\Example\Entities\User')
                           );

                           $tool->createSchema($classes);

                       } catch (\Exception $te) {
                           error_log($te->__toString());
                       }
                       */

                       $applicationInstance = $this->newInstance('TechDivision\PersistenceContainer\Application', array($applicationName));
                       $applicationInstance->setEntityManager($entityManager);

                       $this->addApplication($applicationInstance)->deploy();

                       error_log("Successfully initialized application $applicationName");
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }
    }

    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new InitialContext();
        }
        return self::$_instance;
    }
    
    public function newInstance($className, $params) {    
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($params);
    }
    
    public function addApplication($application) {
        return $this->_applications[$application->getName()] = $application;
    }
    
    public function getApplications() {
        return $this->_applications;
    }
    
    public function findApplication(RemoteMethod $remoteMethod) {
        
        foreach ($this->getApplications() as $name => $application) {
            
            if (strpos($remoteMethod->getClassName(), $name) !== false) {
            
                error_log("Found application for class name '{$remoteMethod->getClassName()}'");
                
                return $application;
            }
            
            error_log("Now comparing '$name' on class name '{$remoteMethod->getClassName()}'");
        }
        
        throw new \Exception("Can\'t find application for {$remoteMethod->getClassName()}");
    }

    public function handleRequest(RemoteMethod $remoteMethod) {

        $application = $this->findApplication($remoteMethod);

        $className = $remoteMethod->getClassName();
        $methodName = $remoteMethod->getMethodName();
        $parameters = $remoteMethod->getParameters();
        
        if ($methodName == 'lookup') {
            return Proxy::create($className); 
        }
        
        $instance = new $className($application);
        
        return call_user_func_array(array($instance, $methodName), $parameters);   
    }
}
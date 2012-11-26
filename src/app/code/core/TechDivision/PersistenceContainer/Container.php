<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision\PersistenceContainer
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServerClient\Proxy;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * The container holds the deployed applications with a reference
 * to the entity manager and the lookup for the session beans. 
 *
 * @package TechDivision\PersistenceContainer
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
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
        $this->_initialContext = new InitialContext();
    }
    
    /**
     * Deploys all applications found in the app/code/local folder.
     * 
     * @return \TechDivision\PersistenceContainer\Container The instance itself
     */
    public function deploy() {
        
        // create the directory iterator
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(getcwd() . '/app/code/local')
        );
        
        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {
            
            // check if file or subdirectory has been found
            if (!$it->isDot()) {
                
                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'appserver.xml') {
                    
                    // initialize the SimpleXMLElement with the content of pointcut XML file
                    $sxe = new \SimpleXMLElement(
                        file_get_contents($it->getSubPathName(), true)
                    );
                    
                    // iterate over the found nodes
                    foreach ($sxe->xpath('/appserver/applications/application') as $application) {
                       
                        // load the application name and the path to the entities
                       $applicationName = (string) $application->name;
                       $pathToEntities = (string) $application->pathToEntities;
                        
                       // load the database connection information
                       foreach ($application->children() as $database) {
                           $dbParams = array(
                               'driver'   => (string) $database->driver,
                               'user'     => (string) $database->user,
                               'password' => (string) $database->password,
                               'dbname'   => (string) $database->databaseName,
                           );
                       }
                       
                       // initialize the Doctrine EntityManager instance
                       $path = array($pathToEntities);
                       $config = Setup::createAnnotationMetadataConfiguration($path, true);
                       $entityManager = EntityManager::create($dbParams, $config);
                       
                       // create a new application instance and deploy it
                       $applicationInstance = $this->newInstance('TechDivision\PersistenceContainer\Application', array($applicationName));
                       $applicationInstance->setInitialContext($this->getInitialContext());
                       $applicationInstance->setEntityManager($entityManager);
                       $this->addApplication($applicationInstance)->deploy();
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }
        
        // finally return the instance itself
        return $this;
    }

    /**
     * The singleton method to get the instance.
     * 
     * @return \TechDivision\PersistenceContainer\Container The singleton instance
     */
    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new Container();
        }
        return self::$_instance;
    }
    
    /**
     * Returns the containers intial context.
     * 
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext() {
        return $this->_initialContext;
    }
    
    /**
     * Creates a new instance of the class with the passed name and passes
     * the also passed arguments to the constructor.
     * 
     * @param string $className The class name to create the instance for
     * @param array $args Array with the arguments to pass to the constructor
     * @return object The requested instance
     */
    public function newInstance($className, array $args = array()) {
        return $this->getInitialContext()->newInstance($className, $args);
    }
    
    /**
     * Adds the passed application instance to the container.
     * 
     * @param \TechDivision\PersistenceContainer\Application $application The application instance to add
     * @return \TechDivision\PersistenceContainer\Application The application instance
     */
    public function addApplication(Application $application) {
        return $this->_applications[$application->getName()] = $application;
    }
    
    /**
     * Returns an array with the initialized application instances.
     * 
     * @return array An array with all initialized applications instances
     */
    public function getApplications() {
        return $this->_applications;
    }
    
    /**
     * Tries to find and return the application for the passed class name.
     * 
     * @param string $className The name of the class to find and return the application instance
     * @return \TechDivision\PersistenceContainer\Application The application instance
     * @throws \Exception Is thrown if no application can be found for the passed class name
     */
    public function findApplication($className) {
        
        // iterate over all classes and check if the application name contains the class name
        foreach ($this->getApplications() as $name => $application) {
            if (strpos($className, $name) !== false) {
                // if yes, return the application instance
                return $application;
            }
        }
        
        // if not throw an exception
        throw new \Exception("Can\'t find application for '$className'");
    }
    
    /**
     * Run's a lookup for the session bean with the passed class name and 
     * session ID. If the passed class name is a session bean an instance
     * will be returned.
     * 
     * @param string $className The name of the session bean's class
     * @param string $sessionId The session ID
     * @param array $args The arguments passed to the session beans constructor
     * @return object The requested session bean
     * @throws \Exception Is thrown if passed class name is no session bean
     */
    public function lookup($className, $sessionId, array $args = array()) {
        
        // get the reflection class for the passed class name
        $reflectionClass = $this->getInitialContext()->newReflectionClass($className);
        
        // if the class is a stateless session bean simply return a new instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Stateless')) {            
            return $reflectionClass->newInstanceArgs($args);
        }
        
        // if the class is a statefull session bean, first check the container for a initialized instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Statefull')) {
            
            // load the session's from the initial context
            $session = $this->getInitialContext()->getAttribute($sessionId);
            
            // if an instance exists, load and return it
            if (is_array($session)) {              
                if (array_key_exists($className, $session)) {
                    return $session[$className];
                }
            } else {
                $session = array();
            }
            
            // if not, initialize a new instance, add it to the container and return it
            $instance = $reflectionClass->newInstanceArgs($args);           
            $session[$className] = $instance;           
            $this->getInitialContext()->setAttribute($sessionId, $session);           
            return $instance;
        }
        
        // if the class is a singleton session bean, return the singleton instance if available
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Singleton')) {
            
            // check if an instance is available
            if ($this->getInitialContext()->getAttribute($className)) {
                return $this->getInitialContext()->getAttribute($className);
            }
            
            // if not create a new instance and return it
            $instance = $reflectionClass->newInstanceArgs($args);            
            $this->getInitialContext()->setAttribute($className, $instance);           
            return $instance;
        }
        
        // if the class is no session bean, throw an exception
        throw new \Exception("Can\'t find session bean with class name '$className'");
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
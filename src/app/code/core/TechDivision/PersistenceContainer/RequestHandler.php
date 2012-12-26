<?php

/**
 * TechDivision_PersistenceContainer_RequestHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\SplClassLoader;
use TechDivision\Socket;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class RequestHandler extends \Worker {

    /**
     * A reference to the container instance.
     * 
     * @var \TechDivision\PersistenceContainer
     */
    protected $container;
    
    /**
     * Array with the available applications.
     * @var array
     */
    protected $applications;

    /**
     * Passes a reference to the container instance.
     * 
     * @param \TechDivision\PersistenceContainer\Container $container The container instance
     * @return void
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Returns the container instance.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * Returns the array with the available applications.
     * 
     * @return array The avaliable applications
     */
    public function getApplications() {
        return $this->applications;
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
     * @see \Worker::run()
     */
    public function run() {
        
        // register class loader again, because we are in a thread
        $classLoader = new SplClassLoader();
        $classLoader->register();
        
        // initialize the array for the applications
        $applications = array();
        
        // load the available applications from the container
        foreach ($this->getContainer()->getApplications() as $name => $application) {
            // set the applications and connect the entity manager
            $applications[$name] = $application->connect();
        }
        
        // set the applications in the worker instance
        $this->applications = $applications;
    }
 
    /**
     * Process the the request that has been passed as string.
     * 
     * @param string $line The serialized request
     * @return void
     */
    public function processRequest($line) {
        
        // register class loader again, because we are in a thread
        $classLoader = new SplClassLoader();
        $classLoader->register();
        
        // unserialize the passed remote method
        $remoteMethod = unserialize($line);
        
        // check if a remote method has been passed
        if ($remoteMethod instanceof RemoteMethod) {

            try {
                
                // load class name and session ID from remote method
                $className = $remoteMethod->getClassName();
                $sessionId = $remoteMethod->getSessionId();
                
                // load the referenced application from the server
                $application = $this->findApplication($className);
                
                // initialize the array with params to be passed to the session bean
                $args = array($application);
                
                // create inital context and lookup session bean
                $instance = InitialContext::get()->lookup($className, $sessionId, $args);

                // prepare method name and parameters and invoke method
                $methodName = $remoteMethod->getMethodName();
                $parameters = $remoteMethod->getParameters();

                // invoke the remote method call on the local instance
                $response = call_user_func_array(array($instance, $methodName), $parameters);
                
            } catch (\Exception $e) {                
                $response = new \Exception($e);
            }
            
            // load the sender instance
            $sender = $this->getContainer()->getSender($remoteMethod);

            try {
                
                // prepare the sender instance
                $sender->prepare($remoteMethod);
                
                // serialize the response
                $serializedResponse = serialize($response);
                
                // send the data back to the client
                $sender->send($serializedResponse . PHP_EOL);

                // close the sender immediately
                $sender->close();
                
            } catch (\Exception $e) {
                
                // log the stack trace
                error_log($e->__toString());
                
                // close the sender immediately
                $sender->close();
            }
            
        } else {
            
            error_log('Invalid remote method call');
            
        }
    }
}
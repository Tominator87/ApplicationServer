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
 * @package TechDivision\ApplicationServer
 */

namespace TechDivision\PersistenceContainer;

use Doctrine\Common\ClassLoader;
use TechDivision\Socket;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

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
        $classLoader = new ClassLoader();
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
        $classLoader = new ClassLoader();
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
                $instance = InitialContext::singleton()->lookup($className, $sessionId, $args);

                // prepare method name and parameters and invoke method
                $methodName = $remoteMethod->getMethodName();
                $parameters = $remoteMethod->getParameters();

                // invoke the remote method call on the local instance
                $response = call_user_func_array(array($instance, $methodName), $parameters);
                
            } catch (\Exception $e) {                
                $response = new \Exception($e);
            }

            // create a new socket
            $socket = new Socket();

            try {
                
                // serialize the response
                $serializedResponse = serialize($response);

                // set port and address and send the data back to the client
                $socket->setAddress($remoteMethod->getAddress())
                       ->setPort($remoteMethod->getPort())
                       ->create()
                       ->connect()
                       ->send($serializedResponse . PHP_EOL);

                // close the socket immediately
                $socket->close();
                
            } catch (\Exception $e) {
                
                // log the stack trace
                error_log($e->__toString());
                
                // close the socket immediately
                $socket->close();
            }
            
        } else {
            
            error_log('Invalid remote method call');
            
        }
    }
}
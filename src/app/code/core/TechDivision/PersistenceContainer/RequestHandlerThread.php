<?php

/**
 * TechDivision_PersistenceContainer_RequestHandlerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\SplClassLoader;
use TechDivision\Socket\Client;
use TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class RequestHandlerThread extends \Thread {

    /**
     * A reference to the container instance.
     * 
     * @var \TechDivision\PersistenceContainer
     */
    protected $container;

    /**
     * The client socket resource to return the response to.
     * @var resource
     */
    protected $socket;

    /**
     * Array with the available applications.
     * @var array
     */
    protected $applications;

    /**
     * Passes a reference to the container instance.
     * 
     * @param \TechDivision\PersistenceContainer\Container $container The container instance
     * @param resource $socket The client socket resource
     * @return void
     */
    public function __construct($container, $socket) {

        // set container and client socket resource
        $this->container = $container;
        $this->socket = $socket;

        // start the thread
        $this->start();
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
     * @return array The available applications
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
     * @see \Thread::run()
     */
    public function run() {

        try {

            // register class loader again, because we are in a thread
            $classLoader = new SplClassLoader();
            $classLoader->register();

            try {

                // initialize the array for the applications
                $applications = array();

                // load the available applications from the container
                foreach ($this->getContainer()->getApplications() as $name => $application) {
                    // set the applications and connect the entity manager
                    $applications[$name] = $application->connect();
                }

                // set the applications in the worker instance
                $this->applications = $applications;

                // initialize a new client socket
                $client = new Client();

                // set the client socket resource
                $client->setResource($this->socket);

                // read a line from the socket
                $line = $client->readLine();

                // read a line from the client and unserialize the passed remote method
                if (($remoteMethod = unserialize($line)) === false) {
                    throw new \Exception("Can't unserialize remote method '$line'");
                }

                // check if a remote method has been passed
                if (($remoteMethod instanceof RemoteMethod) === false) {
                    throw new \Exception('Invalid remote method call');
                }

                // load class name and session ID from remote method
                $className = $remoteMethod->getClassName();
                $sessionId = $remoteMethod->getSessionId();

                // load the referenced application from the server
                $application = $this->findApplication($className);

                // create initial context and lookup session bean
                $instance = $application->lookup($className, $sessionId);

                // prepare method name and parameters and invoke method
                $methodName = $remoteMethod->getMethodName();
                $parameters = $remoteMethod->getParameters();

                // invoke the remote method call on the local instance
                $response = call_user_func_array(array($instance, $methodName), $parameters);

            } catch (\Exception $e) {
                $response = new \Exception($e);
            }

            // send the data back to the client
            $client->sendLine(serialize($response));

            // close the socket immediately
            $client->close();

        } catch (\Exception $e) {

            // catch and log the exception
            error_log($e->__toString());
        }
    }
}
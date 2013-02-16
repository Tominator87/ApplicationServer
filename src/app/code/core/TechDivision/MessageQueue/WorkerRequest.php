<?php

/**
 * TechDivision\MessageQueue\WorkerRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\MessageQueue;

use TechDivision\Socket\Client;

/**
 * The stackable implementation that handles the request.
 * 
 * @package     TechDivision\MessageQueue
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class WorkerRequest extends \Stackable {
    
    /**
     * The message to process.
     * @var string
     */
    public $message;
    
    /**
     * Initializes the request with the message to process.
     * 
     * @param object $message The message to process
     * @return void
     */
    public function __construct($message) {
        $this->message = $message;
    }
    
    /**
     * Method that is executed, when a fatal error occurs.
     *
     * @return void
     */
    public function fatalErrorShutdown() {
        // nothing to do
    }
    
    /**
     * @see \Stackable::run()
     */
    public function run() {
            
        register_shutdown_function(array($this, 'fatalErrorShutdown'));

        // check if a worker is available
        if ($this->worker) {

            try {

                // load class name and session ID from remote method
                $className = $remoteMethod->getClassName();
                $sessionId = $remoteMethod->getSessionId();

                // load the referenced application from the server
                $application = $this->worker->findApplication($className);

                // create initial context and lookup session bean
                $instance = $application->lookup($className, $sessionId);

                // prepare method name and parameters and invoke method
                $methodName = $remoteMethod->getMethodName();
                $parameters = $remoteMethod->getParameters();

                // invoke the remote method call on the local instance
                call_user_func_array(array($instance, $methodName), $parameters);

            } catch (\Exception $e) {
                error_log($e->__toString());
            }
        }
    }
}
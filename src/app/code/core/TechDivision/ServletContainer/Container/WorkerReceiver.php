<?php

/**
 * TechDivision\ServletContainer\Container\WorkerReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Container;

use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\AbstractReceiver;
use TechDivision\ServletContainer\WorkerRequest;
use TechDivision\ServletContainer\RequestHandler;

/**
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class WorkerReceiver extends AbstractReceiver {
    
    /**
     * The socket instance use to send the data back to the client.
     * @var \TechDivision\Socket\Client 
     */
    protected $socket;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        
        // pass the container instance to the superclass
        parent::__construct($container);
        
        // initialize the socket
        $this->socket = new Client();
    }
    
    /**
     * Returns the socket instance used to send the data back to the client.
     * 
     * @return \TechDivision\Socket\Client
     */
    public function getSocket() {
        return $this->socket;
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {
        
        // load the socket instance
        $socket = $this->getSocket();
        
        // prepare the main socket and listen
        $socket->create()
               ->setAddress($this->getConfiguration()->getAddress())
               ->setPort($this->getConfiguration()->getPort())
               ->setBlock()
               ->setReuseAddr()
               ->bind()
               ->listen();

        // start the infinite loop and listen to clients (in blocking mode)
        while ($client = $socket->accept()) {

            try {

                // initialize a new worker request instance
                $request = new WorkerRequest($client->getResource());

                // start a new worker and stack the request
                $this->getRandomWorker()->stack($this->work[] = $request);
                
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }

    /**
     * Returns a random worker.
     *
     * @return \Worker The random worker instance
     */
    public function getRandomWorker() {

        // get a random worker number
        $randomWorker = rand(0, $this->getWorkerNumber() - 1);

        // check if the worker is already initialized
        if (!array_key_exists($randomWorker, $this->workers)) {
            $this->workers[$randomWorker] = new RequestHandler($this->getContainer());
            $this->workers[$randomWorker]->start();
        }

        // return the random worker
        return $this->workers[$randomWorker];
    }
}
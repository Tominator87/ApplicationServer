<?php

/**
 * TechDivision_PersistenceContainer_Container_SocketReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class SocketReceiver extends AbstractReceiver {
    
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
               ->setReceiveTimeout()
               ->bind()
               ->listen();

        // start the ifinite loop and listen to clients
        while (true) {

            try {

                // prepare array of readable client sockets
                $read = array($socket->getResource());

                // prepare the array for write/except sockets
                $write = $except = array();

                // select a socket to read from
                $socket->select($read, $write, $except);

                // if ready contains the master socket, then a new connection has come in
                if (in_array($socket->getResource(), $read)) {

                    // initialize the buffer
                    $buffer = '';

                    // load the character for line ending
                    $newLine = $socket->getNewLine();

                    // get the client socket (in blocking mode)
                    $client = $socket->accept();

                    // read one line (till EOL) from client socket
                    while ($buffer .= $client->read($socket->getLineLength())) {
                        if (substr($buffer, -1) === $newLine) {
                            $line = rtrim($buffer, $newLine);
                            break;
                        }
                    }

                    // close the client socket if no more data will be transmitted
                    if ($line == null) {
                        $client->close();
                    } else {
                        $this->stack($line);
                    }
                }
                
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }
}
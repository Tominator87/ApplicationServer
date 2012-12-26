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
use TechDivision\ApplicationServer\Interfaces\ReceiverInterface;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class SocketReceiver extends Client implements ReceiverInterface {
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * Sets the reference to the container instance.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Returns the refrence to the container instance.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container instance
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * Returns the receiver configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
     */
    public function getConfiguration() {
        return $this->getContainer()->getConfiguration()->getReceiver();
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {

        // prepare the main socket and listen
        $this->create()
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
                $read = array($this->resource);

                // prepare the array for write/except sockets
                $write = $except = array();

                // select a socket to read from
                $this->select($read, $write, $except);

                // if ready contains the master socket, then a new connection has come in
                if (in_array($this->resource, $read)) {

                    // initialize the buffer
                    $buffer = '';

                    // load the character for line ending
                    $newLine = $this->getNewLine();

                    // get the client socket (in blocking mode)
                    $client = $this->accept();

                    // read one line (till EOL) from client socket
                    while ($buffer .= $client->read($this->getLineLength())) {
                        if (substr($buffer, -1) === $newLine) {
                            $line = rtrim($buffer, $newLine);
                            break;
                        }
                    }

                    // close the client socket if no more data will be transmitted
                    if ($line == null) {
                        $client->close();
                    } else {
                        $this->getContainer()->stack($line);
                    }
                    
                }
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }
}
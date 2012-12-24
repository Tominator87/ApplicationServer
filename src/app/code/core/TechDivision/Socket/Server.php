<?php

/**
 * TechDivision_Socket_Server
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Socket;

use TechDivision\Socket\Client;

/**
 * The server socket implementation.
 *
 * @package     TechDivision\Socket
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Server extends Client {
    
    public function start() {
        
        $this->create()
             ->setBlock()
             ->setReuseAddr()
             ->setReceiveTimeout()
             ->bind()
             ->listen();
        
        return $this;
    }
    
    public function readLine() {
        
        // prepare array of readable client sockets
        $read = array($this->resource);
        
        $write = $except = array();
        
        $this->select($read, $write, $except);

        // if ready contains the master socket, then a new connection has come in
        if (in_array($this->resource, $read)) {
            
            // initialize the buffer
            $buffer = '';
        
            $newLine = $this->getNewLine();
            
            $client = $this->accept();
            
            while ($buffer .= $client->read($this->getLineLength())) {
                if (substr($buffer, -1) === $newLine) {
                    return rtrim($buffer, $newLine);
                }
            }
        }
    }
}
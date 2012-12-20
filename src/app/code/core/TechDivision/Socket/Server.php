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
 * @package TechDivision
 */

namespace TechDivision\Socket;

use TechDivision\Socket\Client;

/**
 * The socket implementation.
 *
 * @package TechDivision\Socket
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
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
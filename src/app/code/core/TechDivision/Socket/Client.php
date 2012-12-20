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
 * @package TechDivision\Socket
 */

namespace TechDivision\Socket;

use TechDivision\Socket;

/**
 * The socket implementation.
 *
 * @package TechDivision\Socket
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class Client extends Socket {
    
    protected $lineLength = 2048;
    
    protected $newLine = PHP_EOL;

    public function __construct($address, $port = 0) {
        
        parent::__construct();
        
        $this->address = $address;
        $this->port = $port;
    }
    
    public function getLineLength() {
        return $this->lineLength;
    }
    
    public function getNewLine() {
        return $this->newLine;
    }
    
    public function start() {
        return $this->create()->connect();
    }
    
    public function sendLine($data) {
        return $this->send($data . PHP_EOL);
    }
    
    public function readLine() {
            
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
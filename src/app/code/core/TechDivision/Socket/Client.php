<?php

/**
 * TechDivision_Socket_Client
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Socket;

use TechDivision\Socket;

/**
 * The client socket implementation.
 *
 * @package     TechDivision\Socket
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Client extends Socket {
    
    protected $lineLength = 2048;
    
    protected $newLine = "\n";

    public function __construct($address = '0.0.0.0', $port = 0) {
        
        $this->setAddress($address);
        $this->setPort($port);
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
        return $this->send($data . $this->getNewLine());
    }
    
    public function readLine() {
            
        // initialize the buffer
        $buffer = '';

        $newLine = $this->getNewLine();

        while ($buffer .= $this->read($this->getLineLength())) {

            if (substr($buffer, -1) === $newLine) {
                return rtrim($buffer, $newLine);
            }
        }
    }
}
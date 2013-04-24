<?php

/**
 * TechDivision\Socket\Server
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Socket;

use TechDivision\ServletContainer\Http;
use TechDivision\Socket\Client;
use TechDivision\ServletContainer\Http\Request;


/**
 * The server socket implementation.
 *
 * @package     TechDivision\Socket
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Philipp Dittert <pd@techdivision.com>
 */
class HttpClient extends Client {

    protected $_httpServerRequest;

    public function __construct($address = '0.0.0.0', $port = 0){
        parent::__construct($address,$port);
    }

    public function receive(){
        $buffer = '';
        while ($buffer .= $this->read($this->getLineLength())) {
            $req = Request::parse($buffer);
            if($req->isValid()){
                return $req;
            }
        }
    }

    public function readLine() {
        return $this->receive();
    }



}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: philipp
 * Date: 02.04.13
 * Time: 20:20
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\Socket;

use TechDivision\ServletContainer\Http;
use TechDivision\Socket\Client;
use TechDivision\ServletContainer\Http\Request;

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
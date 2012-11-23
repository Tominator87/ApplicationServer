<?php

namespace TechDivision\StreamSocket;

use TechDivision\StreamSocket\Interfaces\Socket;
use TechDivision\StreamSocket\Exceptions\StreamSocketException;

/**
 * StreamSocket implementation for data transfer via streams.
 *
 * @package TechDivision\StreamSocket
 * @author Johann Zelger <j.zelger@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */

class StreamSocket implements Socket {

    /**
     * holds stream socket resource
     */
    protected $_socket = null;

    public function __construct() {
        // nothing yet
    }

    /**
     * Opens a stream socket server port
     *
     * @param $url string
     * @throws
     * @return void
     */
    public function listen($url)
    {
        // check if socket is initiated already
        if (!$this->_socket) {
            $this->_socket = stream_socket_server($url, $errno, $errstr);
            if ($this->_socket === false) {
                throw StreamSocketException($errstr, $errno);
            }
        } else {
            throw StreamSocketException('socket resource already exists.');
        }
    }

    /**
     * Connects to a open stream socket server port
     *
     * @param $url string
     * @param $timeout int
     * @throws StreamSocketException
     * @return void
     */
    public function connect($url, $timeout = 30)
    {
        // check if socket is initiated already
        if (!$this->_socket) {
            $this->_socket = stream_socket_client($url, $errno, $errstr, $timeout);
            if ($this->_socket === false) {
                throw StreamSocketException($errstr, $errno);
            }
        }
    }

}
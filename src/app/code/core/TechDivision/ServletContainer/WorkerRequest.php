<?php

/**
 * TechDivision\ServletContainer\WorkerRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer;

use TechDivision\Socket\Client;
use TechDivision\ServletContainer\Servlets\StaticResourceServlet;
use TechDivision\ServletContainer\Http\HttpServletResponse;
use TechDivision\ServletContainer\Http\HttpServletRequest;

/**
 * The stackable implementation that handles the request.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class WorkerRequest extends \Stackable {

    /**
     * The client socket resource.
     * @var string
     */
    public $resource;

    /**
     * Initializes the request with the client socket.
     *
     * @param resource $resource The client socket instance
     * @return void
     */
    public function __construct($resource) {
        $this->resource = $resource;
    }

    /**
     * @see \Stackable::run()
     */
    public function run() {

        // check if a worker is available
        if ($this->worker) {

            // initialize a new client socket
            $client = new Client();

            // set the client socket resource
            $client->setResource($this->resource);

            // read a line from the client
            $line = $client->readLine();

            try {

                // initialize response container
                $response = new HttpServletResponse();

                // instanciate request and response containers
                $request = HttpServletRequest::factory($line);

                // load the application to handle the request
                $application = $this->worker->findApplication($request);

                // try to locate a servlet which could service the current request
                if (($servlet = $application->locate($request)) === false) {

                    // if no servlet could be located for the request, use fallback
                    $servlet = new StaticResourceServlet();
                }

                // let the servlet process the request and store the result in the response
                $servlet->service($request, $response);

            } catch (\Exception $e) {

                ob_start();

                debug_print_backtrace();

                $response->setContent(get_class($e) . "\n\n" . $e->getMessage() . "\n\n" . ob_get_clean());
            }

            // prepare the headers
            $headers = $this->prepareHeader($response);

            // return the string representation of the response content to the client
            $client->send($headers . "\r\n\r\n" . $response->getContent());

            // close the socket connection to the client
            $client->close();

            // notify the calling thread
            $this->notify();
        }
    }

    /**
     * Prepares the headers for the given response and returns them.
     *
     * @param \TechDivision\ServletContainer\Interfaces\ServletResponse $response The response to prepare the header for
     * @return string The headers
     * @todo This is a dummy implementation, headers has to be handled in request/response
     */
    public function prepareHeader($response) {

        // prepare the content length
        $contentLength = strlen($response->getContent());

        // prepare the headers
        $headers = '';
        $headers .= "HTTP/1.1 200 OK\r\n";
        $headers .= "Date: " . gmdate('D, d M Y H:i:s \G\M\T', time()) . "\r\n";
        $headers .= "Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', time()) . "\r\n";
        $headers .= "Expires: " . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600) . "\r\n";
        $headers .= "Server: Apache/1.3.29 (Unix) PHP/5.4.10\r\n";
        $headers .= "Content-Length: $contentLength\r\n";
        $headers .= "Content-Language: de\r\n";
        $headers .= "Connection: close\r\n";
        $headers .= "Content-Type: text/html";

        // return the headers
        return $headers;
    }
}
<?php

namespace TechDivision\ServletContainer\HTTP;

use TechDivision\ServletContainer\Exceptions\BadRequestException;
use TechDivision\ServletContainer\ServletLocator;
use TechDivision\ServletContainer\HttpResponse;
use TechDivision\ServletContainer\HttpRequest;

use TechDivision_Lang_String as String;

class RequestHandler
{
    public $servletContainer;

    public $servletLocator;

    public function __construct()
    {
        // register the servlet locator
        $this->servletLocator = new ServletLocator();
    }

    /**
     * @return \Net_Server_Driver_Sequential
     */
    protected function getServerReference()
    {
        return $this->_server;
    }

    /**
     * @param int    $clientId
     * @param string $data
     * @throws BadRequestException
     * @return null|void
     */
    public function onReceiveData($clientId = 0, $data = '')
    {
        // initialize response container
        $response = new HttpServletResponse();

        try {
            // instanciate request and response containers
            $request = HttpServletRequest::factory(new String($data));

            // try to locate a servlet which could service the current request
            if (!$servlet = $this->servletLocator->locate($request)) {
                // if no servlet could be located for the request, use fallback
                $servlet = new StaticResourceServlet();
            }

            // let the servlet process the request and store the result in the response
            $servlet->service($request, $response);

        } catch (\Exception $e) {
            header_status(500);
            $response->setContent(new String(get_class($e) . "\n\n" . $e->getMessage() . "\n\n" . ob_get_clean()));
        }

        // return the string representation of the response content to the client
        $this->getServerReference()->sendData($clientId, (string)$response->getContent());

        // close the socket connection to the client
        $this->getServerReference()->closeConnection($clientId);
    }

}

<?php

namespace TechDivision\ServletContainer\Service\Locator;

use TechDivision\ServletContainer\Interfaces\LocatorInterface;
use TechDivision\ServletContainer\Interfaces\ServerInterface;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Servlet;

use TechDivision\ServletContainer\ServletManager;

use TechDivision_Lang_String as String;

class ServletLocator implements LocatorInterface
{
    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @param ServerInterface $server
     * @return void
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * @return ServerInterface
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param Request $request
     * @internal param Request $identifier
     * @return Servlet
     */
    public function locate(Request $request)
    {
        /** @var $request HttpServletRequest */
        // build the file-path of the request
        $path    = $request->getRequestUrl();
        $servlet = FALSE;

        // retrieve the registered servlets
        $servlets = ServletManager::instance()->getServlets();

        // traverse the path to find matching segment
        do {
            if ($servlets->exists(new String($path))) {
                $servlet = $servlets->get(new String($path));
                break;
            }

            $path = substr($path, 0, strrpos($path, '/'));

        } while (strpos($path, '/') !== FALSE);

        return $servlet;

    }
}

<?php

namespace TechDivision\ServletContainer\Interfaces;

use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\ServerInterface;

interface LocatorInterface
{
    /**
     * @param ServerInterface $server
     * @return mixed
     */
    public function setServer(ServerInterface $server);

    /**
     * @return ServerInterface
     */
    public function getServer();

    /**
     * @param Request $request
     * @return mixed
     */
    public function locate(Request $request);
}

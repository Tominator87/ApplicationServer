<?php

namespace TechDivision\ServletContainer\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;

abstract class GenericServlet implements Servlet
{
    /**
     * @param ServletConfig $config
     * @throws ServletException;
     * @return mixed
     */
    public function init(ServletConfig $config = null)
    {
        // TODO: Implement init() method.
    }

    /**
     * @return ServletConfig
     */
    public function getServletConfig()
    {
        // TODO: Implement getServletConfig() method.
    }

    /**
     * @return mixed|void
     */
    public function getServletInfo()
    {
        // TODO: Implement getServletInfo() method.
    }

    /**
     * @return mixed|void
     */
    public function destroy()
    {
        // TODO: Implement destroy() method.
    }
}

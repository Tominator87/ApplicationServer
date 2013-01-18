<?php

namespace TechDivision\ExampleWebapp\Servlets;

/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 05.10.12
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Interfaces\ServletResponse;

class HelloWorldServlet extends HttpServlet implements Servlet {

    public function __construct() {
        error_log(__METHOD__);
    }

    /**
     * @param TechDivision\ServletContainer\ServletConfig $config
     * @return mixed|void
     */
    public function init(ServletConfig $config = null) {

        return;

        error_log(__METHOD__);

        // simulate some heavy initialization logic
        for($i=0; $i <= 3; $i++) {
            sleep(1);
            error_log(__METHOD__ . " - " . $i);
        }
    }

    /**
     * @param ServletRequest $req
     * @param ServletResponse $res
     */
    public function doGet(ServletRequest $req, ServletResponse $res) {

        $content = 'Hello World!!!';

        $res->setContent('
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
                <head></head>
                <body>
                <h1>' . $content . '</h1>
                </body>
            </html>
        ');
    }

}

<?php

namespace TechDivision\Example\Servlets;

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

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

class HelloWorldServlet extends HttpServlet implements Servlet {

    public function __construct() {
        error_log(__METHOD__);
    }

    /**
     * @param TechDivision\ServletContainer\ServletConfig $config
     * @return mixed|void
     */
    public function init(ServletConfig $config = null) {

        error_log(__METHOD__);

        return;
    }

    public function doPost(ServletRequest $req, ServletResponse $res) {

        /** @var $req \TechDivision\ServletContainer\Http\HttpServletRequest */
        $args = $req->getRequestParameterMap();

        $connection = Factory::createContextConnection();
        $session = $connection->createContextSession();
        $initialContext = $session->createInitialContext();

        // lookup the remote processor implementation
        $processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');

        if (array_key_exists('action', $args)) {
            $action = $args['action'];
        } else {
            $action = 'findAll';
        }

        $sampleId = '';
        $name = '';

        switch ($action) {
            case 'persist':
                $entity = new Sample();
                $entity->setSampleId((integer) $args['sampleId']);
                $entity->setName($args['name']);
                $processor->persist($entity);
                $entities = $processor->findAll();
                break;
            case 'changeWorker':
                $processor->changeWorker($args['workers']);
                $entities = $processor->findAll();
                break;
            default:
                $entities = $processor->findAll();
                error_log("Found " . sizeof($entities) . " entities");
                break;
        }

        $res->setContent('<meta http-equiv="refresh" content="0;URL=\'?action=findAll\'">');
    }

    /**
     * @param ServletRequest $req
     * @param ServletResponse $res
     */
    public function doGet(ServletRequest $req, ServletResponse $res) {

        /** @var $req \TechDivision\ServletContainer\Http\HttpServletRequest */
        $args = $req->getRequestParameterMap();

        $connection = Factory::createContextConnection();
        $session = $connection->createContextSession();
        $initialContext = $session->createInitialContext();

        // lookup the remote processor implementation
        $processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');

        switch ($args['action']) {
            case 'createSchema':
                $processor->createSchema();
                $entities = $processor->findAll();
                break;
            case 'load':
                $entity = $processor->load($args['sampleId']);
                $name = $entity->getName();
                $sampleId = $entity->getSampleId();
                $entities = $processor->findAll();
                break;
            case 'delete':
                $entities = $processor->delete($args['sampleId']);
                break;
            default:
                $entities = $processor->findAll();
                error_log("Found " . sizeof($entities) . " entities");
                break;
        }

        $content = array();

        foreach ($entities as $entity) {
            $content[] = $this->getTableRow($entity);
        }

        $res->setContent('
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
                <head></head>
                <body>
                    <div>
                        <ul>
                            <li><a href="/example/hello-world.do?action=findAll">Home</a></li>
                            <li><a href="/example/hello-world.do?action=findAll">Script version</a></li>
                        </ul>
                    </div>
                    <div>
                        <form action="/example/hello-world.do" method="post">
                            <input type="hidden" name="action" value="persist" />
                            <fieldset>
                                <legend>Sample</legend>
                                <table><tr>
                                        <td>Id:</td>
                                        <td><input type="text" size="40" maxlength="40" name="sampleId" value=""></td>
                                    </tr><tr>
                                        <td>Name:</td>
                                        <td><input type="text" size="40" maxlength="40" name="name" value=""></td>
                                    </tr><tr>
                                        <td colspan="2"><input type="submit" value="Save"></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </form>
                    </div>
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <td>Id</td>
                                    <td>Name</td>
                                    <td>Actions</td>
                                </tr>
                            </thead>
                            <tbody>' . implode(PHP_EOL, $content) . '</tbody>
                        </table>
                    </div>
                </body>
            </html>
        ');
    }

    public function getTableRow($entity) {

        $sampleId = $entity->getSampleId();

        $hrefEdit = '/example/hello-world.do?action=load&sampleId=' . $sampleId;
        $hrefDelete = '/example/hello-world.do?action=delete&sampleId=' . $sampleId;

        return '<tr><td><a href="' . $hrefEdit . '">' . $sampleId . '</a></td><td>' . $entity->getName() . '</td><td><a href="' . $hrefDelete . '">Delete</a></td></tr>';
    }

}

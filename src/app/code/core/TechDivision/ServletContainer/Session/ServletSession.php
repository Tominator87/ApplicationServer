<?php
/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 07.04.13
 * Time: 00:57
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\ServletContainer\Session;


use TechDivision\PersistenceContainer\Interfaces\Statefull;
use TechDivision\ServletContainer\Application;
use TechDivision\ServletContainer\Session;

class ServletSession implements Session, Statefull {

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Application
     */
    protected $application;

    protected $test;

    /**
     * @param $sessionId
     */
    public function __construct($application)
    {
        $this->setApplication($application);
    }

    /**
     * Returns the ID of the session.
     *
     * @param void
     * @return string The session ID
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Sets the ID of the session.
     *
     * @param string $sessionId
     * @return void
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function setTest($test) {
        $this->test = $test;
    }

    public function getTest() {
        return $this->test;
    }
}
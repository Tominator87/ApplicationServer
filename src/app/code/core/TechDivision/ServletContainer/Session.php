<?php
/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 07.04.13
 * Time: 00:33
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\ServletContainer;


interface Session {

    /**
     * Returns the ID of the session.
     *
     * @param void
     * @return string The session ID
     */
    public function getSessionId();

    /**
     * Sets the ID of the session.
     *
     * @param string $sessionId
     * @return void
     */
    public function setSessionId($sessionId);

}
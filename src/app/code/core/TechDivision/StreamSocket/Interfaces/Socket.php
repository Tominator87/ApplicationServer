<?php

namespace TechDivision\StreamSocket\Interfaces;

/**
 * The interface for socket implementations
 *
 * @package TechDivision\StreamSocket
 * @author Johann Zelger <j.zelger@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
interface Socket {

    public function listen($url);
    public function connect($url, $timeout = 30);

}
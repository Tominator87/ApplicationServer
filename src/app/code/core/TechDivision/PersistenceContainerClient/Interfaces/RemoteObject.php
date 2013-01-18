<?php

/**
 * TechDivision\PersistenceContainerClient\Interfaces\RemoteObject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Interfaces;

/**
 * Interface for all remote objects.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface RemoteObject {

    /**
     * Returns the session instance.
     * 
     * @return TechDivision\PersistenceContainerClient\Interfaces\Session The session instance
     */
    public function getSession();

    /**
     * The name of the original object.
     * 
     * @return string The name of the original object
     */
    public function getClassName();
}
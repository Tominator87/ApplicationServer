<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision\PersistenceContainerClient
 */

namespace TechDivision\PersistenceContainerClient\Proxy;

use TechDivision\PersistenceContainerClient\Proxy;
use TechDivision\PersistenceContainerClient\RemoteMethodCall;

/**
 * Proxy for the container instance itself.
 *
 * @package TechDivision\PersistenceContainerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class InitialContext extends Proxy {

    /**
     * Initialize the proxy instance.
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct('TechDivision\ApplicationServer\InitialContext');
    }

    /**
     * Runs a lookup on the container for the class with the
     * passed name.
     * 
     * @param string $className The class name to run the lookup for
     * @return TechDivision\PersistenceContainerClient\Interfaces\RemoteObject The instance
     */
    public function lookup($className) {
        return Proxy::create($className)->setSession($this->getSession());
    }

}
<?php

/**
 * TechDivision\ServletContainer\Http\Request
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Http;


class GetRequest extends Request {

    public function __construct(){
        parent::__construct();
    }

    public function ParseUriInformation(){
        $uriMod = explode( "?", $this->getUri() );

        $this->setPathInfo( $uriMod[0] );
        $this->setQueryString( $uriMod[1] );

        return $this;
    }

    public function setParameter(){
        $this->_parameter = $this->getQueryString();
        $this->setParameterMap();

        return $this;
    }

}
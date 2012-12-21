<?php

namespace TechDivision\ServletContainer\Interfaces;

use TechDivision_Lang_String as String;

interface Response
{
    /**
     * @abstract
     * @return \TechDivision_Lang_String
     */
    public function getContent();

    /**
     * @abstract
     * @param \TechDivision_Lang_String $content
     * @return void
     */
    public function setContent(String $content);
}

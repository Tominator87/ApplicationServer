<?php

namespace TechDivision\ServletContainer\Interfaces;

interface Request
{
    /**
     * @throws IOException
     */
    public function getInputStream();
}

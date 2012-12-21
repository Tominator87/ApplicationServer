<?php

namespace TechDivision\ApplicationServer\Interfaces;

interface ContainerConfiguration {
    public function getAddress();
    public function getPort();
    public function getWorkerNumber();
}
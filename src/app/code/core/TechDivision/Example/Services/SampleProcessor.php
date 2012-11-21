<?php

namespace TechDivision\Example\Services;

use TechDivision\Example\Entities\Sample;
use TechDivision\ApplicationServer\Container;
use TechDivision\ApplicationServer\Interfaces\Stateless;

class SampleProcessor implements Stateless {

    public function __construct(Container $container) {
        $this->_container = $container;
    }

    public function getContainer() {
        return $this->_container;
    }

    public function load($id) {
        $em = $this->getContainer()->getEntityManager();     
        return $em->find('TechDivision\Example\Entities\Sample', $id);
    }

    public function persist(Sample $entity) {
        $em = $this->getContainer()->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function findAll() {
        $em = $this->getContainer()->getEntityManager();
        $repository = $em->getRepository('TechDivision\Example\Entities\Sample');
        return $repository->findAll();
    }

}
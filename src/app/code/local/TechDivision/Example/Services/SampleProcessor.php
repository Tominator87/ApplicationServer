<?php

namespace TechDivision\Example\Services;

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainer\Application;
use TechDivision\PersistenceContainer\Interfaces\Stateless;

class SampleProcessor implements Stateless {

    public function __construct(Application $application) {
        $this->_application = $application;
    }

    public function getApplication() {
        return $this->_application;
    }

    public function load($id) {
        $em = $this->getApplication()->getEntityManager();     
        return $em->find('TechDivision\Example\Entities\Sample', $id);
    }

    public function persist(Sample $entity) {
        $em = $this->getApplication()->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function findAll() {
        $em = $this->getApplication()->getEntityManager();
        $repository = $em->getRepository('TechDivision\Example\Entities\Sample');
        return $repository->findAll();
    }

    public function getContainer()
    {
        
    }

}
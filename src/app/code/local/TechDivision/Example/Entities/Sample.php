<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="sample")
 */
class Sample {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    private $sampleId;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;

    public function setSampleId($sampleId) {
        $this->sampleId = $sampleId;
    }

    public function getSampleId() {
        return $this->sampleId;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
}
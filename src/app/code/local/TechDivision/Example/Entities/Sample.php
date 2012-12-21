<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="sample")
 */
class Sample {

    /**
     * @Id 
     * @Column(type="integer") 
     * @GeneratedValue
     */
    protected $sampleId;

    /**
     * @Column(type="string", length=255)
     */
    protected $name;

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
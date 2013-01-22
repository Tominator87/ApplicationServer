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
    public $sampleId;

    /**
     * @Column(type="string", length=255)
     */
    public $name;

    /**
     * @OneToMany(targetEntity="User", mappedBy="sample", cascade={"all"}, fetch="EAGER")
     **/
    private $users;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function getUsers() {
        return $this->users;
    }
}
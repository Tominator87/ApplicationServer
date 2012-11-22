<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="resource")
 **/
class Resource {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $resourceId;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $resourceLocale;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $key;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $message;

    /**
     * Returns the value of the class member resourceId.
     *
     * @return integer Holds the value of the class member resourceId
     */
    public function getResourceId() {
        return $this->resourceId;
    }

    /**
     * Sets the value for the class member resourceId.
     *
     * @param integer $resourceId Holds the value for the class member resourceId
     */
    public function setResourceId($resourceId) {
        $this->resourceId = $resourceId;
    }

    /**
     * Returns the value of the class member resourceLocale.
     *
     * @return string Holds the value of the class member resourceLocale
     */
    public function getResourceLocale() {
        return $this->resourceLocale;
    }

    /**
     * Sets the value for the class member resourceLocale.
     *
     * @param string $resourceLocale Holds the value for the class member resourceLocale
     */
    public function setResourceLocale($resourceLocale) {
        $this->resourceLocale = $resourceLocale;
    }

    /**
     * Returns the value of the class member key.
     *
     * @return string Holds the value of the class member key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Sets the value for the class member key.
     *
     * @param string $key Holds the value for the class member key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     * Returns the value of the class member message.
     *
     * @return string Holds the value of the class member message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Sets the value for the class member message.
     *
     * @param string $message Holds the value for the class member message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

}
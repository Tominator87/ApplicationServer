<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="user")
 */
class User {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $userId;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $username;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $userLocale;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @Column(type="boolean")
     * @var boolean
     */
    protected $enabled;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $rate;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $contractedHours;

    /**
     * @Column(type="boolean")
     * @var boolean
     */
    protected $ldapSynced;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $syncedAt;
    // ...
    /**
     * @ManyToOne(targetEntity="Sample", inversedBy="users", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="sampleIdFk", referencedColumnName="sampleId")
     **/
    protected $sample;

    /**
     * Returns the value of the class member userId.
     *
     * @return integer Holds the value of the class member userId
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Sets the value for the class member userId.
     *
     * @param integer $userId Holds the value for the class member userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * Returns the value of the class member userId.
     *
     * @return integer Holds the value of the class member userId
     */
    public function getSampleIdFk() {
        return $this->sampleIdFk;
    }

    /**
     * Sets the value for the class member userId.
     *
     * @param integer $userId Holds the value for the class member userId
     */
    public function setSampleIdFk($sampleIdFk) {
        $this->sampleIdFk = $sampleIdFk;
    }

    /**
     * Returns the value of the class member email.
     *
     * @return string Holds the value of the class member email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Sets the value for the class member email.
     *
     * @param string $email Holds the value for the class member email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Returns the value of the class member username.
     *
     * @return string Holds the value of the class member username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Sets the value for the class member username.
     *
     * @param string $username Holds the value for the class member username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Returns the value of the class member userLocale.
     *
     * @return string Holds the value of the class member userLocale
     */
    public function getUserLocale() {
        return $this->userLocale;
    }

    /**
     * Sets the value for the class member userLocale.
     *
     * @param string $userLocale Holds the value for the class member userLocale
     */
    public function setUserLocale($userLocale) {
        $this->userLocale = $userLocale;
    }

    /**
     * Returns the value of the class member password.
     *
     * @return string Holds the value of the class member password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Sets the value for the class member password.
     *
     * @param string $password Holds the value for the class member password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Returns the value of the class member enabled.
     *
     * @return boolean Holds the value of the class member enabled
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Sets the value for the class member enabled.
     *
     * @param boolean $enabled Holds the value for the class member enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    /**
     * Returns the value of the class member rate.
     *
     * @return integer Holds the value of the class member rate
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * Sets the value for the class member rate.
     *
     * @param integer $rate Holds the value for the class member rate
     */
    public function setRate($rate) {
        $this->rate = $rate;
    }

    /**
     * Returns the value of the class member contractedHours.
     *
     * @return integer Holds the value of the class member contractedHours
     */
    public function getContractedHours() {
        return $this->contractedHours;
    }

    /**
     * Sets the value for the class member contractedHours.
     *
     * @param integer $contractedHours Holds the value for the class member contractedHours
     */
    public function setContractedHours($contractedHours) {
        $this->contractedHours = $contractedHours;
    }

    /**
     * Returns the value of the class member ldapSynced.
     *
     * @return boolean Holds the value of the class member ldapSynced
     */
    public function getLdapSynced() {
        return $this->ldapSynced;
    }

    /**
     * Sets the value for the class member ldapSynced.
     *
     * @param boolean $ldapSynced Holds the value for the class member ldapSynced
     */
    public function setLdapSynced($ldapSynced) {
        $this->ldapSynced = $ldapSynced;
    }

    /**
     * Returns the value of the class member syncedAt.
     *
     * @return integer Holds the value of the class member syncedAt
     */
    public function getSyncedAt() {
        return $this->syncedAt;
    }

    /**
     * Sets the value for the class member syncedAt.
     *
     * @param integer $syncedAt Holds the value for the class member syncedAt
     */
    public function setSyncedAt($syncedAt = null) {
        $this->syncedAt = $syncedAt;
    }

}
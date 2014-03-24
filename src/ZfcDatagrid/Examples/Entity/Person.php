<?php

namespace ZfcDatagrid\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $displayName;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $familyName;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $givenName;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $gender;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $age;
	
	/**
	 * @ORM\Column(type="decimal")
	 */
	protected $weight;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $birthday;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $changeDate;
	
	/**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="primaryGroupId", referencedColumnName="id")
     */
	protected $primaryGroup;
	
	
	/**
	 *
	 * @param integer $id        	
	 */
	public function setId($id) {
		$this->id = $id;
	}
	/**
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return $this->displayName;
	}
	
	/**
	 *
	 * @param string $displayName        	
	 */
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getFamilyName() {
		return $this->familyName;
	}
	
	/**
	 *
	 * @param string $familyName        	
	 */
	public function setFamilyName($familyName) {
		$this->familyName = $familyName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getGivenName() {
		return $this->givenName;
	}
	
	/**
	 *
	 * @param string $givenName        	
	 */
	public function setGivenName($givenName) {
		$this->givenName = $givenName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 *
	 * @param string $email        	
	 */
	public function setEmail($email) {
		$this->email = $email;
	}
	
	/**
	 *
	 * @return string $gender
	 */
	public function getGender() {
		return $this->gender;
	}
	
	/**
	 *
	 * @param string $gender        	
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getAge() {
		return $this->age;
	}
	
	/**
	 *
	 * @param integer $age        	
	 */
	public function setAge($age) {
		$this->age = $age;
	}
	
	/**
	 *
	 * @return float
	 */
	public function getWeight() {
		return $this->weight;
	}
	
	/**
	 *
	 * @param float $weight        	
	 */
	public function setWeight($weight) {
		$this->weight = $weight;
	}
	
	/**
	 *
	 * @return \DateTime
	 */
	public function getBirthday() {
		return $this->birthday;
	}
	
	/**
	 *
	 * @param \DateTime $birthday        	
	 */
	public function setBirthday($birthday) {
	    if(is_string($birthday)){
	        $birthday = new \DateTime($birthday);
	    }
		$this->birthday = $birthday;
	}
	
	/**
	 *
	 * @return \DateTime
	 */
	public function getChangeDate() {
		return $this->changeDate;
	}
	
	/**
	 *
	 * @param \DateTime $changeDate        	
	 */
	public function setChangeDate($changeDate) {
	    if(is_string($changeDate)){
	        $changeDate = new \DateTime($changeDate);
	    }
		$this->changeDate = $changeDate;
	}
}


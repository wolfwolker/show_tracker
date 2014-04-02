<?php

namespace WW\WWShowTrackerBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table
 */
class User
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	private $email;

	public function getId()
	{
		return $this->id;
	}

	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}

	function __toString()
	{
		return $this->getEmail();
	}
}

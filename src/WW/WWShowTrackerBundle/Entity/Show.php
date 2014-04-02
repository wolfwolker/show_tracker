<?php

namespace WW\WWShowTrackerBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table
 */
class Show
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
	private $url;

	/**
	 * @ORM\Column(type="string", length=30, unique=true)
	 */
	private $name;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $season;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $episode;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $leadingZeroEpisode = true;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $leadingZeroSeason = false;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = true;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 * @var \DateTime
	 */
	private $lastUpdate;

	/**
	 * @ORM\ManyToMany(targetEntity="User", fetch="EAGER")
	 * @ORM\JoinTable
	 *
	 * @var ArrayCollection
	 */
	private $users;

	/**
	 * @ORM\Column(type="integer")
	 *
	 * @var integer
	 */
	private $checkNextSeason = 0;

	public function setCheckNextSeason($checkNextSeason)
	{
		$this->checkNextSeason = $checkNextSeason;
		return $this;
	}

	public function getCheckNextSeason()
	{
		return $this->checkNextSeason;
	}

	public function setUsers(ArrayCollection $users)
	{
		$this->users = $users;
		return $this;
	}

	public function getUsers()
	{
		return $this->users;
	}

	function __construct()
	{
		$this->users = new ArrayCollection;
	}

	public function setActive($active)
	{
		$this->active = $active;
		return $this;
	}

	public function getActive()
	{
		return $this->active;
	}

	public function setEpisode($episode)
	{
		$this->episode = (int)$episode;
		return $this;
	}

	public function getEpisode()
	{
		return $this->episode;
	}

	public function getNext($episode = true)
	{
		return $episode ?
			$this->getSeasonAndEpisodeAsArray($this->getSeason(), $this->getEpisode()+1) :
			$this->getSeasonAndEpisodeAsArray($this->getSeason() + 1, 1);
	}

	public function getCurrent()
	{
		return $this->getSeasonAndEpisodeAsArray($this->getSeason(), $this->getEpisode());
	}

	private function getSeasonAndEpisodeAsArray($s, $e)
	{
		$s = $s < 10 && $this->getLeadingZeroSeason() ? sprintf("0%s", $s) : $s;
		$e = $e < 10 && $this->getLeadingZeroEpisode() ? sprintf("0%s", $e) : $e;

		return array(
			'{season}' => $s,
			'{episode}' => $e,
			'{s}' => $s,
			'{e}' => $e,
		);
	}

	public function update(array $data)
	{
		$this->setSeason(array_shift($data));
		$this->setEpisode(array_shift($data));
		$this->setLastUpdate();
	}

	public function getId()
	{
		return $this->id;
	}

	public function setLastUpdate()
	{
		$this->lastUpdate = new \DateTime();
		return $this;
	}

	public function getLastUpdate()
	{
		return $this->lastUpdate;
	}

	public function setLeadingZeroEpisode($leadingZeroEpisode)
	{
		$this->leadingZeroEpisode = $leadingZeroEpisode;
		return $this;
	}

	public function getLeadingZeroEpisode()
	{
		return $this->leadingZeroEpisode;
	}

	public function setLeadingZeroSeason($leadingZeroSeason)
	{
		$this->leadingZeroSeason = $leadingZeroSeason;
		return $this;
	}

	public function getLeadingZeroSeason()
	{
		return $this->leadingZeroSeason;
	}

	public function setSeason($season)
	{
		$this->season = (int)$season;
		return $this;
	}

	public function getSeason()
	{
		return $this->season;
	}

	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	function __toString()
	{
		return $this->getName();
	}
}

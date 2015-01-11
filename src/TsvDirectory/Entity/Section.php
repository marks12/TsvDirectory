<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;

use TsvDirectory\Entity\Content;

/** @ORM\Entity */
class Section {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	protected $id;

	/** @ORM\Column(type="string") */
	protected $secName;

	/** @ORM\Column(type="text") */
	protected $secDescription;

    /** @ORM\ManyToMany(targetEntity="Content") */
    protected $Content;
    
    /**
     * Magic getter
     * @param $property
     * @return mixed
     */
    public function __get($key)
    {
    	if(property_exists($this, $key))
    	return $this->{$key};
    	else
	    	die("Requested property {$key} not exists in ".__FUNCTION__." ".__CLASS__);
    }

    public function __construct() {
    	$this->Content = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Magic setter
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
    	if(property_exists($this, $key))
	    	$this->{$key} = $value;
    	else
	    	die("Requested property {$key} not exists in ".__FUNCTION__." ".__CLASS__);
    }
	
}
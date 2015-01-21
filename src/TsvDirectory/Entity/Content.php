<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;

use TsvDirectory\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity */
class Content {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="integer") */
	protected $order_num;

	/** @ORM\ManyToMany(targetEntity="TsvText") */
	protected $TsvText;	

	/** @ORM\ManyToMany(targetEntity="TsvFile") */
	protected $TsvFile;
	
	/** @ORM\ManyToMany(targetEntity="TsvStext") */
	protected $TsvStext;
	
	/** @ORM\ManyToMany(targetEntity="TsvCarousel") */
	protected $TsvCarousel;
	
	/** @ORM\Column(type="string") */
	protected $content_type;
	
	/** @ORM\Column(type="string") */
	protected $TsvKey;
	
	/** @ORM\ManyToOne(targetEntity="Section", inversedBy="Content")*/
	protected $Section;
	
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

    public function __construct() {
    	$this->TsvText = new ArrayCollection();
    	$this->TsvFile = new ArrayCollection();
    	$this->TsvStext = new ArrayCollection();
    	$this->TsvCarousel = new ArrayCollection();
    	$this->Section = new ArrayCollection();
    }
}
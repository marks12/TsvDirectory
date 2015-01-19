<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity */
class TsvCarouselElement {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="text") */
	protected $content;
	
	/** @ORM\ManyToOne(targetEntity="TsvCarousel", inversedBy="TsvCarouselElements")*/
	private $TsvCarousel; // Привязка к карусели
	
	/** @ORM\ManyToMany(targetEntity="TsvCarouselImage") */
	protected $Background;
	
	function __construct()
	{
		$this->TsvCarousel = new ArrayCollection();
		$this->Background = new ArrayCollection();
	}
	
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

}
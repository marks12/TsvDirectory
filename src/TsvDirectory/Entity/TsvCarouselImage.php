<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity */
class TsvCarouselImage {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="string") */
	protected $url;
	
	/** @ORM\ManyToOne(targetEntity="TsvCarousel", inversedBy="TsvCarouselImages")*/
	private $TsvCarousel; // Привязка к карусели
	
	function __construct()
	{
		$this->TsvCarousel = new ArrayCollection();
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
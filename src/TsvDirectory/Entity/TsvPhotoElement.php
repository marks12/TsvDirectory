<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;

use TsvDirectory\Entity\TsvPhoto;

/** @ORM\Entity */
class TsvPhotoElement {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="integer") */
	protected $order_num;

	/** @ORM\Column(type="text") */
	protected $FileName;
	
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
    	$this->TsvText = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
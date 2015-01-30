<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;

use TsvDirectory\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity */
class ContentData {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="string") */
	protected $contentName;
	
	/**
	 * @ORM\OneToOne(targetEntity="Content")
	 * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
	 **/
	private $Content;
	
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
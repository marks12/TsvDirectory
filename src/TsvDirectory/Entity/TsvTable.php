<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity 
 *  @ORM\Table(options={"comment":"Динамические таблицы"});
 * */
class TsvTable {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/** @ORM\Column(type="string") */
	protected $name;

	/** @ORM\Column(type="text", nullable=true) */
	protected $description;

	/** @ORM\OneToMany(targetEntity="TsvTableField", mappedBy="table", cascade={"persist","remove"})*/
	protected $fields;
	
    /**
     * Magic getter
     * @param $property
     * @return mixed
     */
    public function __get($key)
    {
    	if(property_exists($this, $key))
    	return $this->{$key};
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
    	$this->fields = new ArrayCollection();
    }
    
}
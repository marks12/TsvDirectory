<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity 
 * */
class TsvTable {
	/**
	 * @ORM\Id 
	 * @ORM\Column(type="integer", options={"comment":"Dinamic tables"})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/** @ORM\Column(type="string") */
	protected $name;

	/** @ORM\Column(type="string") */
	protected $entity;

	/** @ORM\Column(type="text", nullable=true) */
	protected $description;

	/** @ORM\Column(type="text", nullable=true, options={"comment"="Список полей подключенных для связей таблицы"}) */
	protected $linked_fields;

	/** @ORM\Column(type="boolean", nullable=true, options={"default"="0", "comment"="Отображать справочник в Я-Монитор"}) */
	protected $iMonitor = 0;

	/** @ORM\Column(type="boolean", nullable=true, options={"default"="1", "comment"="Отображать справочник в управлении данными"}) */
	protected $dataManagement = 0;
	
	/** @ORM\Column(type="integer", options={"default"=0}) */
	protected $viewType = 0;
	
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

    }
    
}
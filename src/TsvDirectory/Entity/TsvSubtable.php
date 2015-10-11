<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/** 
 * @ORM\Entity
 * @ORM\Table(options={"comment":"Расширение таблицы зависимыми полями"})
 **/
class TsvSubtable {
	
	/**
	 * @ORM\Id @ORM\Column(type="integer", options={"comment":"Id"})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\Column(type="string", options={"comment":"Класс хранения данных"}) */
	protected $data_class;
	
	/** @ORM\Column(type="string", options={"comment":"Класс типизации данных"}) */
	protected $title_class;

	/**
	 * @ORM\ManyToOne(targetEntity="TsvDirectory\Entity\TsvTable", inversedBy="subtables")
	 * @ORM\JoinColumn(name="table_id", referencedColumnName="id", onDelete="cascade")
	 **/
	protected $tsv_table;
	
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
        
    }
}
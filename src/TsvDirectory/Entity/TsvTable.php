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
	 * Необходимо в случае применения в сущности данных зависящих от других
	 * таблиц и справочников. Например: есть таблица товаров и у товаров может быть
	 * динамическое количество цен. Для этого создается отдельный справочник типов
	 * цен а таблица цен становится перекрестной. Для этого таблица цен должна
	 * содержать ManyToOne связь с типом цены и ManyToOne связь с таблицей товаров.
	 * При этом таблица типов цен должна содержать поле name которое будет являться
	 * заголовком для цены и отражать ее тип.
	 * 
	 * @ORM\OneToMany(targetEntity="TsvDirectory\Entity\TsvSubtable", mappedBy="tsv_table")
	 */
	private $subtables;
	
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
        
        $this->subtables = new ArrayCollection();
    }
    
    /**
     * Try to find subtablse data class in subtables ArrayCollection
     * @param string $class_name
     * @return boolean
     */
    public function is_subtable($class_name) {
        
        foreach ($this->subtables as $st)
            if($st->__get('data_class')==$class_name)
                return true;
        return false;
    }
    
    /**
     * Try to find subtablse data class in subtables ArrayCollection
     * @param string $class_name
     * @return boolean
     */
    public function is_subtableTitle($class_name) {
        
        foreach ($this->subtables as $st)
            if($st->__get('title_class')==$class_name)
                return true;
        return false;
    }
    
    public function getTitleSubtableByData($subtable_class)
    {
        foreach ($this->subtables as $sub)
            if($sub->__get('data_class') == $subtable_class)
                return $sub->__get('title_class');
            
            return false;
    }
    
}
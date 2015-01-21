<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use TsvFunctions;
use TsvDirectory;

/** @ORM\Entity */
class TsvCarousel {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 
	/** @ORM\Column(type="text") */
	protected $TsvCarousel;

	/** @ORM\OneToMany(targetEntity="TsvCarouselElement", mappedBy="TsvCarousel", cascade={"remove"})*/
	private $TsvCarouselElements; // Привязка к страницам карусели

	/** @ORM\OneToMany(targetEntity="TsvCarouselImage", mappedBy="TsvCarousel", cascade={"remove"})*/
	private $TsvCarouselImages; // Привязка к картинкам
	
	public function __construct() {
		$this->TsvCarouselElements = new ArrayCollection();
	}
	
	/**
	 * Return array of data fields
	 * @return multitype:string
	 */
	public function get_vars()
	{
		return array("TsvCarousel");
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
    
    public function onDelete($id)
    {
    	$dir = TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/'.(int)$id;
    	
    	if(file_exists($dir) && is_dir($dir))
    		if(TsvFunctions\Controller\TsvFunctionsController::deleteDir($dir))
    			return true;
    		else 
    			return false;
    	else 
	    	return null;
//   		exit('Dir '.$dir.' deleted');
    }
    
    /**
     * Check required variables
     * @param object $input_object
     * @return boolean
     */
    public function check_input($input_object)
    {
    	if(isset($input_object->TsvCarousel))
    		if(isset($input_object->TsvCarousel) && $input_object->TsvCarousel!='')
    			return true;
    	
    	return false;
    }

    public function afterSave()
    {
    	$dir_source = \TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/0';
    	$dir_destination = \TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/'.$this->id;
    	
    	rename($dir_source, $dir_destination);
    }
    
}
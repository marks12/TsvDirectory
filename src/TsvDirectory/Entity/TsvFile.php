<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use TsvFunctions;
use TsvDirectory;

/** @ORM\Entity */
class TsvFile {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 
	/** @ORM\Column(type="string") */
	protected $TsvFile;

	/** @ORM\OneToMany(targetEntity="TsvFileElement", mappedBy="TsvFile", cascade={"persist","remove"})*/
	private $TsvFileElements; // Привязка к файлам
	
	/** @ORM\ManyToOne(targetEntity="Content", inversedBy="TsvFile")*/
	protected $Content;
	
	public function __construct() {
		$this->TsvFileElements = new ArrayCollection();
	}
	
	/**
	 * Return array of data fields
	 * @return multitype:string
	 */
	public function get_vars()
	{
		return array("TsvFile");
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
    
    public function onDelete()
    {

    	$dir = TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvFile').'/'.$this->__get('Content')->__get('id');
    	
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
    	if(isset($input_object->TsvKey))
    		if(isset($input_object->TsvFile) && $input_object->TsvFile!='')
    			return true;
    	
    	return false;
    }

    
}
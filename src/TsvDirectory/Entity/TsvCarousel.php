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

	/** @ORM\OneToMany(targetEntity="TsvCarouselElement", mappedBy="TsvCarousel", cascade={"persist","remove"})*/
	private $TsvCarouselElements; // Привязка к страницам карусели

	/** @ORM\OneToMany(targetEntity="TsvCarouselImage", mappedBy="TsvCarousel", cascade={"persist","remove"})*/
	private $TsvCarouselImages; // Привязка к картинкам
	
	/** @ORM\ManyToOne(targetEntity="Content", inversedBy="TsvCarousel")*/
	protected $Content;
	
	/** @ORM\Column(type="integer") */
	private $strategy = 1;	// 1 - стратегия элементов карусели с HTML текстом. На фоне используется картинка.
						// 2 - стратегия использования картинок как элементов каресули.
						
	/** @ORM\Column(type="integer") */
	private $height = 0;	// высота карусели в px, 0 - авто
	
	public function __construct() {
		$this->TsvCarouselElements = new ArrayCollection();
	}
	
	/**
	 * Return array of data fields
	 * @return multitype:string
	 */
	public function get_vars()
	{
		return array("TsvCarousel","strategy");
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

    	$dir = TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/'.$this->__get('Content')->__get('id');
    	
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

    public function afterSave($em)
    {
    	$dir_source = \TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/0';
    	$dir_destination = \TsvDirectory\Controller\TsvDirectoryController::get_dir_name().strtolower('TsvCarousel').'/'.$this->Content->__get('id');
    	   	
    	rename($dir_source, $dir_destination);
    	
    	$upload_url = \TsvDirectory\Controller\TsvDirectoryController::get_full_url().'/files/'.strtolower('TsvCarousel').'/'.(int)$this->Content->__get('id').'/';
    	
    	$dir = opendir($dir_destination);
    	
    	while ($file_name = readdir($dir))
    	{
    		if(in_array($file_name, array(".","..","thumbnail")))
    			continue;
    	
    		$file = new TsvCarouselImage();
    		$file->__set('url',$upload_url.$file_name);
    		$file->__set('TsvCarousel',$this);
    		$em->persist($file);
    	
    	}
    	$em->flush();
    	 
    	closedir($dir);

    }
    
}
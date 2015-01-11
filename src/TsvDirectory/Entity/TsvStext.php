<?php
namespace TsvDirectory\Entity;
use Doctrine\ORM\Mapping as ORM;


/** @ORM\Entity */
class TsvStext {
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/** @ORM\Column(type="string") */
	protected $TsvStext;

	/**
	 * Return array of data fields
	 * @return multitype:string
	 */
	public function get_vars()
	{
		return array("TsvStext");
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
    	die("Requested property {$key} not exists2 in ".__FUNCTION__." ".__CLASS__);
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
    
    /**
     * Check required variables
     * @param object $input_object
     * @return boolean
     */
    public function check_input($input_object)
    {
    	if(isset($input_object->TsvKey))
    		if(isset($input_object->TsvStext) && $input_object->TsvStext!='')
    			return true;
    		
    	return false;
    }

    
}
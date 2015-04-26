<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;



class TgetValue extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($field_value)
	{
// 		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
// 		$em = $getEM->GetEM();

		if(gettype($field_value)!="object")
			return $field_value;
		elseif(get_class($field_value) == 'Doctrine\ORM\PersistentCollection' || strstr(get_class($field_value),"DoctrineORMModule\Proxy\__CG__"))
		{
// 			var_dump($field_value->getMapping()['targetEntity']);

			return "Всего: ".count($field_value)." элементов";
			
			return $type = get_class($field_value);
		}
		else 
			return "unsupported data type ".get_class($field_value);
// 		var_dump($filed_value);
// 		exit();
		
// 		return $filed_value;
	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}
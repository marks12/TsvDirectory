<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;



class TgetValue extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($row,$filed_name)
	{
		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
		$em = $getEM->GetEM();

		if(isset($em->getClassMetadata(get_class($row))->fieldMappings[$filed_name]))
		$field_params = $em->getClassMetadata(get_class($row))->fieldMappings[$filed_name];
		else 
			$field_params = null;
		
		$data_type = trim(strtolower($field_params["type"]));
		
		if($data_type == 'string' && isset($field_params['options']['enumNames']))
			$data_type = 'enum';
		
		if($data_type == 'double')
			$data_type = 'float';
		
		if(gettype($row->__get($filed_name))!="object" && in_array($data_type, array("float","enum","string","integer")))
		{
			$viewHM = $this->sm->getServiceLocator()->get('ViewHelperManager');
			$partial = $viewHM->get('partial');
			$html = $partial->__invoke("tsv-table/types/".$data_type,array("data"=>$row,"field"=>$filed_name,"field_params"=>$field_params));

			return $html;
		}
		elseif(gettype($row->__get($filed_name))=="object" && (get_class($row->__get($filed_name)) == 'Doctrine\ORM\PersistentCollection' || strstr(get_class($row->__get($filed_name)),"DoctrineORMModule\Proxy\__CG__")))
		{
// 			var_dump($field_value->getMapping()['targetEntity']);

			return "Всего: ".count($row->__get($filed_name))." элементов";
			
			return $type = get_class($row->__get($filed_name));
		}
		elseif($row->__get($filed_name)==NULL) 
		{
		    return "Не привязано";
		}
		else
			return "unsupported data type ".gettype($row->__get($filed_name));
// 		var_dump($filed_value);
// 		exit();
		
// 		return $filed_value;
	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}
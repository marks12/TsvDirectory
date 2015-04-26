<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;

/**
 * @author Vladimir Tsarevnikov serveon.ru tsarevnikov@mail.ru
 */

class TgetFForm extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($table_params,$field_name)
	{
		
		if(isset($table_params->fieldMappings[$field_name]))
		{
			if(isset($table_params->fieldMappings[$field_name]['options']) && isset($table_params->fieldMappings[$field_name]['options']['comment']))
			return $table_params->fieldMappings[$field_name]['options']['comment'];
		}
		else 
		{
			$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
			$em = $getEM->GetEM();
			
			$target_params = $em->getClassMetadata($table_params->associationMappings[$field_name]['targetEntity']);
			if(isset($target_params->table) && isset($target_params->table['options']) && isset($target_params->table['options']['comment']))
				return $target_params->table['options']['comment'];
			else 
				return 'Наименование не установлено';
		}
		
// 		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
// 		$em = $getEM->GetEM();

		

// 		var_dump($filed_value);
// 		exit();
		
// 		return $filed_value;

	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}
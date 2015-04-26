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
			$datatype = $table_params->fieldMappings[$field_name]['type'];
			
			if($datatype == 'string' && isset($table_params->fieldMappings[$field_name]['columnDefinition']))
				$datatype = 'enum';
			
			switch ($datatype)
			{
				case "integer":
					$htmlObject = $this->getView()->plugin('TdtInteger');
        			return $htmlObject($table_params, $field_name);
				break;

				case "float":
					$htmlObject = $this->getView()->plugin('TdtFloat');
        			return $htmlObject($table_params, $field_name);
				break;
				
				case "string":
					$htmlObject = $this->getView()->plugin('TdtString');
					return $htmlObject($table_params, $field_name);
				break;

				case "enum":
					$htmlObject = $this->getView()->plugin('TdtEnum');
					return $htmlObject($table_params, $field_name);
				break;

				case "tinyint":
					
				break;

				case "text":
				case "longtext":
					
				break;
			}
			
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
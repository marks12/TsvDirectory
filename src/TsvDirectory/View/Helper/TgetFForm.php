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
			
			$table_config = $em->getRepository('TsvDirectory\Entity\TsvTable')->findOneBy(array("entity"=>$table_params->name));
			
			$linked_fields = $table_config->__get('linked_fields');

			$linked_fields = explode(";", $linked_fields);
			
			if(count($linked_fields))
				unset($linked_fields[count($linked_fields)-1]);

			$linked_error = true;
			foreach ($linked_fields as $k=>$v)
			{
				if(strstr($v, $field_name))
				{
					$linked_error = false;
					$select_fields[] = explode(":", substr($v, 1))[1];
				}
				
			}


			if($linked_error)
			{
				
				return '<div class="panel panel-danger">
						  <div class="panel-body">
						    В настоящее время не указаны <strong>связи</strong> для таблицы 
							<code>'.$target_params->table['options']['comment'].'</code>
							<a href="'.$this->view->url('zfcadmin/tsv-directory/table',array("action"=>"configure","id"=>$table_config->__get('id'))).'">Настроить</a>
						  </div>
						</div>';
			}
			else 
			{
				$title = $target_params->table['options']['comment'];
				
// 				var_dump($select_fields);
				
				return '
				<div class="form-group">
					<label for="tt-'.$target_params->table['name'].'">'.$target_params->table['options']['comment'].'</label>
				    <input type="text" class="form-control" id="tt-min_land_size_img" placeholder="Введите данные для поиска по таблице '.$target_params->table['options']['comment'].'">
				</div>';
			}
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
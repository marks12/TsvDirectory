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
	
	public function __invoke($table_params,$field_name,$obj=null)
	{
		
		if(isset($table_params->fieldMappings[$field_name]))
		{
			$datatype = $table_params->fieldMappings[$field_name]['type'];
			
			if($datatype == 'string' && isset($table_params->fieldMappings[$field_name]['columnDefinition']))
				$datatype = 'enum';
			
			if($field_name=='id')
				return '';
			
			switch ($datatype)
			{
				case "integer":
					$htmlObject = $this->getView()->plugin('TdtInteger');
        			return $htmlObject($table_params, $field_name,$obj);
				break;

				case "float":
					$htmlObject = $this->getView()->plugin('TdtFloat');
        			return $htmlObject($table_params, $field_name,$obj);
				break;
				
				case "string":
					$htmlObject = $this->getView()->plugin('TdtString');
					return $htmlObject($table_params, $field_name,$obj);
				break;

				case "enum":
					$htmlObject = $this->getView()->plugin('TdtEnum');
					return $htmlObject($table_params, $field_name,$obj);
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
					$f = explode(":", substr($v, 1))[1];
					$select_fields[$f] = $f;
				}
				
			}

// 			var_dump($table_params->associationMappings[$field_name]['type']);
			
			if(isset($select_fields) && !isset($select_fields['id']))
				array_unshift($select_fields,'id');

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
				
// 				var_dump($target_params);

				$data_title_th = '';
				foreach ($select_fields as $k=>$v)
				{
					$data_title_th .= '<th>'.$target_params->fieldMappings[$v]['options']['comment'].'</th>';
				}
				$sObj='';
	                        if($obj!=null){
                            		foreach($obj->$field_name as $lObj){
						$i=1;
						$cells='var cell = row.insertCell(0);
                                                	cell.appendChild(checkbox);';
						foreach ($select_fields as $k=>$v){
							if($v=='id') continue;
							$cells.='cell'.$i.' = row.insertCell('.$i.');
                                                		cell'.$i.'.innerHTML="'.str_replace("\r\n",' ',rtrim($lObj->$v)).'";';
                                            		$i++;
						}
		
                                		$sObj.='<script>
                                		var row = document.createElement("tr");
						var checkbox = document.createElement("input");
						checkbox.type = "checkbox";
						checkbox.value = "value";
						checkbox.setAttribute("field","'.$field_name.'");
						checkbox.value=1;
						checkbox.class = "app_data";
						'.$cells.'
						add_data(checkbox);
						</script>';
					}
                        	}
		
				return '
				<input type="hidden" value="" name="'.$field_name.'" id="'.$field_name.'" assocType="'.$table_params->associationMappings[$field_name]['type'].'">
				<div class="panel panel-default">
			      <div class="panel-heading">
						  <div class="form-group">
						    <label class="sr-only" for="tt-'.$field_name.'">'.$target_params->table['options']['comment'].'</label>
						      <h5>'.$target_params->table['options']['comment'].'</h5>
						      <div class="input-group">
						      <input type="text" onkeypress="if(event.keyCode == 13) return false;" class="form-control findData" id="tt-'.$field_name.'" placeholder="Введите данные для поиска по таблице '.$target_params->table['options']['comment'].'">
   				      		  <a class="input-group-addon btn btn-xs btn-success" onclick="$(\'#tt-'.$field_name.'\').keyup();">Поиск</a>
   				      		  <a class="input-group-addon btn btn-xs btn-primary" onclick="$(\'#tt-'.$field_name.'-result\').html(\'\');">Стереть</a>
						    </div>
						  </div>
				  </div>
			        <div id="tt-'.$field_name.'-result"></div>
   				  <div class="panel-body hidden">
					<strong>Выбранные данные</strong>
			      </div>
			      <table class="table table-bordered">
			        <thead>
			          <tr>
						  '.$data_title_th.'
			          </tr>
			        </thead>
			        <tbody id="added-rows-'.$field_name.'" strategy="">
			          <tr id="no-rows-'.$field_name.'">
			            <td scope="row" colspan="'.count($select_fields).'">Данные не привязаны</td>
			          </tr>
			        </tbody>
			      </table>
			    </div>'.$sObj;
		    }
		}
		
// 		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
// 		$em = $getEM->GetEM();
		/*
		 		<div class="form-group">
					<label for="tt-'.$field_name.'">'.$target_params->table['options']['comment'].'</label>
				    <input type="text" class="form-control" id="tt-'.$field_name.'" placeholder="Введите данные для поиска по таблице '.$target_params->table['options']['comment'].'">
				</div>
		 */
// 		var_dump($filed_value);
// 		exit();
// 		return $filed_value;

	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}

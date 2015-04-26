<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;



class TdtEnum extends AbstractHelper
{
	public function __invoke($table_params,$field_name)
	{
		if(isset($table_params->fieldMappings[$field_name]['options']) && isset($table_params->fieldMappings[$field_name]['options']['comment']))
		$name =  $table_params->fieldMappings[$field_name]['options']['comment'];

		$radio = '';
		
		$enum_names = explode(":", $table_params->fieldMappings[$field_name]['options']['enumNames']);
		$enum_values = explode(",", str_replace("'", "", str_replace("ENUM(", "", str_replace(")", "", $table_params->fieldMappings[$field_name]['columnDefinition']))));
		
		foreach ($enum_names as $k=>$v)
		{
			$radio .= '
					<div class="radio">
					  <label>
					    <input type="radio" name="tt-'.$field_name.'" id="tt-'.$field_name.'" value="'.$enum_values[$k].'">
					    '.$v.'
					  </label>
					</div>';
		}
		
		$html = '
		<div class="form-group">
			<label for="tt-'.$field_name.'">'.$name.' (один вариант из нескольких)</label>
		    '.$radio.'
		</div>
		<script>$(\'input[name=tt-'.$field_name.']\').first().prop("checked","true");</script>';

		return $html;
	}

	
}
<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;



class TdtInteger extends AbstractHelper
{
	public function __invoke($table_params,$field_name)
	{
		if(isset($table_params->fieldMappings[$field_name]['options']) && isset($table_params->fieldMappings[$field_name]['options']['comment']))
		$name =  $table_params->fieldMappings[$field_name]['options']['comment'];

		$html = '
		<div class="form-group">
			<label for="tt-'.$field_name.'">'.$name.' (целое число)</label>
		    <input type="text" class="form-control" id="tt-'.$field_name.'" name="tt-'.$field_name.'" placeholder="Укажите '.$name.'">
		</div>';

		return $html;
	}

	
}
<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;



class TdtFloat extends AbstractHelper
{
	public function __invoke($table_params,$field_name)
	{
		if(isset($table_params->fieldMappings[$field_name]['options']) && isset($table_params->fieldMappings[$field_name]['options']['comment']))
		$name =  $table_params->fieldMappings[$field_name]['options']['comment'];

		$html = '
		<div class="form-group">
			<label for="tt-'.$field_name.'">'.$name.' (число с плавоющей точкой)</label>
		    <input type="text" class="form-control" id="tt-'.$field_name.'" placeholder="Укажите '.$name.', например: 10.50518 ">
		</div>';

		return $html;
	}

	
}
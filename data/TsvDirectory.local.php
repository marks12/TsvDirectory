<?php

/**
 * TsvDirectory Configuration
*
*	Пожалуйста укажите наименование и типы контента которые вы 
*	используете на сайте (в шаблонах) для создания этих данных в панели администратора в формате:
*
*	Please give the name and types of content that you
* 	use on site (in teplates) to create these data in the admin panel in the following format:
*
*	$settings = array(
*		'TsvDirectoryContent' => array(
*			"auto_create_content" => true,			// true / false - your content will be created automaticly with 
*													// empty values when you visit index Action in indexController /admin/tsvDirectory
*
*			"Главная"=>array(						//Main
*				"Главная карусель" => array(		//Main carousel
*					"type" =>  "carousel",
*					"width" => "100%",
*					"height" => "400px",
*					"imageType" => "background",	// you can use "background" or "img" values. 
*													// Background	- means that content will be shown over the your background
*													// Img			- means that content will be shown on Caption block over your image
*				)
*				"Главный заголовок" => array(		//Main title
*					"type" => "text",			
*				)
*				"Информация о компании" => array(	//Company information
*					"type" => "html",
*				)
*				"Фотогалерея" => array(				//Photogalary. All images will be shown as photogalary
*					"type" => "photogalary",
*				)
*				"Галерея файлов" => array(			//File galary. All files whll be shown as file directory block
*					"type" => "filegalary",
*				)
*			),
*			"О компании"=>array(					//About
*				"Главная карусель" => array(		//About carousel
*					"type" => "carousel",
*				)
*				"Главный заголовок" => array(		//About title
*					"type" => "text",			
*				)
*				"Информация о компании" => array(	//Company information
*					"type" => "html",
*				)
*				"Фотогалерея" => array(				//Photogalary
*					"type" => "photogalary",
*				)
*				"Галерея файлов" => array(			//File galary
*					"type" => "filegalary",
*				)
*			),
*		),
*	),
*
*/

$settings = array(
		"auto_create_content" => true,
		'TsvDirectoryContent' => array(
			
			"Главная" => array(
				"Главная карусель" => array(
					"type" => "text",
				),
				"Главная карусель" => array(
					"type" => "text",
				),
			),
		),
	);

return array(
		'TsvDirectoryContent' => $settings,
		)
		
		
		
		
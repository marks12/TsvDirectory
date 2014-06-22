<?php
/**
 * This file is placed here for compatibility with ZendFramework 2's ModuleManager.
 * It allows usage of this module even without composer.
 * The original Module.php is in 'src/BjyAuthorize' in order to respect PSR-0
 */

namespace TsvDirectory;

class Module 
{
	public function getConfig()
	{
		return include __DIR__ . '/../../config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespace' => array(
					__NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
				)
			)
		);		
	}
	
	public function getServiceConfig()
	{
		return array(
				'invokables' => array(
				    'TsvDirectory\Content' => 'TsvDirectory\Content',
				),
				'factories' => array(
				//     					'zfcuser_module_options'                        => 'ZfcUser\Factory\ModuleOptionsFactory',
	
				),
				//     			'aliases' => array(
						//     					'zfcuser_register_form_hydrator' => 'zfcuser_user_hydrator'
						//     			),
		);
	}

}
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
						'TsvDirectory\Service\Uploader' => function ($sm) {
							return new \TsvDirectory\Service\Uploader($sm);
						}
				),
		);
	}
// 	public function init(\Zend\ModuleManager\ModuleManager $manager)
// 	{
// 		$events = $manager->getEventManager();
// 		$sharedEvents = $events->getSharedManager();
// 		$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
// 			/* @var $e \Zend\Mvc\MvcEvent */
// 			// fired when an ActionController under the namespace is dispatched.
// 			$controller = $e->getTarget();
// 			$routeMatch = $e->getRouteMatch();
// 			/* @var $routeMatch \Zend\Mvc\Router\RouteMatch */
// 			$routeName = $routeMatch->getMatchedRouteName();
// 		}, 100);
// 	}
}
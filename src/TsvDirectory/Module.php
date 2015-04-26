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
						},
						'TsvDirectory\Service\ContentViewModel' => function ($sm) {
							return new \TsvDirectory\Service\ContentViewModel($sm);
						},
						'TsvDirectory\Service\GetEM' => function ($sm) {
							return new \TsvDirectory\Service\GetEM($sm);
						},
						'TsvDirectory\Service\ScanTemplates' => function ($sm) {
							return new \TsvDirectory\Service\ScanTemplates($sm);
						},

				),
		);
	}
	
	public function getViewHelperConfig()
	{
		return array(
				'factories' => array(
						'TsvdContent' => function($sm) {
							$helper = new View\Helper\TsvdContent($sm);
							return $helper;
						},
						'TgetValue' => function($sm) {
							$helper = new View\Helper\TgetValue($sm);
							return $helper;
						},
						'TgetFTitle' => function($sm) {
							$helper = new View\Helper\TgetFTitle($sm);
							return $helper;
						},
						'TgetFForm' => function($sm) {
							$helper = new View\Helper\TgetFForm($sm);
							return $helper;
						},
				)
		);
	}

	public function onBootstrap($e)
	{
		$serviceManager = $e->getApplication()->getServiceManager();
	
// 		$serviceManager->get('viewhelpermanager')->setFactory('GetEM', function ($sm) use ($e) {
// 			return new \TsvDirectory\Service\GetEM($sm);
// 		});
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
<?php

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use TsvDirectory\Entity\TsvTable;

/**
 * TableController
 *
 * @author
 *
 * @version
 *
 */
class TsvTableController extends AbstractActionController {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		$vm = new ViewModel();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$name = $request->getPost()->name;
			
			if($name)
			{		
				$table = new TsvTable();
				$table->__set("name", $name);
				
				$em->persist($table);
				$em->flush();
			}
		}
		
		return $vm;
	}
	
	public function createAction() {
		
		$vm = new ViewModel();
		
		
		return $vm;
	}
}
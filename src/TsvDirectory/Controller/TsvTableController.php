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
	

	public function configureAction() {
	
		$vm = new ViewModel();
	
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	
		$id = (int)$this->getEvent()->getRouteMatch()->getParam('id');
	
		$table = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($id);
	
		if(!$table)
			return $this->redirect()->toRoute("zfcadmin/tsv-directory/table");
	
		$vm->setVariable("table", $table);
	
		return $vm;
	}
	
	public function createAction() {
	
		$vm = new ViewModel();
	
		return $vm;
	}
	
	public function indexAction() {
		
		$vm = new ViewModel();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$name = $request->getPost()->name;
			$description = $request->getPost()->description;
			
			if($name)
			{		
				$table = new TsvTable();
				$table->__set("name", $name);
				$table->__set("description", $description);
				
				$em->persist($table);
				$em->flush();
				
				return $this->redirect()->toUrl("table");
			}
		}
		
		$tables = $em->getRepository("TsvDirectory\Entity\TsvTable")->findAll();
		
		$vm->setVariable("tables", $tables);
		
		return $vm;
	}


}
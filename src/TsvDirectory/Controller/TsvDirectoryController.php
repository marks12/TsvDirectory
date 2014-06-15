<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/TsvDirectory for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use TsvDirectory\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class TsvDirectoryController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }

    public function fooAction()
    {
    	
    	
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /tsvDirectory/tsv-directory/foo
        return array();
    }
    public function sectionsAction()
    {
    	$vm =  new ViewModel();
    	 
    	$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$repository = $entityManager->getRepository('TsvDirectory\Entity\Section');
    	$adapter = new DoctrineAdapter(new ORMPaginator($repository->createQueryBuilder('Section')));
    	$paginator = new Paginator($adapter);
    	$paginator->setDefaultItemCountPerPage(1);
    	 
    	$page = (int)$this->params()->fromQuery('page');
    	if($page) $paginator->setCurrentPageNumber($page);
    	
    	$vm->setVariable('paginator',$paginator);
    	   	
    	
    	return $vm;
    }
    public function addSectionAction()
    {
        $request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		if(isset($request->getPost()->secName) && $request->getPost()->secDescription)
    		{
    			$objectManager = $this
    			->getServiceLocator()
    			->get('Doctrine\ORM\EntityManager');
    			 
    			$section = new Section();
    			$section->__set("secName", $request->getPost()->secName);
    			$section->__set("secDescription", $request->getPost()->secDescription);
    			$objectManager->persist($section);
    			$objectManager->flush();

    			return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/sections");
    		}
    		
    	}
    	
    	
    	// This shows the :controller and :action parameters in default route
        // are working when you browse to /tsvDirectory/tsv-directory/foo
        return array();
    }
}

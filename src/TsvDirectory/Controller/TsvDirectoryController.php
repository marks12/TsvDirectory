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
use TsvDirectory\Entity\TsvText;
use TsvDirectory\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Doctrine\ORM\Query\ResultSetMapping;

class TsvDirectoryController extends AbstractActionController
{
    public function indexAction()
    {
    	$session = new Container('tsv');
   	
   		$session->offsetSet('selectedSession','manage');
    	
        return array("selectedSection"=>$session->offsetGet('selectedSession'),"sections"=>$this->getSections());
    }

    private function getSections()
    {
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	$section = $objectManager
    	->getRepository('TsvDirectory\Entity\Section')
    	->findAll();
    	
    	return $section;
    }

    public function viewSectionAction()
    {
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	
    	$section = $objectManager->find('TsvDirectory\Entity\Section', (int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
    	$session = new Container('tsv');
   	
   		$session->offsetSet('selectedSession',(int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
        return array("selectedSection"=>$session->offsetGet('selectedSession'),"sections"=>$this->getSections(),"section"=>$section);
    }
    
    public function addContentAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	$section_id = (int)$this->getEvent()->getRouteMatch()->getParam('section_id');
    	$content_type = $this->getEvent()->getRouteMatch()->getParam('content_type');
    	$section = $objectManager->find('TsvDirectory\Entity\Section', $section_id);
    	
    	$vm = new ViewModel();

    	
    	if ($request->isPost()) {
    	
    		switch ($content_type)
    		{
    			case "TsvText":
    				$content = new TsvText();
    			break;
    			
    			default:
    				exit("Unsupported content type in ".__FILE__.":".__LINE__);
    			break;
    		}

    		if(isset($request->getPost()->TsvKey) && $content->check_input($request->getPost()))
    		{
    			foreach ($content->get_vars() as $k=>$v)
    			{
    				$content->__set($v,$request->getPost()->$v);
    			}
    		}
    		$objectManager->persist($content);
    		
    		
//     		$rsm = new ResultSetMapping();
//     		$query = $entityManager->createNativeQuery('SELECT max(C.order_num) max_order_num FROM Content C 
//     				LEFT JOIN section_content SC on SC.content_id = C.id
//     				WHERE SC.section_id = ?', $rsm);
//     		$query->setParameter(1, $section_id);
//     		$max_order_num = $query->getResult();
    		
    		
    		$content_entity = new Content();
    		$content_entity->__set('content_type',$content_type);
    		$content_entity->__set('order_num',$section->__get('Content')->count()+1);// next num
    		$content_entity->__get($content_type)->add($content);
    		$objectManager->persist($content_entity);
    		
     		$section->__get('Content')->add($content_entity);
    		$objectManager->flush();
    		
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$section_id);
    	
    	}
    	else {
    		
    	}
    	
    	$vm->setVariable("secName", $section->__get('secName'));
    	$vm->setVariable("secId", $section->__get('id'));
    	$vm->setVariable("content_type", $content_type);
    	 
    	// This shows the :controller and :action parameters in default route
    	// are working when you browse to /tsvDirectory/tsv-directory/foo
    	
    	return $vm;
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
    	$paginator->setDefaultItemCountPerPage(10);
    	 
    	$page = (int)$this->getEvent()->getRouteMatch()->getParam('page');
    	
    	if($page) $paginator->setCurrentPageNumber($page);
    	
    	$vm->setVariable('paginator',$paginator);
    	$vm->setVariable('sections',$this->getSections());
    	   	
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
    
    public function editSectionAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    	
    	if ($request->isPost()) {
    	
    		if(isset($request->getPost()->secName) && $request->getPost()->secDescription)
    		{
	    		$section = $objectManager
	    		->getRepository('TsvDirectory\Entity\Section')
	    		->findOneBy(
	    					array(
	    							'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id') 
	    					)
	    		);
	    		$section->__set('secName',$request->getPost()->secName);
	    		$section->__set('secDescription',$request->getPost()->secDescription);
    			$objectManager->flush();
    	
    			return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/sections");
    		}
    	
    	}
    	else
    	{
//     		$section = $objectManager->find('TsvDirectory\Entity\Section', $request->getPost()->id);
    		
    		$section = $objectManager
    		->getRepository('TsvDirectory\Entity\Section')
    		->findOneBy(
    					array(
    							'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id') 
    					)
    		);

    		return array(	
    						"id"=>$section->__get('id'),
    						"secName"=>$section->__get('secName'),
    						"secDescription"=>$section->__get('secDescription'),
    				);
    		
    	}
    }
    
    public function removeSectionAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    	 
    	$section = $objectManager
    	->getRepository('TsvDirectory\Entity\Section')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			)
    	);
    	
    	$objectManager->remove($section);
    	$objectManager->flush();
    	return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/sections");
    }
    
}

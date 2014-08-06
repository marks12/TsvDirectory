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
use TsvDirectory\Entity\TsvPhoto;
use TsvDirectory\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

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

    			case "TsvPhoto":
    				$content = new TsvPhoto();
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
    		
    		$content_entity = new Content();
    		$content_entity->__set('content_type',$content_type);
    		$content_entity->__set('TsvKey',$request->getPost()->TsvKey);
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

    			$section_id = $section->__get('id');
    			
    			return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/$section_id");
    		}
    		
    	}
    	
    	// This shows the :controller and :action parameters in default route
        // are working when you browse to /tsvDirectory/tsv-directory/foo
        return array();
    }
    
    public function editContentAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	$section_id = (int)$this->getEvent()->getRouteMatch()->getParam('section_id');
    	$content_id = (int)$this->getEvent()->getRouteMatch()->getParam('content_id');
    	$content_type = $this->getEvent()->getRouteMatch()->getParam('content_type');
    	$section = $objectManager->find('TsvDirectory\Entity\Section', $section_id);
    	
    	$content = $objectManager
    	->getRepository('TsvDirectory\Entity\\'.$content_type)
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('content_id')
    			)
    	);
    	
    	$content_entity = $objectManager
    	->getRepository('TsvDirectory\Entity\Content')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('content_entity_id')
    			)
    	);
    	
    	$vm = new ViewModel();
    	
    	if ($request->isPost()) {
    	
    		if(isset($request->getPost()->TsvKey) && $content->check_input($request->getPost()))
    		{
    			foreach ($content->get_vars() as $k=>$v)
    			{
    				$content->__set($v,$request->getPost()->$v);
    			}
    		}
    		$objectManager->persist($content);
    		$objectManager->flush();
    		

    		$content_entity->__set('TsvKey',$request->getPost()->TsvKey);
    		$objectManager->persist($content_entity);
    		$objectManager->flush();
    		
    		
    		
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$section_id);
    	
    	}

    	
    	$vm->setVariable("secName", $section->__get('secName'));
    	$vm->setVariable("secId", $section->__get('id'));
    	$vm->setVariable("content_type", $content_type);
    	
    	$arr = array();
    	
    	foreach ($content->get_vars() as $k=>$v)
    	{
    		$arr[$v] = $content->__get($v);
    	}
    	$arr['TsvKey'] = $content_entity->__get('TsvKey');
    	
    	$vm->setVariable('content_arr', $arr);
    	
		// This shows the :controller and :action parameters in default route
		// are working when you browse to /tsvDirectory/tsv-directory/foo
    	
    	return $vm;
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
    
    public function removeContentAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

    	$content_type = $this->getEvent()->getRouteMatch()->getParam('content_type');
    	
    	$content = $objectManager
    	->getRepository('TsvDirectory\Entity\\'.$content_type)
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('content_id')
    			)
    	);
    	
    	$objectManager->remove($content);
    	$objectManager->flush();
		
    	return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$this->getEvent()->getRouteMatch()->getParam('section_id'));
		
    	return array();
    }
    
    /**
     * 	Add data to views in Front-End modules
     * @param string $name
     * @return mixed
     */
    public function findSection($name)
    {
    	$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    	
    	$section = $objectManager
    	->getRepository('TsvDirectory\Entity\Section')
    	->findOneBy(
    			array(
    					'secName' => explode("/", $name)[0],
    			)
    	);
    	
    
    	if(!is_object($section) || !method_exists($section, '__get'))
    		return false;
    	
    	$content = $objectManager
    	->getRepository('TsvDirectory\Entity\Content')
    	->findOneBy(
    			array(
    					'TsvKey' => explode("/", $name)[1],
    			)
    	);

    	if(!is_object($content) || !method_exists($content, '__get'))
    		return false;
    	
    	return $content->__get($content->__get('content_type'))[0]->__get('TsvText');
    
    }
    
}

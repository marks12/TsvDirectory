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
use TsvDirectory\Entity\TsvStext;
use TsvDirectory\Entity\TsvFile;
use TsvDirectory\Entity\TsvCarousel;
use TsvDirectory\Entity\TsvCarouselElement;
use TsvDirectory\Entity\TsvCarouselImage as TsvCarouselImage;
use TsvDirectory\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use TsvDirectory\Service\Uploader;
use TsvDirectory\Entity\TsvFileElement;

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
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$section = $objectManager
    	->getRepository('TsvDirectory\Entity\Section')
    	->findAll();
    	
    	return $section;
    }

    public function viewSectionAction()
    {
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	
    	$section = $objectManager->find('TsvDirectory\Entity\Section', (int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
    	$session = new Container('tsv');
   	
   		$session->offsetSet('selectedSession',(int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
        return array("selectedSection"=>$session->offsetGet('selectedSession'),"sections"=>$this->getSections(),"section"=>$section);
    }
    
    public function addContentAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
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

    			case "TsvFile":
    				$content = new TsvFile();
    			break;
    			
    			case "TsvCarousel":
    				$content = new TsvCarousel();
    			break;
    			
    			case "TsvStext":
    				$content = new TsvStext();
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
    		$content_entity->__set('Section',$section);
    		$content_entity->__set('order_num',$section->__get('Content')->count()+1);// next num
    		$objectManager->persist($content_entity);
    		$objectManager->flush();
    		
    		$content->__set("Content", $content_entity);
    		$content_entity->__get($content_type)->add($content);
			$objectManager->persist($content_entity);
    		$objectManager->flush();
    		
    		
     		$section->__get('Content')->add($content_entity);
    		$objectManager->flush();
    		
    		if(method_exists($content, "afterSave"))
    			$content->afterSave();
    		
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
    			$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    			 
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
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
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
    	$arr['id'] = $content_entity->__get('id');
    	
    	$vm->setVariable('content_arr', $arr);
    	
		// This shows the :controller and :action parameters in default route
		// are working when you browse to /tsvDirectory/tsv-directory/foo
    	
    	return $vm;
    }
    
    
    public function editSectionAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
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
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	 
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
    	$objectManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

    	$content_type = $this->getEvent()->getRouteMatch()->getParam('content_type');
    	
//     	var_dump($content_type);
//     	exit();

    	$class_name = 'TsvDirectory\Entity\\'.$content_type;
    	
    	$id = (int)$this->getEvent()->getRouteMatch()->getParam('content_id');
    	
    	
    	$content_type_obj = $objectManager->getRepository($class_name)->findOneBy(array('id' => $id));
    	
    	if($content_type_obj && method_exists($content_type_obj,'onDelete'))
    		$content_type_obj->onDelete();

    	$content_id = $content_type_obj->__get('Content')->__get('id');
    	
    	$objectManager->remove($content_type_obj);
    	$objectManager->flush();
    	
    	$content = $objectManager->getRepository('TsvDirectory\Entity\Content')->find($content_id);
    	    	
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

    	$prop = $content->__get('content_type');

    	if(count($content->__get($prop)) == 1)
	   		foreach ($content->__get($prop) as $data)
	   			if(isset($data))
	   				return $data->__get($prop);
	   			else
	   				return false;
		else
			return $content->__get($prop);

		return false;
		
    	 
    }

    private function updateFolder($upload_url,$upload_dir,$parent_id,$entity_parent,$entity_store)
    {
    	$entity_class_parent	= 'TsvDirectory\Entity'.'\\'.$entity_parent;
    	$entity_class_store	= 'TsvDirectory\Entity'.'\\'.$entity_store;
    	
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$parent = $em->getRepository('TsvDirectory\Entity\Content')->find($parent_id);
    	
    	$request = $this->getRequest();
    	
    	if(!file_exists($upload_dir))
    	{
    		$created = mkdir($upload_dir);
    		if(!$created)
    			exit("folder $upload_dir dos not exists and Program cant create it.");
    	}
    	
    	if($parent)
    	{
    		$files = $parent->__get($entity_parent)->first()->__get($entity_store."s");
    		
    		$addto = $em->getRepository($entity_class_parent)->find($parent->__get($entity_parent)->first()->__get('id'));
    		
    		if($files)
    		{
    			foreach ($files as $file)
    			{
    				$em->remove($file);
    			}
    			//     		$em->persist($parent);
    			$em->flush();
    		}
    		
    		$dir = opendir($upload_dir);
    		
    		while ($file_name = readdir($dir))
    		{
    			if(in_array($file_name, array(".","..","thumbnail")))
    				continue;
    		
    			$file = new $entity_class_store();
    			$file->__set('url',$upload_url.$file_name);
    			$file->__set($entity_parent,$addto);
    			$em->persist($file);
    			 
    		}
    		$em->flush();
    		 
    		closedir($dir);
    	}
    }
    
    protected function get_server_var($id) {
    	return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }
    
    protected function get_full_url() {
    	$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
    	!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    	strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    	return
    	($https ? 'https://' : 'http://').
    	(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
    	(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
    			($https && $_SERVER['SERVER_PORT'] === 443 ||
    					$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
    					substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    public function get_dir_name()
    {
    	$class = __CLASS__;
    	return dirname($class :: get_server_var('SCRIPT_FILENAME')).'/files/';
    }
    
    public function uploaderAction()
    {
    	$vm = new ViewModel();
    	
    	$entity_parent = $this->getEvent()->getRouteMatch()->getParam('entity_parent');
    	$entity_store = $this->getEvent()->getRouteMatch()->getParam('entity_store');
    	
    	$dir_name = $this->get_dir_name();
    	
    	if(!file_exists($dir_name))
    	{
    		$created = mkdir($dir_name);
    		if(!$created)
    			exit("folder $dir_name dos not exists and Program cant create it.");
    	}
    	
    	if(!file_exists($dir_name.strtolower($entity_parent)))
    	{
    		$created = mkdir($dir_name.strtolower($entity_parent));
    		if(!$created)
    			exit("folder ".$dir_name.strtolower($entity_parent)." dos not exists and Program cant create it.");
    	}
    	
    	$vm->setTerminal(true);
    	 
    	$tsvfile_id = (int)$this->getEvent()->getRouteMatch()->getParam('id');
//     	if(!$tsvfile_id)
//     	{
//     		exit("Error: Content id is wrong");
//     	}
    	    	
    	$upload_dir = $dir_name.strtolower($entity_parent).'/'.(int)$this->getEvent()->getRouteMatch()->getParam('id').'/';
    	$upload_url = $this->get_full_url().'/files/'.strtolower($entity_parent).'/'.(int)$this->getEvent()->getRouteMatch()->getParam('id').'/';

   		$upload_handler = new \TsvDirectory\Service\Uploader(
   				array(
   						"script_url"	=>	$this->getRequest()->getUri()->getPath(),
   						'upload_dir' 	=> 	$upload_dir,
   						'upload_url'	=>	$upload_url,
   				)
   		);

   		$request = $this->getRequest();
   		
   		if ($request->isPost() || $request->isDelete())
	   		$this->updateFolder($upload_url,$upload_dir,$tsvfile_id,$entity_parent,$entity_store);

   		return $vm;
   		
    }
    
    public function uploaderFormAction()
    {
    	$vm = new ViewModel();
    	
    	$id  = $this->getEvent()->getRouteMatch()->getParam('id');
    	
    	$entity_parent = $this->getEvent()->getRouteMatch()->getParam('entity_parent');
    	$entity_store = $this->getEvent()->getRouteMatch()->getParam('entity_store');
    	
    	$vm->setVariable("content_id", $id);
    	$vm->setVariable("entity_parent", $entity_parent);
    	$vm->setVariable("entity_store", $entity_store);
    	    	
    	return $vm;
    }
}

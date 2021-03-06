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
use TsvDirectory\Entity\TsvOneFile;
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
use TsvDirectory\View\Helper\TsvdContent;
use TsvDirectory\Service\ScanTemplates;

class TsvDirectoryController extends AbstractActionController
{
    public function indexAction()
    {
    	$vm = new ViewModel();
    	
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	$session = new Container('tsv');
   	
   		$session->offsetSet('selectedSession','manage');
    	
   		$templates_vars = $this->getServiceLocator()->get("TsvDirectory\Service\ScanTemplates")->ScanTemplates();

   		$db_vars = $em->getRepository('TsvDirectory\Entity\Content')->findAll();
   		
   		$need_tepmlate_vars = array();
   		$all_db_vars = array();
   		$need_db_vars = array();
   		
   		foreach ($db_vars as $var)
   		{
   			if(!$var || !$var->__get('Section') || !in_array($var->__get('Section')->__get('secName')."/".$var->__get('TsvKey'), $templates_vars))
   				$need_tepmlate_vars[] = $var;

   			if($var->__get('Section'))
   				$all_db_vars[] = $var->__get('Section')->__get('secName')."/".$var->__get('TsvKey');
   		}
   			
   		foreach ($templates_vars as $k=>$var)
   			if(!in_array($var, $all_db_vars))
   				$need_db_vars[$var] = $var;

   		$vm = new ViewModel();
   		
   		$sections = $this->getSections();
   		
   		$arr_sec_by_name = array();
   		foreach ($sections as $sec)
   		{
   			$arr_sec_by_name[$sec->__get('secName')] = $sec->__get('id');
   		}
   		
   		/**
   		 * Выбираем подключаемые Entity
   		 */
   		
   		$qb = $em->createQueryBuilder();
   		$dataManagement = $qb->select('t')
   			->from('TsvDirectory\Entity\TsvTable', 't')
   			->where('t.dataManagement = 1')
   			->getQuery()
   			->getResult();
   		$vm->setVariable("dataManagement",$dataManagement);
   		 
   		
   		$vm->setVariable("selectedSection", $session->offsetGet('selectedSession'));
   		$vm->setVariable("sections",$sections);
   		$vm->setVariable("need_tepmlate_vars", $need_tepmlate_vars);
   		$vm->setVariable("need_db_vars", $need_db_vars);
   		$vm->setVariable("arr_sec_by_name", $arr_sec_by_name);
   		
        return $vm;
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
    	
    	$request = $this->getRequest();
    	
    	if($request->isPost())
    	{
    		$entity = $request->getPost()->entity;
    		$entity_id = (int)$request->getPost()->entity_id;

    		$ent = $objectManager->find("TsvDirectory\Entity"."\\"."$entity",$entity_id);

    		if(method_exists($ent, "get_vars"))
    		{
    			foreach ($ent->get_vars() as $var)
    			{
    				if($request->getPost()->$var)
    					$ent->__set($var,$request->getPost()->$var);
    			}
    			
    			$objectManager->persist($ent);
    			$objectManager->flush();
    		}
    		
    	}
    	
    	$qb = $objectManager->createQueryBuilder();
    	$dataManagement = $qb->select('t')
    	->from('TsvDirectory\Entity\TsvTable', 't')
    	->where('t.dataManagement = 1')
    	->getQuery()
    	->getResult();

    	
    	$section = $objectManager->find('TsvDirectory\Entity\Section', (int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
    	$session = new Container('tsv');
   	
   		$session->offsetSet('selectedSession',(int)$this->getEvent()->getRouteMatch()->getParam('id'));
    	
        return array("selectedSection"=>$session->offsetGet('selectedSession'),"sections"=>$this->getSections(),"section"=>$section,"dataManagement"=>$dataManagement);
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

    			case "TsvOneFile":
    				$content = new TsvOneFile();
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
    				if($request->getPost()->$v)
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
    			$content->afterSave($objectManager);
    		
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$section_id);
    	
    	}
    	else {
    		$b64TsvKey = trim(base64_decode(urldecode($this->getEvent()->getRouteMatch()->getParam('b64TsvKey'))));
    		if(mb_strlen($b64TsvKey))
    			$vm->setVariable("b64TsvKey", $b64TsvKey);
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
    	
    	if($page) 
    		$paginator->setCurrentPageNumber($page);
    	
    	/**
    	 * Выбираем подключаемые Entity
    	 */
    	 
    	$qb = $entityManager->createQueryBuilder();
    	$dataManagement = $qb->select('t')
    	->from('TsvDirectory\Entity\TsvTable', 't')
    	->where('t.dataManagement = 1')
    	->getQuery()
    	->getResult();
    	$vm->setVariable("dataManagement",$dataManagement);
    	
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
    			
    			if(isset($request->getPost()->back))
	   				return $this->redirect()->toUrl($request->getPost()->back);
   				else
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
	if(!isset($this)) {
            return false;
        }
	    
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
    		
    		$exists_files = array();
    		$dir = opendir($upload_dir);
    		
    		while ($file_name = readdir($dir))
    		{
    			if(in_array($file_name, array(".","..","thumbnail")))
    				continue;
    			
    			$exists_files [] = $file_name;
    		}
    		closedir($dir);

    		if($files)
    		{
    			foreach ($files as $file)
    			{
    				$base_name = basename($file->__get('url'));

    				if(!in_array($base_name, $exists_files))
	    				$em->remove($file);
    				else
    					unset($exists_files[array_keys($exists_files,$base_name)[0]]);
    			}
    			$em->flush();
    		}
    		
    		if(count($exists_files))
			foreach ($exists_files as $k=>$v)
    		{
	    		$file = new $entity_class_store();
	    		$file->__set('url',$upload_url.$v);
	    		$file->__set($entity_parent,$addto);
	    		$em->persist($file);
	    		$em->flush();
    		}

    	}
    }
    
    protected function get_server_var($id) {
    	return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }
    
    public function get_full_url() {
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

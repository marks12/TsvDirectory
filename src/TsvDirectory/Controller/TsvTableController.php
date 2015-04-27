<?php

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use TsvDirectory\Entity\TsvTable;
use Zend\View\Model\JsonModel;
use TsvDirectory\Entity\TsvTableField;
use Zend\Session\Container;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

/**
 * TableController
 *
 * @author
 *
 * @version
 *
 */
class TsvTableController extends AbstractActionController {

	public function addDataAction()
	{
		$vm = new ViewModel();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$qb = $em->createQueryBuilder();
		$dataManagement = $qb->select('t')
		->from('TsvDirectory\Entity\TsvTable', 't')
		->where('t.dataManagement = 1')
		->getQuery()
		->getResult();

		$vm->setVariable("dataManagement",$dataManagement);
		
		$sections = $em->getRepository('TsvDirectory\Entity\Section')->findAll();
		$vm->setVariable("sections",$sections);
		
		$table_id = $this->getEvent()->getRouteMatch()->getParam('id');
		$vm->setVariable("dataManagement_id", $table_id);
		$table_config = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($table_id);

		$table_params = $em->getClassMetadata($table_config->__get('entity'));
		
		$vm->setVariable("table_config", $table_config);
		$vm->setVariable("table_params", $table_params);

		return $vm;
	}
	
	public function dataManagementAction()
	{
		$vm = new ViewModel();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$qb = $em->createQueryBuilder();
		$dataManagement = $qb->select('t')
		->from('TsvDirectory\Entity\TsvTable', 't')
		->where('t.dataManagement = 1')
		->getQuery()
		->getResult();

		$vm->setVariable("dataManagement",$dataManagement);
		
		$sections = $em->getRepository('TsvDirectory\Entity\Section')->findAll();
		$vm->setVariable("sections",$sections);
		
		$table_id = $this->getEvent()->getRouteMatch()->getParam('id');
		
		$vm->setVariable("dataManagement_id", $table_id);
		
		$table_config = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($table_id);

		$table_params = $em->getClassMetadata($table_config->__get('entity'));
		
// 		var_dump($em->getClassMetadata($table_config->__get('entity'))/*->getFieldNames()*/);
// 		exit();
		
		$vm->setVariable("table_config", $table_config);
		$vm->setVariable("table_params", $table_params);
		
		$repository = $em->getRepository($table_config->__get('entity'));
		$adapter = new DoctrineAdapter(new ORMPaginator($repository->createQueryBuilder($table_config->__get('entity'))));
		$paginator = new Paginator($adapter);
		$paginator->setDefaultItemCountPerPage(20);

		$page = (int)$this->getEvent()->getRouteMatch()->getParam('page');
		if($page)
		{
			$paginator->setCurrentPageNumber($page);
		}
		else
			$paginator->setCurrentPageNumber(0);
		
		$vm->setVariable('table',$paginator);
		
		return $vm;
	}
	
	public function configureAction()
	{
		$vm = new ViewModel();
		
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$id = $this->getEvent()->getRouteMatch()->getParam('id');
		
		$tableConfig = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($id);
		
		if(!$tableConfig)
		{
			$session = new Container("error");
			$session->offsetSet("error",array("Запрошеные настройки в БД не обнаружены, нечего настраивать"));
			$vm->terminate();
			return $this->redirect()->toRoute("zfcadmin/tsv-directory/table");
		}
		
		$cmf = $em->getMetadataFactory();
		$entities = $cmf->getAllMetadata();
		
		$res_ent = array();
		
		foreach ($entities as $ent)
			$res_ent[] = $ent->name;
		
		$entity_params = $em->getClassMetadata($tableConfig->__get('entity'));
		
		foreach ($entity_params->associationMappings as $k=>$target_ent)
		{
			$entity_params->associationMappings[$k]['targetEntityParams'] = $em->getClassMetadata($target_ent['targetEntity']);
		}
		
// 		var_dump($entity_params->associationMappings);
		
		$vm->setVariable('entities',$res_ent);
		$vm->setVariable("table", $tableConfig);
		$vm->setVariable("entity_params", $entity_params->associationMappings);
		
		return $vm;
		
	}
	
	public function saveConfigureAction()
	{
		$request_data = json_decode(file_get_contents("php://input"), true);
		$error = false;
		
		if($request_data)
		{
			$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
			
			if(isset($request_data['table_id']))
			{
				$id = $request_data['table_id'];
				$table = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($id);
				
				if($table)
				{
					$params = array("iMonitor"=>null,"dataManagement"=>null,"name"=>"TableName", "entity"=>null,"description"=>null);
					
					foreach ($params as $k=>$v)
					{
						if(!isset($request_data[$k]))
							$error .= "Не установлен обязательный параметр $k";
						else
							$params[$k] = $request_data[$k];
					}
					
					if(!$error)
					{
						foreach ($params as $k=>$v)
						{
							$table->__set($k,$v);
						}
						
						$em->persist($table);
						$em->flush();
						
					}
				}
				else
					$error = "В БД не нашлось настроек для таблицы с id=$id. Возможно страница устарела, попробуйте обновить страницу";
			}
			else
				$error = "Ошибка установки id для редактируемой таблицы";
		}
		else
		{
			$error = "Сервер не получил данные для сохранения";
		}
		
		$result = new JsonModel(array(
				'success'	=>	true,
				'error'		=>	$error,
		));
		
		
		return $result;
	}
	
	public function deleteAction()
	{
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$id = $this->getEvent()->getRouteMatch()->getParam('id');

		$tableConfig = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($id);
		
		if($tableConfig)
		{
			$em->remove($tableConfig);
			$em->flush();
		}
		else 
		{
			$session = new Container("error");
			$session->offsetSet("error",array("Запрошеные настройки в БД не обнаружены, удалять нечего"));

		}
		
		return $this->redirect()->toRoute("zfcadmin/tsv-directory/table");
	}
	
	public function indexAction() {
		
		$vm = new ViewModel();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$name = $request->getPost()->name;
			$description = $request->getPost()->description;
			$entity = $request->getPost()->entity;
			
			if($name)
			{		
				$table = new TsvTable();
				$table->__set("name", $name);
				$table->__set("description", $description);
				$table->__set("entity", $entity);
				
				$em->persist($table);
				$em->flush();
				
				return $this->redirect()->toUrl("table");
			}
		}
		
		$tables = $em->getRepository("TsvDirectory\Entity\TsvTable")->findAll();
		
		$cmf = $em->getMetadataFactory();
		$entities = $cmf->getAllMetadata();
	
		$res_ent = array();
		
		foreach ($entities as $ent)
			$res_ent[] = $ent->name;
		
		$vm->setVariable('entities',$res_ent);
		
		$vm->setVariable("tables", $tables);
		
		return $vm;
	}
	
}
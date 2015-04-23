<?php

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use TsvDirectory\Entity\TsvTable;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\View\Model\JsonModel;
use TsvDirectory\Entity\TsvTableField;

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
	
	public function getDataTypeFormAction() {
		
		$request_data = json_decode(file_get_contents("php://input"), true);

		if(!isset($request_data['type']))
		return new JsonModel(array(
				'success'	=>	true,
				'html'	=>	'type is not set',
		));
		
		$type = strip_tags($request_data['type']);

		if(!in_array($type, array('OneToOne','ManyToOne','ManyToMany','integer','string','text','one_image','image_list','one_file','file_list','enum','date','datetime')))
			$html = 'Unknown type '.$type;
		else 
		{
			switch ($type)
			{
				case "OneToOne":
				
				break;
				
				case "ManyToOne":
				
				break;
				
				case "ManyToMany":
				
				break;
				
				case "float":
				
				break;
				
				case "integer":
				
				break;
				
				case "string":
				
				break;
				
				case "text":
				
				break;
				
				case "one_image":
				
				break;
				
				case "image_list":
				
				break;
				
				case "one_file":
				
				break;
				
				case "file_list":
				
				break;
				
				case "date":
				
				break;
				
				case "datetime":
				
				break;
				
				case "enum":

				break;

				default:
					$html = 'error case for data type '.$type;
				break;
			}
			
			if(!isset($html) || !mb_strlen($html))
			{
				if(!isset($template))
					$template = "tsv-table/types/".$type;
				
				$messageView = new ViewModel();
				$viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');

// 				$TsvdContent = $viewHelperManager->get('TsvdContent');
// 				$phone_number = $TsvdContent('Главная/Телефон',array("alt"=>"+7 (495) 374-72-94."));
// 				$main_mail = $TsvdContent('Главная/Главный e-mail',array("alt"=>"tsarevnikov@mail.ru"));
// 				$messageView->setVariable("phone_number", $phone_number);

				$messageView->setTemplate($template);
				
				$renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
				$htmlPart = new \Zend\Mime\Part($renderer->render($messageView));
				$html = $htmlPart->getRawContent();
				
			}
		}
		
		if(!isset($html))
		{
			$html = '';
		}
			
		
		$result = new JsonModel(array(
				'success'	=>	true,
				'html'	=>	$html,
		));
		
		
		return $result;
	}

	public function configureAction() {
	
		$vm = new ViewModel();
	
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	
		$id = (int)$this->getEvent()->getRouteMatch()->getParam('id');
	
		$table = $em->getRepository('TsvDirectory\Entity\TsvTable')->find($id);
	
		$request = $this->getRequest();
		
		if($request->isPost())
		{
			$fields = $table->__get('fields');
			
			$field = new TsvTableField();
			
			var_dump($request->getPost());
			
			foreach ($request->getPost() as $k=>$v)
			{
				$field->__set($k,$v);
			}
			
			$field->__set("table", $table);
			
			$em->persist($field);
			$em->flush();

			
			return $this->redirect()->toRoute("zfcadmin/tsv-directory/table",array("action"=>"configure","id"=>$id));
		}
		
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
				$table->__set("entity", $entity);
				
				$em->persist($table);
				$em->flush();
				
				return $this->redirect()->toUrl("table");
			}
		}
		
		$tables = $em->getRepository("TsvDirectory\Entity\TsvTable")->findAll();
		
		$entities = $em->getConnection()->getSchemaManager()->listTables();
		
		var_dump($entities[20]);
		
		$vm->setVariable('entities',$entities);
		
		$vm->setVariable("tables", $tables);
		
		return $vm;
	}


}
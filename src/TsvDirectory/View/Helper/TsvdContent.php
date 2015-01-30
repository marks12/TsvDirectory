<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;
use TsvDirectory\Entity\ContentData;

class TsvdContent extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($str)
	{
		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
		$em = $getEM->GetEM();

		$str = htmlspecialchars(trim($str));
		
		if(!mb_strlen($str))
		{
			exit("Var have 0 length ");
		}
		
		$content = $em->getRepository('TsvDirectory\Entity\ContentData')->findOneBy(array("contentName"=>$str));
		
		if($content && $content->__get('Content'))
			return $content->__get('Content');
		elseif($content)
			return '#'.$content->__get('id');
		else 
			return '#U#';
	}
	
	
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
}
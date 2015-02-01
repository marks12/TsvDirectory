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
			throw new \Exception(sprintf(
					'%s Var value have 0 length. Please use call helper like this: <?php echo $this->TsvdContent("Pain page/Main content 1");?>',
					get_class($this) . '::' . __FUNCTION__,
					$str
			));
		}
		
		$str_arr = explode("/", $str);
		if(count($str_arr)!=2 || !mb_strlen($str_arr[0]) || !mb_strlen($str_arr[1]))
			throw new \Exception(sprintf(
					'%s Var value dont have two necessarily parts: Group name/Name. Please use call helper like this: <?php echo $this->TsvdContent("Group name/Var name");?>',
					get_class($this) . '::' . __FUNCTION__,
					$str
			));
		
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
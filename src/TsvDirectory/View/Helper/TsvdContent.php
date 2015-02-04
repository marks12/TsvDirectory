<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;
use TsvDirectory\Entity\ContentData;


class TsvdContent extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($str, array $params = array())
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
		
		$qb = $em->createQueryBuilder();
		
		$qb->select('C')
			->from('TsvDirectory\Entity\Content', 'C')
			->innerJoin('C.Section','S','WITH','S.secName = :section')
			->where('C.TsvKey = :tsvkey');
			
		$qb->setParameters(array(
				'section' => $str_arr[0],
				'tsvkey' => $str_arr[1],
		));
		$query = $qb->getQuery();
		$content = $query->getResult();

		$html = '';
		
		$viewHM = $this->sm->getServiceLocator()->get('ViewHelperManager');
		
		if($content)
			foreach ($content as $k=>$v)
			{
				$partial = $viewHM->get('partial');
				$html .= $partial->__invoke("partials/helper/".$v->__get('content_type'),array("params"=>$params, "content"=>$v->__get($v->__get('content_type')))); 
			}
		else
			$html .= "#U#";

		return $html;

	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}
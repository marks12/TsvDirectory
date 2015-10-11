<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;

/**
 * @author Vladimir Tsarevnikov serveon.ru tsarevnikov@mail.ru
 */

class TgetSubtitles extends AbstractHelper
{
	protected $em;
	protected $sm;
	
	public function __invoke($table_class)
	{
		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
		$em = $getEM->GetEM();
		
		return $em->getRepository($table_class)->findAll();
	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}

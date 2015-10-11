<?php

namespace TsvDirectory\View\Helper;
use Zend\View\Helper\AbstractHelper;

/**
 * @author Vladimir Tsarevnikov serveon.ru tsarevnikov@mail.ru
 */

class TgetSubtitlesCount extends AbstractHelper
{
	protected $sm;
	
	public function __invoke($table_class)
	{
		$getEM =   $this->sm->getServiceLocator()->get('TsvDirectory\Service\GetEM');
		$em = $getEM->GetEM();
		
        $query = $em->createQueryBuilder()
            ->select('COUNT(f.id)') 
            ->from($table_class, 'f')
            ->getQuery();
        
        $total = $query->getSingleScalarResult();
        
		return $total;
	}
		
	public function __construct($sm) {
		
		$this->sm = $sm;
	
	}
	
}

<?php

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @author
 * @version 
 */

class Carousel extends AbstractActionController
{
    public function carouselAddPageAction()
    {
    	$request = $this->getRequest();
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
    	if(!$request->isPost())
    	{
    		
    	}
    	
    	
    	
    	return array();
    }
    
    
    
}
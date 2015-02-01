<?php
namespace TsvDirectory\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class GetEM implements ServiceLocatorAwareInterface
{
	protected $services;
	protected $em;
	protected $sm;
	
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->services;
    }
    
    public function GetEM()
    {
    	$this->em = $this->sm->get('doctrine.entitymanager.orm_default');
    	
    	return $this->em;
    }
    
    function __construct($sm) {

    	$this->sm = $sm;
    	
    	return array();
    }

}

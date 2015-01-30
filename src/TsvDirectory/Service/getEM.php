<?php
namespace TsvDirectory\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

class GetEM implements ServiceLocatorAwareInterface
{
	protected $services;
	public $em;
	
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->services;
    }
    
    function __construct($sm) {

    	$this->em = $sm->get('doctrine.entitymanager.orm_default');
    	return $this->em;
    }

}

<?php
namespace TsvDirectory\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

class ContentViewModel implements ServiceLocatorAwareInterface
{

	protected $services;
	public $vm;
	
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->services;
    }
    
    public function setVariable($name, $val)
    {
    	return $this->vm->setVariable($name, $val);
    }
    
    function __construct($sm) {

    	$vm = new ViewModel();
    	
    	$this->vm = $vm;
    	
    	return $this->vm;
    }

}

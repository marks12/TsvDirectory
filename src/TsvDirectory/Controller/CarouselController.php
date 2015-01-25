<?php

namespace TsvDirectory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use TsvDirectory\Entity\TsvCarouselElement;

/**
 * @author
 * @version 
 */

class CarouselController extends AbstractActionController
{
    public function carouselAddPageAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $request = $this->getRequest();
        $vm = new ViewModel();
        
    	if (!$request->isPost()) 
    	{
    		return $this->redirect()->toUrl("/admin/tsvDirectory");
    	}

    	$back = (int)$request->getPost()->selected_back;
    	$CarouselContent = $request->getPost()->CarouselContent;
    	$carousel_id = (int)$this->getEvent()->getRouteMatch()->getParam('carousel_id');

    	
    	$carousel = $em->getRepository('TsvDirectory\Entity\TsvCarousel')->find($carousel_id);
    	
    	if(!$carousel)
    		$vm->setVariable("error", "Вы пытаетесь создать страницу для несуществующей карусели. Возможно предыдущая страница устарела. Пожалуйста перейдите в раздел администрирования карусели и попробуйте повторить операцию.");
    	
    	$page = new TsvCarouselElement();
    	$page->__set("content",$CarouselContent);
    	
   		$back_obj = $em->getRepository('TsvDirectory\Entity\TsvCarouselImage')->find($back);
   		
    	if($back_obj)
	    	$page->__get("Background")->add($back_obj);

    	$page->__set('TsvCarousel',$carousel);
    	
    	$em->persist($page);
    	$em->flush();
    	
    	if($carousel)
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$carousel->__get('Content')->__get('Section')->__get('id')."#tab-".$carousel->__get('Content')->__get('id'));
    	
    	return $vm;
    }
    
    
    public function carouselEditPageAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $request = $this->getRequest();
        $vm = new ViewModel();
        
        $page_id = (int)$this->getEvent()->getRouteMatch()->getParam('page_id');
        $page = $em->getRepository('TsvDirectory\Entity\TsvCarouselElement')->find($page_id);
        
        if(!$page)
        {
        	$vm->setVariable("error", "Вы пытаетесь изменить несуществующую страницу. Возможно предыдущая страница устарела. Пожалуйста перейдите в раздел администрирования карусели и попробуйте повторить операцию.");
        	return $vm;
        }
        
    	if (!$request->isPost()) 
    	{
    		return $this->redirect()->toUrl("/admin/tsvDirectory");
    	}

    	$back = (int)$request->getPost()->selected_back;
    	$CarouselContent = $request->getPost()->CarouselContent;
    	$carousel_id = (int)$this->getEvent()->getRouteMatch()->getParam('carousel_id');

    	
    	$carousel = $em->getRepository('TsvDirectory\Entity\TsvCarousel')->find($carousel_id);
    	
    	if(!$carousel)
    	{
    		$vm->setVariable("error", "Вы пытаетесь изменить страницу для несуществующей карусели. Возможно предыдущая страница устарела. Пожалуйста перейдите в раздел администрирования карусели и попробуйте повторить операцию.");
    		return $vm;
    	}

    	$page->__set("content",$CarouselContent);

    	foreach ($page->__get("Background") as $old_back)
    		$page->__get("Background")->removeElement($old_back);
    	 
   		$back_obj = $em->getRepository('TsvDirectory\Entity\TsvCarouselImage')->find($back);
   		
    	if($back_obj)
	    	$page->__get("Background")->add($back_obj);

    	$em->persist($page);
    	$em->flush();
    	
    	if($carousel)
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$carousel->__get('Content')->__get('Section')->__get('id')."#tab-".$carousel->__get('Content')->__get('id'));
    	
    	return $vm;
    }
    
    
    
    public function carouselRemovePageAction()
    {
    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $request = $this->getRequest();
        $vm = new ViewModel();
        
        $page_id = (int)$this->getEvent()->getRouteMatch()->getParam('page_id');
        $page = $em->getRepository('TsvDirectory\Entity\TsvCarouselElement')->find($page_id);
        
        if(!$page)
        {
        	$vm->setVariable("error", "Вы пытаетесь удалить несуществующую страницу. Возможно предыдущая страница устарела. Пожалуйста перейдите в раздел администрирования карусели и попробуйте повторить операцию.");
        	return $vm;
        }
        
    	$carousel_id = (int)$this->getEvent()->getRouteMatch()->getParam('carousel_id');
    	
    	$carousel = $em->getRepository('TsvDirectory\Entity\TsvCarousel')->find($carousel_id);
    	
    	if(!$carousel)
    	{
    		$vm->setVariable("error", "Вы пытаетесь удалить страницу несуществующей карусели. Возможно предыдущая страница устарела. Пожалуйста перейдите в раздел администрирования карусели и попробуйте повторить операцию.");
    		return $vm;
    	}

    	$em->remove($page);
    	$em->flush();
    	
    	if($carousel)
    		return $this->redirect()->toUrl("/admin/tsvDirectory/TsvDirectory/section/view/".$carousel->__get('Content')->__get('Section')->__get('id')."#tab-".$carousel->__get('Content')->__get('id'));
    	
    	return $vm;
    }
    
    
    
}
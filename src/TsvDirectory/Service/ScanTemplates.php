<?php
namespace TsvDirectory\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ScanTemplates implements ServiceLocatorAwareInterface
{
	protected $services;
	protected $sm;
	
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->services;
    }

    public function __construct($sm)
    {
    	$this->sm = $sm;
    }
    
    public function ScanTemplates()
    {
    	$config = $this->sm->get('Config');
    	 
    	$template_vars = array();

    	if($config['view_manager'])
    	foreach ($config['view_manager'] as $k=>$v)
    	{
    		if(preg_match("/template/is", $k))
    		{
    			$template_vars = array_merge($template_vars, $this->scanTemplateArr($v));
    		}
    	}
      	return $template_vars;
    }
    
    private function scanTemplateArr($arr)
    {
    	$content_search = array();
    	
    	if(is_array($arr))
    	foreach ($arr as $k=>$v)
    	{
    		if(file_exists($v) && is_dir($v))
    			$content_search = array_merge($content_search,$this->scanTemplateDir($v));
    		elseif(file_exists($v) && is_file($v))
    		$content_search = array_merge($content_search,$this->scanTemplateFile($v));
    	}
    
    	return $content_search;
    }
    
    public function scanTemplateDir($dir)
    {
    	$content_search = array();
    	
    	if(file_exists($dir) && is_dir($dir))
    	{
    		$d = opendir($dir);
    		while ($file = readdir($d))
    		{
    			if(!in_array($file, array(".","..")))
    				$content_search = array_merge($content_search,$this->scanTemplateFile($dir."/".$file));
    		}
    		closedir($d);
    	}
    	 
    	return $content_search;
    }
    
    public function scanTemplateFile($file)
    {
    	$content_search = array();
    	 
    	if(file_exists($file) && is_file($file))
    	{
    		$fp = fopen($file,"r");
    		while (!feof($fp))
    		{
    			$str = fgets($fp);
    			if(preg_match("#TsvdContent\((.*)\)#", $str,$match))
    			{
    				if(isset($match[1]))
    				{
    					$var = trim(htmlspecialchars(str_replace(array("'",'"'), '' , $match[1])));
    					if(mb_strlen($var))
    						$content_search[] = $var;
    				}
    			}
    		}
    		fclose($fp);
    	}
    	 
    	return $content_search;
    	 
    }
    

}

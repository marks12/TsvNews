<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/TsvNews for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace TsvNews\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use TsvNews\Entity\News;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class TsvNewsController extends AbstractActionController
{
    public function indexAction()
    {
    	$vm =  new ViewModel();
    	 
    	$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$repository = $entityManager->getRepository('TsvNews\Entity\News');
    	
    	$adapter = new DoctrineAdapter(new ORMPaginator($repository->createQueryBuilder('News')));
    	$paginator = new Paginator($adapter);
    	
    	$page = (int)$this->getEvent()->getRouteMatch()->getParam('page');
    	
    	$paginator->setCurrentPageNumber($page)
    			  ->setItemCountPerPage(10);
    	
    	$vm->setVariable('paginator',$paginator);
    	$vm->setVariable('news',$this->getNews());
    	$vm->setVariable('route',$this->getEvent()->getRouteMatch()->getMatchedRouteName());

    	return $vm;
    }

    public function convert_date()
    {
    	$request = $this->getRequest();
    	
    	if($request->getPost()->start_date)
    	{
    		$date_arr = explode(".", $request->getPost()->start_date);
    		$request->getPost()->start_date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
    	}
    	
    	if($request->getPost()->end_date)
    	{
    		$date_arr = explode(".", $request->getPost()->end_date);
    		$request->getPost()->end_date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
    	}
    	
    	if($request->getPost()->news_date)
    	{
    		$date_arr = explode(".", $request->getPost()->news_date);
    		$request->getPost()->news_date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
    	}
    }
    
    public function editAction()
    {
    	$request = $this->getRequest();
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	$vm =  new ViewModel();
    	
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			)
    	);
    	
    	if ($request->isPost()) {
    	
    		if(isset($request->getPost()->title) && $request->getPost()->content)
    		{
				$this->convert_date();
    			$news->__set("title", $request->getPost()->title);
    			$news->__set("content", $request->getPost()->content);
    			$news->__set("start_date", new \DateTime($request->getPost()->start_date));
    			$news->__set("end_date", new \DateTime($request->getPost()->end_date));
    			$news->__set("news_date", new \DateTime($request->getPost()->news_date));
    			$objectManager->persist($news);
    			$objectManager->flush();
    	
    			return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
    		}
    	
    	}
    	else 
    	{

    		$vm->setVariable('title',$news->__get("title"));
    		$vm->setVariable('content',$news->__get("content"));
    		$vm->setVariable('start_date',$news->__get("start_date"));
    		$vm->setVariable('end_date',$news->__get("end_date"));
    		$vm->setVariable('news_date',$news->__get("news_date"));
    		
    	}
    	 
    	return $vm;
    }
    
    public function deleteAction()
    {

        $request = $this->getRequest();
        $objectManager = $this
        ->getServiceLocator()
        ->get('Doctrine\ORM\EntityManager');
        $vm =  new ViewModel();
         
        $news = $objectManager
        ->getRepository('TsvNews\Entity\News')
        ->findOneBy(
        		array(
        				'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
        		));

    	$objectManager->remove($news);
    	$objectManager->flush();
        
        return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
        
    	return array();
    }
    
    
    public function addAction()
    {
        $request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		if(isset($request->getPost()->title) && $request->getPost()->content)
    		{
    			$objectManager = $this
    			->getServiceLocator()
    			->get('Doctrine\ORM\EntityManager');
    			
				$this->convert_date();
    			    			
    			$news = new News();
    			$news->__set("title", $request->getPost()->title);
    			$news->__set("content", $request->getPost()->content);
    			$news->__set("disabled_news", false);
    			$news->__set("start_date", new \DateTime($request->getPost()->start_date));
    			$news->__set("end_date", new \DateTime($request->getPost()->end_date));
    			$news->__set("news_date", new \DateTime($request->getPost()->news_date));
    			$objectManager->persist($news);
    			$objectManager->flush();

    			return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
    		}
    		
    	}
    	
    	return array();
    }
    
    private function getNews()
    {
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	 
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findAll();
    	 
    	return $news;
    }
    
    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /tsvNews/tsv-news/foo
        return array();
    }
}

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
use Zend\View\Model\ViewModel;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use TsvNews\Entity\News;
use Zend\Escaper\Escaper;
use Zend\Db\Sql\Ddl\Column\Integer;

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
    		
    		if($request->getFiles() && $request->getFiles()['image']['size'])
	    		if(!$news->__set('image',$request->getFiles()['image'])){
	    			exit('File upload error');
	    		}
    		
    		if(isset($request->getPost()->title) && $request->getPost()->content)
    		{
				$this->convert_date();
    			$news->__set("title", $request->getPost()->title);
    			$news->__set("content", $request->getPost()->content);
    			$news->__set("short_content", $request->getPost()->short_content);
    			$news->__set("start_date", new \DateTime($request->getPost()->start_date));
    			$news->__set("end_date", new \DateTime($request->getPost()->end_date));
    			$news->__set("news_date", new \DateTime($request->getPost()->news_date.' '.$request->getPost()->news_time));
    			$objectManager->persist($news);
    			$objectManager->flush();
    	
    			return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
    		}
    	
    	}
    	else 
    	{

    		$vm->setVariable('title',$news->__get("title"));
    		$vm->setVariable('content',$news->__get("content"));
    		$vm->setVariable('short_content',$news->__get("short_content"));
    		$vm->setVariable('start_date',$news->__get("start_date"));
    		$vm->setVariable('end_date',$news->__get("end_date"));
    		$vm->setVariable('news_date',$news->__get("news_date"));
    		$vm->setVariable('image',$news->__get("image"));
    		
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

        $news->deleteImage();
        
    	$objectManager->remove($news);
    	$objectManager->flush();
        
        return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
        
    	return array();
    }
    
    
    public function addAction()
    {
        $request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		if(isset($request->getPost()->title) && $request->getPost()->short_content)
    		{
    			$objectManager = $this
    			->getServiceLocator()
    			->get('Doctrine\ORM\EntityManager');
    			
				$this->convert_date();
    			    			
				$news = new News();
				
				if($request->getFiles() && $request->getFiles()['image']['size'])
					if(!$news->__set('image',$request->getFiles()['image']))
						exit('File upload error');

				$news->__set("title", $request->getPost()->title);
    			$news->__set("content", $request->getPost()->content);
    			$news->__set("short_content", $request->getPost()->short_content);
    			$news->__set("disabled_news", false);
    			$news->__set("start_date", new \DateTime($request->getPost()->start_date));
    			$news->__set("end_date", new \DateTime($request->getPost()->end_date));
    			$news->__set("news_date", new \DateTime($request->getPost()->news_date.' '.$request->getPost()->news_time));
    			$objectManager->persist($news);
    			$objectManager->flush();

    			return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator");
    		}
    		
    	}
    	
    	return array();
    }
    
    public function disableNewsAction()
    {
    	$request = $this->getRequest();
    	
//     	exit('disable');
//     	echo (int)$this->getEvent()->getRouteMatch()->getParam('id');
    	
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			));
    	
    	$news->__set("disabled_news", true);
    	
    	$objectManager->persist($news);
    	$objectManager->flush();
    	
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			));
//     	var_dump($news->__get('disabled_news'));
    	 
        return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator",array("page"=>(int)$this->getEvent()->getRouteMatch()->getParam('page')));
    }
    
    
    public function enableNewsAction()
    {
    	$request = $this->getRequest();
    	
//     	exit('enable');
    	
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			));

    	var_dump($news->__get('disabled_news'));
    	 
    	$news->__set("disabled_news", false);
    	
    	$objectManager->persist($news);
    	$objectManager->flush();
    	
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$this->getEvent()->getRouteMatch()->getParam('id')
    			));
//     	var_dump($news->__get('disabled_news'));
    	 
        return $this->redirect()->toRoute("zfcadmin/tsv-news/paginator",array("page"=>(int)$this->getEvent()->getRouteMatch()->getParam('page')));
    	    	
    }
    
    private function getNews($count_news=5)
    {
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	 
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findAll();
    	
    	return $news;
    }
    
    public function getArchiveList()
    {
    	$em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    	$query = $em->createQuery("SELECT n FROM TsvNews\Entity\News n WHERE 
    			n.disabled_news = 0 and 
    			n.news_date < '".date("Y-m-01")."' order by n.news_date desc");

    	$news = $query->getResult();

    	$archive_list = array();
    	foreach ($news as $n)
    	{
    		$date = $n->__get('news_date');
    		$n->news_date = $n->__get('news_date');
    		
    		$archive_list[$date->format("Y")][$date->format("F")][$n->__get('id')] = $n;
    	}
    	
    	return $archive_list;
    	
    }
    
    public function getNewsByPage($archive_date = '')
    {
    	$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
    	$repository = $entityManager->getRepository('TsvNews\Entity\News')->getFilteredNews($archive_date,$entityManager);
    	$adapter = new DoctrineAdapter(new ORMPaginator($repository, $fetchJoinCollection = true));

    	$paginator = new Paginator($adapter);
    	$paginator->setDefaultItemCountPerPage(5);
    	
    	$page = (int)$this->getEvent()->getRouteMatch()->getParam('page');
    	if($page) $paginator->setCurrentPageNumber($page);
    	
    	return $paginator;
    	
    }
    public function getNewsById($id)
    {
    	$objectManager = $this
    	->getServiceLocator()
    	->get('Doctrine\ORM\EntityManager');
    	 
    	$news = $objectManager
    	->getRepository('TsvNews\Entity\News')
    	->findOneBy(
    			array(
    					'id' => (int)$id
    			)
    	);
    	
    	return $news;
    	
    }
    
    public function getNewsList($count_news=5)
    {
    	$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	
   		$dql = "SELECT n from TsvNews\Entity\News n where n.disabled_news=:disabled_news and n.start_date<=:date and n.end_date>=:date";

    	$query = $entityManager->createQuery($dql)
						    	->setFirstResult(0)
						    	->setMaxResults($count_news);
    	$query->setParameters(array(
    			'date' => date("Y-m-d"),
    			'disabled_news' => '0',
    	));
    	
    	$news = $query->getResult();
    	
    	return $news;
    }
    
    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /tsvNews/tsv-news/foo
        return array();
    }
    

}

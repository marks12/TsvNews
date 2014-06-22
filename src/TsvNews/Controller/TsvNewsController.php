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
    	$paginator->setDefaultItemCountPerPage(1);
    	
    	$page = (int)$this->getEvent()->getRouteMatch()->getParam('page');
    	
    	if($page) $paginator->setCurrentPageNumber($page);
    	
    	$vm->setVariable('paginator',$paginator);
    	$vm->setVariable('news',$this->getNews());
    	   	
    	return $vm;
    }

    public function addAction()
    {
    	
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

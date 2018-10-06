<?php

namespace TsvNews\Repository;

use Doctrine\ORM\EntityRepository;

class TsvNewsRepository extends EntityRepository
{
	public function getFilteredNews($archive_date = '', $em)
	{
		if($archive_date=='')
			$query = $em->createQuery("SELECT n FROM TsvNews\Entity\News n WHERE n.disabled_news = 0 and n.start_date <= '".date("Y-m-d H:i:s")."' and n.end_date >= '".date("Y-m-d H:i:s")."'  order by n.news_date DESC");
		else 
			$query = $em->createQuery("SELECT n FROM TsvNews\Entity\News n WHERE n.disabled_news = 0 and n.news_date >= '".date("Y-m-d H:i:s",strtotime($archive_date.'-01'))."' and n.news_date <= '".date("Y-m-01 H:i:s",strtotime($archive_date.'-01')+60*60*24*32)."' order by n.news_date DESC");
		
		return $query;
	}
}
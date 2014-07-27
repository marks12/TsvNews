<?php

namespace TsvNews\Repository;

use Doctrine\ORM\EntityRepository;

class TsvNewsRepository extends EntityRepository
{
	public function getFilteredNews($archive_date = '', $em)
	{
		return $em->createQuery("SELECT n FROM TsvNews\Entity\News n");
	}
}
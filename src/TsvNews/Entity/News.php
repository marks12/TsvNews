<?php
namespace TsvNews\Entity;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class News {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	protected $id;
	
	/** @ORM\Column(type="string") */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $content;
	
	/** @ORM\Column(type="boolean") */
	protected $disabled;
	
	/** @ORM\Column(type="datetime") */
	protected $start_date;
	
	/** @ORM\Column(type="datetime") */
	protected $end_date;
	
	/** @ORM\Column(type="date") */
	protected $news_date;
	
    /**
     * Magic getter
     * @param $property
     * @return mixed
     */
    public function __get($key)
    {
    	if(property_exists($this, $key))
    	return $this->{$key};
    	else
    	die("Requested property {$key} not exists");
    }
    
    /**
     * Magic setter
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
    	if(property_exists($this, $key))
    	$this->{$key} = $value;
    	else
    	die("Requested property {$key} not exists");
    }
}
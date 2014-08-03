<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'TsvNews\Controller\TsvNews' => 'TsvNews\Controller\TsvNewsController',
        ),
    ),
    'router' => array(
        'routes' => array(
       		'zfcadmin' => array(
   				'child_routes' => array(
			            'tsv-news' => array(
			                'type'    => 'Literal',
			                'options' => array(
			                    // Change this to something specific to your module
			                    'route'    => '/tsvNews',
			                    'defaults' => array(
			                        // Change this value to reflect the namespace in which
			                        // the controllers for your module are found
			                        '__NAMESPACE__' => 'TsvNews\Controller',
			                        'controller'    => 'TsvNews',
			                        'action'        => 'index',
			                    ),
			                ),
			                'may_terminate' => true,
			                'child_routes' => array(
			                    // This route is a sane default when developing a module;
			                    // as you solidify the routes for your module, however,
			                    // you may want to remove it and replace it with more
			                    // specific routes.
			                    'default' => array(
			                        'type'    => 'Segment',
			                        'options' => array(
			                            'route'    => '/[:action[/:id[/:page]]]',
			                            'constraints' => array(
			                            	'page'		 => '[0-9]*',
			                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
			                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
			                            ),
			                            'defaults' => array(
			                            	'page'		=> '0',
		                            		'__NAMESPACE__' => 'TsvNews\Controller',
		                            		'controller'    => 'TsvNews',
			                            ),
			                        ),
			                    ),
		                		'paginator' => array(
		                				'type'    => 'Segment',
		                				'options' => array(
		                						'route'    => '/[:page]',
		                						'constraints' => array(
		                								'page'		 => '[0-9]*',
		                						),
		                						'defaults' => array(
		                						),
		                				),
		                		),
			                ),
			            ),
        			),
	    		),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'TsvNews' => __DIR__ . '/../view',
        ),
		'template_map' => array(
			'tsv-news/news_pagination'		=> __DIR__ . '/../view/partials/news_pagination.phtml',
		),
    ),
	'navigation' => array(
			'admin' => array(
					'news' => array(
							'label' => 'Новости',
							'route' => 'zfcadmin/tsv-news/paginator',
					),
			),
	),
	'doctrine' => array(
		'driver' => array(
			'tsvnews_entities' => array(
				'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
					'cache' => 'array',
					'paths' => array(__DIR__ . '/../src/TsvNews/Entity'),
			),
			'tsvnews_repo' => array(
					'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
					'cache' => 'array',
					'paths' => array(__DIR__ . '/../src/TsvNews/Repository'),
			),
			'orm_default' => array(
				'drivers' => array(
					'TsvNews\Entity' => 'tsvnews_entities',
				),
			),
		),
	),
		
);

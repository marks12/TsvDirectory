<?php


return array(
    'controllers' => array(
        'invokables' => array(
            'TsvDirectory\Controller\TsvDirectory' => 'TsvDirectory\Controller\TsvDirectoryController',
            'TsvDirectory\Controller\ContentProvider' => 'TsvDirectory\Controller\ContentProvider',
        ),
    ),
    'router' => array(
        'routes' => array(
    		'zfcadmin' => array(
				'child_routes' => array(
					'tsv-directory' => array(
						'type'    => 'Literal',
						'options' => array(
							// Change this to something specific to your module
							'route'    => '/tsvDirectory',
							'defaults' => array(
								// Change this value to reflect the namespace in which
								// the controllers for your module are found
									'__NAMESPACE__' => 'TsvDirectory\Controller',
									'controller'    => 'TsvDirectory',
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
										'route'    => '/[:controller[/:action]]',
										'constraints' => array(
												'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
									'defaults' => array(
									),
								),
							),
							'paginator' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/:action[/:page]',
										'constraints' => array(
											'page' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'sections',
										),
								),
							),
							'editor' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/:action/edit/:id',
										'constraints' => array(
											'id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'editSection',
										),
								),
							),
							'remover' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/:action/remove/:id',
										'constraints' => array(
											'id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'removeSection',
										),
								),
							),
							'section-view' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/view/:id',
										'constraints' => array(
											'id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'viewSection',
										),
								),
							),
							'section-add-content' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/add/:section_id/:content_type',
										'constraints' => array(
											'section_id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'content_type' => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'addContent',
										),
								),
							),
							'section-edit-content' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/edit/:content_type/:section_id/:content_entity_id/:content_id',
										'constraints' => array(
											'content_id' => '[0-9]*',
											'section_id' => '[0-9]*',
											'content_entity_id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'content_type' => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'editContent',
										),
								),
							),
							'section-remove-content' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/remove/:content_type/:section_id/:content_id',
										'constraints' => array(
											'content_id' => '[0-9]*',
											'section_id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'content_type' => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'removeContent',
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
            'TsvDirectory' => __DIR__ . '/../view',
        ),
    	'template_map' => array(
        	'partials/section-list'		=> __DIR__ . '/../view/tsv-directory/partials/section-list.phtml',
        	'partials/TsvText'			=> __DIR__ . '/../view/tsv-directory/partials/tsv-text.phtml',
        	'partials/ViewTsvText'		=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-text.phtml',
        	'tsv-directory/add-content'		=> __DIR__ . '/../view/tsv-directory/tsv-directory/add-content.phtml',
        ),
    ),
	'navigation' => array(
			'admin' => array(
				'directory' => array(
						'label' => 'Управление данными',
						'route' => 'zfcadmin/tsv-directory',
				),
			),
			'submenu' => array(
					'directory' => array(
							'label' => 'Управление разделами',
							'route' => 'zfcadmin/tsv-directory/section',
					),
			),
	),
	'doctrine' => array(
		'driver' => array(
			'tsvdirectory_entities' => array(
				'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
					'cache' => 'array',
					'paths' => array(__DIR__ . '/../src/TsvDirectory/Entity'),
			),
			'orm_default' => array(
				'drivers' => array(
					'TsvDirectory\Entity' => 'tsvdirectory_entities',
				),
			),
		),
	),
);

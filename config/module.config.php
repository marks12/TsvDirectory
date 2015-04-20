<?php


return array(
    'controllers' => array(
        'invokables' => array(
            'TsvDirectory\Controller\TsvDirectory' 	=> 'TsvDirectory\Controller\TsvDirectoryController',
            'TsvDirectory\Controller\Carousel' 		=> 'TsvDirectory\Controller\CarouselController',
            'TsvDirectory\Controller\TsvTable' 		=> 'TsvDirectory\Controller\TsvTableController',
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
							'table' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/table[/:action[/:id]]',
										'constraints' => array(
											'directory-name' => '[a-zA-Z0-9_-]*',
											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvTable',
											'action'		=>	'index',
										),
								),
								'may_terminate' => true,
							),
							'uploader' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/uploader/:id[/:entity_parent[/:entity_store]]',
										'constraints' => array(
											'id' => '[0-9]*',
											'entity_parent' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'entity_store' => '[a-zA-Z][a-zA-Z0-9_-]*',
// 											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
// 											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'uploader',
											'entity_parent'	=>	'TsvFile',
											'entity_store'	=>	'TsvFileElement',
										),
								),
							),
							'uploader-form' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/uploader-form/:id[/:entity_parent[/:entity_store]]',
										'constraints' => array(
											'id' => '[0-9]*',
											'entity_parent' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'entity_store' => '[a-zA-Z][a-zA-Z0-9_-]*',
// 											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
// 											'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'TsvDirectory',
											'action'		=>	'uploaderForm',
											'entity_parent'	=>	'TsvFile',
											'entity_store'	=>	'TsvFileElement',
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
										'route'    => '/:controller/section/content/add/:section_id/:content_type/:b64TsvKey',
										'constraints' => array(
											'section_id' => '[0-9]*',
											'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'content_type' => '[a-zA-Z][a-zA-Z0-9_-]*',
											'b64TsvKey' => '[a-zA-Z0-9=%_-]*',
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
							'carousel-add-page' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/carousel/add/page/:carousel_id',
										'constraints' => array(
											'carousel_id' => '[0-9]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'Carousel',
											'action'		=>	'carouselAddPage',
										),
								),
							),
							'carousel-edit-page' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/carousel/edit/page/:carousel_id/:page_id',
										'constraints' => array(
											'carousel_id' => '[0-9]*',
											'page_id' => '[0-9]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'Carousel',
											'action'		=>	'carouselEditPage',
										),
								),
							),
							'carousel-remove-page' => array(
								'type'    => 'Segment',
								'options' => array(
										'route'    => '/:controller/section/content/carousel/remove/page/:carousel_id/:page_id',
										'constraints' => array(
											'carousel_id' => '[0-9]*',
											'page_id' => '[0-9]*',
										),
										'defaults' => array(
											'__NAMESPACE__'	=>	'TsvDirectory\Controller',
											'controller'	=>	'Carousel',
											'action'		=>	'carouselRemovePage',
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
        	'partials/uploader-form'	=> __DIR__ . '/../view/tsv-directory/partials/uploader-form.phtml',
    		'partials/section-list'		=> __DIR__ . '/../view/tsv-directory/partials/section-list.phtml',
        	'tsv-directory/add-content'	=> __DIR__ . '/../view/tsv-directory/tsv-directory/add-content.phtml',
        	'layout/empty'				=> __DIR__ . '/../view/tsv-directory/layout/empty.phtml',
   			'partials/add-content-btn'	=> __DIR__ . '/../view/tsv-directory/partials/add-content-btn.phtml',

    		//templates for view in admin
    		'partials/ViewTsvText'		=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-text.phtml',
   			'partials/ViewTsvStext'		=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-stext.phtml',
   			'partials/ViewTsvFile'		=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-file.phtml',
   			'partials/ViewTsvOneFile'	=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-onefile.phtml',
   			'partials/ViewTsvCarousel'	=> __DIR__ . '/../view/tsv-directory/partials/view-tsv-carousel.phtml',
    			 
    			
    		//templates for edit in admin
   			'partials/TsvText'			=> __DIR__ . '/../view/tsv-directory/partials/tsv-text.phtml',
   			'partials/TsvFile'			=> __DIR__ . '/../view/tsv-directory/partials/tsv-file.phtml',
   			'partials/TsvStext'			=> __DIR__ . '/../view/tsv-directory/partials/tsv-stext.phtml',
   			'partials/TsvCarousel'		=> __DIR__ . '/../view/tsv-directory/partials/tsv-carousel.phtml',
    		'partials/TsvOneFile'		=> __DIR__ . '/../view/tsv-directory/partials/tsv-onefile.phtml',
    			 
    		// templates for view in FRONT END
    		'partials/helper/TsvStext'		=> __DIR__ . '/../view/tsv-directory/partials/helper/tsv-stext.phtml',
    		'partials/helper/TsvCarousel'	=> __DIR__ . '/../view/tsv-directory/partials/helper/tsv-carousel.phtml',
    		'partials/helper/TsvText'		=> __DIR__ . '/../view/tsv-directory/partials/helper/tsv-text.phtml',
    		'partials/helper/TsvFile'		=> __DIR__ . '/../view/tsv-directory/partials/helper/tsv-file.phtml',
    		'partials/helper/TsvOneFile'	=> __DIR__ . '/../view/tsv-directory/partials/helper/tsv-onefile.phtml',
    			
        ),
    ),
	'navigation' => array(
			'admin' => array(
				'directory' => array(
						'label' => 'Управление данными',
						'route' => 'zfcadmin/tsv-directory',
				),
				'table' => array(
						'label' => 'Справочники',
						'route' => 'zfcadmin/tsv-directory/table',
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
	'view_helpers' => array(
			'invokables'=> array(
// 					'TsvdContent' => 'TsvDirectory\View\Helper\TsvdContent',
			)
	),
	'bjyauthorize' => array(
			'guards' => array(
				'BjyAuthorize\Guard\Controller' => array(
						
						array(
								'controller' => 'TsvDirectory\Controller\Carousel',
								'roles'	=> array('admin'),
						),
						array(
								'controller' => 'TsvDirectory\Controller\TsvDirectory',
								'roles'	=> array('admin'),
						),
						array(
								'controller' => 'TsvDirectory\Controller\TsvTable',
								'roles'	=> array('admin'),
						),

			),
		),
	),
);

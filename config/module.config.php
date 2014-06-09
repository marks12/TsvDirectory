<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'TsvDirectory\Controller\TsvDirectory' => 'TsvDirectory\Controller\TsvDirectoryController',
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
    ),
	'navigation' => array(
			'admin' => array(
				'directory' => array(
						'label' => 'Управление данными',
						'route' => 'zfcadmin/tsv-directory',
				),
			),
	),
);

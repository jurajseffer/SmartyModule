<?php

return array(

	'alias' => array(
		'view' => 'SmartyModule\View\Renderer\SmartyRenderer',
	),
	
	
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'defaultSuffix'			   => '.tpl',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
		'template_map' => array(
		    'layout/layout'           => __DIR__ . '/../../Application/view/layout/layout.tpl',
		    'error/404'               => __DIR__ . '/../../Application/view/error/404.tpl',
		    'error/index'             => __DIR__ . '/../../Application/view/error/index.tpl',
		    'application/index/index' => __DIR__ . '/../../Application/view/application/index/index.tpl',
		),
		'template_path_stack' => array(
		    __DIR__ . '/../../Application/view',
		),
	),


	'di' => array(
	    'instance' => array(
	        'alias' => array(
	            // entity manager
	            'smarty_engine' => 'Smarty',
	        ),
	
	        'SmartyModule\View\Renderer\SmartyStrategy' => array(
	            'parameters' => array(
	                'smarty' => 'SmartyModule\View\Renderer\SmartyRenderer',
	            ),
	        ),
	        'SmartyModule\View\Renderer\SmartyRenderer' => array(
	            'parameters' => array(
	                'smarty' => 'smarty_engine',
	            ),
	        ),
	
	        'smarty_engine' => array(
	            'parameters' => array(
	                'compile_dir' => __DIR__ . '/../../../data/SmartyModule/templates_c',
	            )
	        )
	    ),
	),
);


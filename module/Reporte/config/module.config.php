<?php

return array(
    'router' => array(
	        'routes' => array(

	        		'reportes' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route'    => '/reportes[/][:action][/imprimirpdf/:imprimirpdf][/codcicloacademico/:codcicloacademico][/codcurso/:codcurso][/codmodalidad/:codmodalidad][/paralelo/:paralelo][/coddocente/:coddocente]',
										'constraints' => array (
												'action' 				=> '[a-zA-Z][a-zA-Z0-9_-]*',
												'imprimirpdf' 			=> 'si',												
												'codcicloacademico' 	=> '[0-9]*',
												'codcurso' 				=> '[0-9]*',
												'codmodalidad' 			=> '[0-9]*',
												'paralelo'				=> '[a-zA-Z]',
												'coddocente' 			=> '[0-9]*',
										),
										'defaults' => array (
												'controller' => 'Reporte\Controller\Index',
												'action' => 'index'
										) 
								) 
						),

	        ),
    ),
    
    'service_manager' => array(

    ),    
    
    'controllers' => array(
        'invokables' => array(
            'Reporte\Controller\Index' => 'Reporte\Controller\IndexController'
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'reporte' => __DIR__ . '/../view',
        ),
    ),
);
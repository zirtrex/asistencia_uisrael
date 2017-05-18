<?php

return array(
    'router' => array(
	        'routes' => array(

	        		'reportes' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route'    => '/reportes[/][:action][/imprimirpdf/:imprimirpdf][/escomun/:escomun][/codareaconocimiento/:codareaconocimiento][/codcarreraprofesional/:codcarreraprofesional][/codcicloacademico/:codcicloacademico][/codcurso/:codcurso][/codmodalidad/:codmodalidad][/paralelo/:paralelo][/codaula/:codaula][/codseccion/:codseccion][/coddocente/:coddocente][/fechainicio/:fechainicio][/fechafin/:fechafin]',
										'constraints' => array (
												'action' 				=> '[a-zA-Z][a-zA-Z0-9_-]*',
												'imprimirpdf' 			=> 'si',
												'escomun' 				=> 'Si|No',
												'codareaconocimiento' 	=> '[0-9]*',
												'codcarreraprofesional' => '[0-9]*',
												'codcicloacademico' 	=> '[0-9]*',
												'codcurso' 				=> '[0-9]*',
												'codmodalidad' 			=> '[0-9]*',
												'paralelo'				=> '[a-zA-Z][a-zA-Z0-9_-]*',							
												'codaula'	 			=> '[0-9]*',
												'codseccion' 			=> '[0-9]*',
												'coddocente' 			=> '[0-9]*',
												'fechainicio' 			=> '[0-9-]*',
												'fechafin' 				=> '[0-9-]*',
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

<?php

return array(
    'router' => array(
        'routes' => array(
        		        		
        		'cursos' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Literal',
        				'options' => array(
        						'route'    => '/asistencia/cursos',
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'index',
        						),
        				),
        		),
        		
        		'iniciar-sesion' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/cursos/iniciar-sesion[/codcicloacademico/:codcicloacademico/codcurso/:codcurso/codmodalidad/:codmodalidad/paralelo/:paralelo/codaula/:codaula/codseccion/:codseccion]',
        						'constraints' => array(        								
        								'codcicloacademico'   		=> '[0-9]+',
        								'codcurso'   				=> '[0-9]+',
        								'codmodalidad'   			=> '[0-9]+',
        								'paralelo'					=> '[a-zA-Z]',
        								'codaula'   				=> '[0-9]+',
        								'codseccion'   				=> '[0-9]+',
        						),
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'iniciar-sesion',
        						),
        				),
        		),
        		
        		'estudiantes' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/estudiantes[/:action/codsesion/:codsesionclase/codcicloacademico/:codcicloacademico/codcurso/:codcurso/codmodalidad/:codmodalidad/paralelo/:paralelo/codaula/:codaula/codseccion/:codseccion]',
        						'constraints' => array(
        								'action'     				=> '[a-zA-Z][a-zA-Z0-9_-]*',
        								'codsesionclase'   			=> '[0-9]+',
        								'codcicloacademico'   		=> '[0-9]+',
        								'codcurso'   				=> '[0-9]+',
        								'codmodalidad'   			=> '[0-9]+',
        								'paralelo'					=> '[a-zA-Z]',
        								'codaula'   				=> '[0-9]+',        								
        								'codseccion'   				=> '[0-9]+',
        						),
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'index',
        						),
        				),
        		),
        		
        		'registrar-asistencia' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/registrar',
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'registrar-asistencia',
        						),
        				),
        		),
        		
        		'avance-silabo' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/silabo[/:action/codsesion/:codsesionclase/codcicloacademico/:codcicloacademico/codcurso/:codcurso/codmodalidad/:codmodalidad/paralelo/:paralelo/codaula/:codaula/codseccion/:codseccion]',
        						'constraints' => array(
        								'action'     				=> '[a-zA-Z][a-zA-Z0-9_-]*',
        								'codsesionclase'   			=> '[0-9]+',
        								'codcicloacademico'   		=> '[0-9]+',
        								'codcurso'   				=> '[0-9]+',
        								'codmodalidad'   			=> '[0-9]+',
        								'paralelo'					=> '[a-zA-Z]',
        								'codaula'   				=> '[0-9]+',
        								'codseccion'   				=> '[0-9]+',
        						),
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'index',
        						),
        				),
        		),
        		
        		'registrar-avance' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/avance',
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'registrar-avance',
        						),
        				),
        		),
        		
        		'cerrar-sesion' => array(
        				'type'    => 'Zend\Mvc\Router\Http\Segment',
        				'options' => array(
        						'route'    => '/asistencia/cerrar-sesion[/:action/codsesion/:codsesionclase/codcicloacademico/:codcicloacademico/codcurso/:codcurso/codmodalidad/:codmodalidad/paralelo/:paralelo/codaula/:codaula/codseccion/:codseccion]',
        						'constraints' => array(
        								'action'     				=> '[a-zA-Z][a-zA-Z0-9_-]*',
        								'codsesionclase'   			=> '[0-9]+',
        								'codcicloacademico'   		=> '[0-9]+',
        								'codcurso'   				=> '[0-9]+',
        								'codmodalidad'   			=> '[0-9]+',
        								'paralelo'					=> '[a-zA-Z]',
        								'codaula'   				=> '[0-9]+',
        								'codseccion'   				=> '[0-9]+',
        						),
        						'defaults' => array(
        								'controller' => 'Asistencia\Controller\Index',
        								'action'     => 'index',
        						),
        				),
        		),
        ),
    ),
    
    'service_manager' => array(

    ),    
    
    'controllers' => array(
        'invokables' => array(
            'Asistencia\Controller\Index' => 'Asistencia\Controller\IndexController'
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'asistencia' => __DIR__ . '/../view',
        ),
    ),
);

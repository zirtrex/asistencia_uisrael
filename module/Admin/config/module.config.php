<?php
return array (
		'router' => array (
				'routes' => array (
						// Route principal para cargar la raíz de la aplicación.
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'Admin\Controller\Index',
												'action' => 'index' 
										) 
								) 
						),
						
						'aula' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/aula[/][:action][/codaula/:codaula][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codaula' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Aula',
												'action' => 'index'
										) 
								) 
						),

						'administrador' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/administrador[/][:action][/codadministrador/:codadministrador][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codadministrador' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC'  
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Administrador',
												'action' => 'index' 
										) 
								) 
						),
						
						'area-conocimiento' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/area-conocimiento[/][:action][/codareaconocimiento/:codareaconocimiento][/page/:page][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codareaconocimiento' => '[0-9]+',
												'page' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC'
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\AreaConocimiento',
												'action' => 'index'
										)
								)
						),
						
						'carrera-profesional' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/carrera-profesional[/][:action][/codcarreraprofesional/:codcarreraprofesional][/page/:page][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcarreraprofesional' => '[0-9]+',
												'page' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC'
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\CarreraProfesional',
												'action' => 'index' 
										) 
								) 
						),
						
						'carga-masiva' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/carga-masiva[/][:action][/file/:file]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'file' => '[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\CargaMasiva',
												'action' => 'index' 
										) 
								) 
						),						
						
						'ciclo-academico' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/ciclo-academico[/][:action][/codcicloacademico/:codcicloacademico][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcicloacademico' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\CicloAcademico',
												'action' => 'index' 
										) 
								) 
						),

						'curso' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/curso[/][:action][/codcurso/:codcurso][/page/:page][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' 	=> '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcurso' 	=> '[0-9]+',
												'page' 		=> '[0-9]+',
												'orderby' 	=> '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' 	=> 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Curso', 
												'action' => 'index',
										), 
								) ,
						),

						'curso-aperturado' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/curso-aperturado[/][:action][/codcurso/:codcurso]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcurso' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\CursoAperturado',
												'action' => 'index' 
										) 
								) 
						),
						
						'carga-academica' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/carga-academica[/][:action][/codcicloacademico/:codcicloacademico][/codcurso/:codcurso][/codmodalidad/:codmodalidad][/paralelo/:paralelo][/page/:page][/orderby/:orderby][[/]:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcicloacademico' => '[0-9]*',
												'codcurso' => '[0-9]*',
												'codmodalidad' => '[0-9]*',
												'paralelo' => '[a-zA-Z]',
												'page' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC'
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\CargaAcademica',
												'action' => 'index'
										)
								)
						),						

						'docente' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/docente[/][:action][/coddocente/:coddocente][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'coddocente' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Docente',
												'action' => 'index' 
										) 
								) 
						),
						
						'estudiante' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/estudiante[/][:action][/codestudiante/:codestudiante][/page/:page][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codestudiante' => '[0-9]+',
												'page' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Estudiante',
												'action' => 'index' 
										) 
								) 
						),						

						'horario' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/horario[/][:action][/codhorario/:codhorario]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codhorario' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Horario',
												'action' => 'index' 
										) 
								) 
						),
						
						'matricula' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/matricula[/][:action][/codcicloacademico/:codcicloacademico/codcurso/:codcurso/codmodalidad/:codmodalidad/paralelo/:paralelo][/codestudiante/:codestudiante]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcicloacademico' => '[0-9]+',
												'codcurso' => '[0-9]+',
												'codmodalidad' => '[0-9]+',
												'paralelo' => '[a-zA-Z]',
												'codestudiante' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Matricula',
												'action' => 'index',
										) 
								) 
						), 
						
						'plan-estudio' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/plan-estudio[/][:action][/codplanestudio/:codplanestudio][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codplanestudio' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\PlanEstudio',
												'action' => 'index' 
										) 
								) 
						),						
						
						'silabo' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/silabo[/][:action][/codcurso/:codcurso][/codsemana/:codsemana][/codtematica/:codtematica][/codsilabodetalle/:codsilabodetalle]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codcurso' => '[0-9]+',
												'codsemana' => '[0-9]+',
												'codtematica' => '[0-9]+',
												'codsilabodetalle' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Silabo', 
												'action' => 'index',
										) 
								) 
						),					

						'usuario' => array (
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array (
										'route' => '/admin/usuario[/][:action][/codusuario/:codusuario][/orderby/:orderby][/:order]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'codusuario' => '[0-9]+',
												'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'order' => 'ASC|DESC'  
										),
										'defaults' => array (
												'controller' => 'Admin\Controller\Usuario',
												'action' => 'index' 
										) 
								) 
						),
						
						
				)
				 
		),
		
		'service_manager' => array (
				'abstract_factories' => array (
						'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
						'Zend\Log\LoggerAbstractServiceFactory' 
				),
				
				'aliases' => array (
						// Permite usar $this->identity() en los controladores
						'Zend\Authentication\AuthenticationService' => 'AuthService' 
				),
				
				'factories' => array (
						'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
						
						'acl' => function ($sm) {
							$config = include __DIR__ . '/acl.config.php';
							return new \Admin\Acl\Acl ( $config );
						} 
				) 
		),
		
		'module_config' => array (
				'upload_location' => __DIR__ . '/../../../public/uploads',
				'perfil_location' => __DIR__ . '/../../../public/img/perfil'
		),
		
		'translator' => array (
				'locale' => 'en_US',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		
		'controllers' => array (
				'invokables' => array (
						'Admin\Controller\Index' => 'Admin\Controller\IndexController',
						'Admin\Controller\Login' => 'Admin\Controller\LoginController',
						'Admin\Controller\CargaMasiva' => 'Admin\Controller\CargaMasivaController',
						'Admin\Controller\AreaConocimiento' => 'Admin\Controller\AreaConocimientoController',
						'Admin\Controller\CarreraProfesional' => 'Admin\Controller\CarreraProfesionalController',
						'Admin\Controller\CicloAcademico' => 'Admin\Controller\CicloAcademicoController',
						'Admin\Controller\PlanEstudio' => 'Admin\Controller\PlanEstudioController',
						'Admin\Controller\Administrador' => 'Admin\Controller\AdministradorController',
						'Admin\Controller\Docente' => 'Admin\Controller\DocenteController',
						'Admin\Controller\Estudiante' => 'Admin\Controller\EstudianteController',
						'Admin\Controller\Curso' => 'Admin\Controller\CursoController',
						'Admin\Controller\Silabo' => 'Admin\Controller\SilaboController',
						'Admin\Controller\CursoAperturado' => 'Admin\Controller\CursoAperturadoController',
						'Admin\Controller\Aula' => 'Admin\Controller\AulaController',
						'Admin\Controller\CargaAcademica' => 'Admin\Controller\CargaAcademicaController',
						'Admin\Controller\Horario' => 'Admin\Controller\HorarioController',
						'Admin\Controller\Matricula' => 'Admin\Controller\MatriculaController',
						'Admin\Controller\Usuario' => 'Admin\Controller\UsuarioController' 
				) 
		),
		
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout'         => __DIR__ . '/../view/layout/layout_users.phtml',
						'layout/admin' 			=> __DIR__ . '/../view/layout/layout_principal.phtml',
						'layout/users' 			=> __DIR__ . '/../view/layout/layout_users.phtml',
						'layout/login' 			=> __DIR__ . '/../view/layout/layout_login.phtml',
						//'admin/index/index' 	=> __DIR__ . '/../view/layout/layout_principal.phtml',
						'error/404' 			=> __DIR__ . '/../view/error/404.phtml',
						'error/index' 			=> __DIR__ . '/../view/error/index.phtml',
						'paginator' 			=> __DIR__ . '/../view/partial/paginator.phtml' 
				),
				'template_path_stack' => array (
						'admin' => __DIR__ . '/../view' 
				) 
		),
		
		'module_layouts' => array(
				'Admin' 		=> 'layout/admin',
				'Asistencia' 	=> 'layout/admin',
				'Users' 		=> 'layout/users',
				'Reporte' 		=> 'layout/admin',
		),
		
		// Placeholder for console routes
		'console' => array (
				'router' => array (
						'routes' => array () 
				) 
		),
		
		// Navigation for breadcrumb
		'navigation' => array (
				'default' => array (
						// Navegación para el backend
						array (
								'label' => 'Admin',
								'route' => 'home',
								'pages' => array (
										array (
												'label' => 'Carga masiva',
												'route' => 'carga-masiva' 
										),
										array (
												'label' => 'Area Conocimiento',
												'route' => 'area-conocimiento',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'area-conocimiento',
																'action' => 'agregar-area-conocimiento'
														),
														array (
																'label' => 'Editar',
																'route' => 'area-conocimiento',
																'action' => 'editar-area-conocimiento'
														)
												)
										),
										array (
												'label' => 'Carrera Profesional',
												'route' => 'carrera-profesional',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'carrera-profesional',
																'action' => 'agregar-carrera-profesional' 
														),
														array (
																'label' => 'Editar',
																'route' => 'carrera-profesional',
																'action' => 'editar-carrera-profesional' 
														) 
												) 
										),
										array (
												'label' => 'Ciclo académico',
												'route' => 'ciclo-academico',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'ciclo-academico',
																'action' => 'agregar-ciclo' 
														),
														array (
																'label' => 'Editar',
																'route' => 'ciclo-academico',
																'action' => 'editar-ciclo' 
														) 
												) 
										),
										array (
												'label' => 'Plan de estdios',
												'route' => 'plan-estudio',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'plan-estudio',
																'action' => 'agregar-plan' 
														),
														array (
																'label' => 'Editar',
																'route' => 'plan-estudio',
																'action' => 'editar-plan' 
														) 
												) 
										),
										array (
												'label' => 'Aulas',
												'route' => 'aula',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'aula',
																'action' => 'agregar-aula' 
														),
														array (
																'label' => 'Editar',
																'route' => 'aula',
																'action' => 'editar-aula' 
														) 
												) 
										),
										array (
												'label' => 'Usuarios',
												'route' => 'usuario',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'usuario',
																'action' => 'agregar-usuario' 
														),
														array (
																'label' => 'Editar',
																'route' => 'usuario',
																'action' => 'editar-usuario' 
														),														
												) 
										),
										array (
												'label' => 'Administradores',
												'route' => 'administrador',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'administrador',
																'action' => 'agregar-administrador' 
														),
														array (
																'label' => 'Editar',
																'route' => 'administrador',
																'action' => 'editar-administrador' 
														),
														array (
																'label' => 'Asignar Usuario',
																'route' => 'administrador',
																'action' => 'asignar-usuario'
														)
												) 
										),
										array (
												'label' => 'Docentes',
												'route' => 'docente',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'docente',
																'action' => 'agregar-docente' 
														),
														array (
																'label' => 'Editar',
																'route' => 'docente',
																'action' => 'editar-docente' 
														) 
												) 
										),
										array (
												'label' => 'Estudiantes',
												'route' => 'estudiante',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'estudiante',
																'action' => 'agregar-estudiante' 
														),
														array (
																'label' => 'Editar',
																'route' => 'estudiante',
																'action' => 'editar-estudiante' 
														) 
												) 
										),
										array (
												'label' => 'Cursos',
												'route' => 'curso',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'curso',
																'action' => 'agregar-curso' 
														),
														array (
																'label' => 'Editar',
																'route' => 'curso',
																'action' => 'editar-curso' 
														),
														array (
																'label' => 'Asignar silabo',
																'route' => 'silabo',
																'action' => 'index' 
														) 
												) 
										),
										array (
												'label' => 'Carga Académica',
												'route' => 'carga-academica',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Agregar',
																'route' => 'carga-academica',
																'action' => 'agregar-carga-academica' 
														),
														array (
																'label' => 'Editar',
																'route' => 'carga-academica',
																'action' => 'editar-carga-academica' 
														),
														array (
																'label' => 'Matricular estudiantes',
																'route' => 'matricula',
																'action' => 'index' 
														) 
												) 
										),
										array (
												'label' => 'Perfil',
												'route' => 'perfil',
												'action' => 'index',
												'pages' => array (
														array (
																'label' => 'Editar Perfil',
																'route' => 'perfil',
																'action' => 'editar-perfil'
														),
														array (
																'label' => 'Cambiar Clave',
																'route' => 'perfil',
																'action' => 'cambiar-clave'
														),
														array (
																'label' => 'Subir Imagen',
																'route' => 'perfil',
																'action' => 'subir-imagen'
														)
												)
										)
										 
								) 
						),
						// Navegación para el frontend
						array (
								'label' => 'Asistencia',
								'route' => 'cursos',
								'pages' => array (
										array (
												'label' => 'Cursos',
												'route' => 'cursos' 
										),
										array (
												'label' => 'Iniciar sesión',
												'route' => 'iniciar-sesion',
												'pages' => array (
														array (
																'label' => 'Listar estudiantes',
																'route' => 'estudiantes' 
														),
														array (
																'label' => 'Avance de sílabo',
																'route' => 'avance-silabo' 
														),
												),
										),
								) ,
						),
						//Navegación para los Reportes
						array(
								'label' => 'Reportes',
								'route' => 'reportes',
								'pages' => array(),
						),
				) 
		) 
);

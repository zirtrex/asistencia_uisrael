<?php

namespace Admin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Module implements AutoloaderProviderInterface, FormElementProviderInterface
{

	public function onBootstrap(MvcEvent $e)
	{
		$app = $e->getApplication();
		$eventManager = $e->getApplication()->getEventManager();

		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
		
		$eventManager->attach('route', array($this, 'onRoute'), -150);
	}

	public function getServiceConfig()
	{
		return array(
			//Authentication
			'factories' => array(
				/*'AuthService' => function($sm){
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$AuthAdapter = new AuthAdapter($dbAdapter, 'usuario', 'usuario', 'clave', 'MD5(?)');
					$select = $AuthAdapter->getDbSelect();
					$select->where('estado = "1"');
					$authService = new AuthenticationService();
					$authService->setAdapter($AuthAdapter);
					$authService->setStorage(new SessionStorage('asistencia_uisrael'));						
					return $authService;
				},*/
				
				'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
			    
			    //CarreraProfesionalTable
			    'ConfigTable' => function ($sm) {
			         return new \Admin\Model\ConfigTable($sm);
			    },
				
				//CarreraProfesionalTable
				'AreaConocimientoTable' => function ($sm) {
					return new \Admin\Model\AreaConocimientoTable($sm);
				},
				
				//CarreraProfesionalTable
				'CarreraProfesionalTable' => function ($sm) {
					return new \Admin\Model\CarreraProfesionalTable($sm);
				},
				
				//UsuariolTable
				'UsuarioTable' => function ($sm) {
					return new \Admin\Model\UsuarioTable($sm);
				},
				
				//PersonaTable
				'PersonaTable' => function ($sm) {
					return new \Admin\Model\PersonaTable($sm);
				},
				
				//AdministradorTable
				'AdministradorTable' => function ($sm) {
					return new \Admin\Model\AdministradorTable($sm);
				},
				
				//EstudianteTable
				'EstudianteTable' => function ($sm) {
					return new \Admin\Model\EstudianteTable($sm);
				},
				
				//DocenteTable
				'DocenteTable' => function ($sm) {
					return new \Admin\Model\DocenteTable($sm);
				},
				
				//PlanEstudioTable
				'PlanEstudioTable' => function ($sm) {
					return new \Admin\Model\PlanEstudioTable($sm);
				},
				
				//PlanEstudioTable
				'CursoTable' => function ($sm) {
					return new \Admin\Model\CursoTable($sm);
				},
				
				//PlanEstudioTable
				'SilaboTable' => function ($sm) {
					return new \Admin\Model\SilaboTable($sm);
				},
				
				//SemanaTable
				'SemanaTable' => function ($sm) {
					return new \Admin\Model\SemanaTable($sm);
				},
				
				//SemanaTable
				'TematicaTable' => function ($sm) {
					return new \Admin\Model\TematicaTable($sm);
				},
				
				//SilaboDetalleTable
				'SilaboDetalleTable' => function ($sm) {
					return new \Admin\Model\SilaboDetalleTable($sm);
				},
				
				//CicloAcademicoTable
				'CicloAcademicoTable' => function ($sm) {
					return new \Admin\Model\CicloAcademicoTable($sm);
				},
				
				//CursoAperturadoTable
				'CursoAperturadoTable' => function ($sm) {
					return new \Admin\Model\CursoAperturadoTable($sm);
				},
				
				//ModalidadTable
				'ModalidadTable' => function ($sm) {
					return new \Admin\Model\ModalidadTable($sm);
				},
				
				//AulaTable
				'AulaTable' => function ($sm) {
					return new \Admin\Model\AulaTable($sm);
				},
				
				//SeccionTable
				'SeccionTable' => function ($sm) {
					return new \Admin\Model\SeccionTable($sm);
				},
				
				//CargaAcademicaTable
				'CargaAcademicaTable' => function($sm) {
					return new \Admin\Model\CargaAcademicaTable($sm);
				},
				
				//DiaSemanaTable
				'DiaSemanaTable' => function($sm) {
					return new \Admin\Model\DiaSemanaTable($sm);
				},
				
				//HorarioTable
				'HorarioTable' => function($sm) {
					return new \Admin\Model\HorarioTable($sm);
				},
				
				//MatriculaTable
				'MatriculaTable' => function ($sm) {
					return new \Admin\Model\MatriculaTable($sm);
				},
				
				//SesionClaseTable
				'SesionClaseTable' => function ($sm) {
					return new \Admin\Model\SesionClaseTable($sm);
				},		
				
				//AsistenciaEstudianteTable
				'AsistenciaEstudianteTable' => function ($sm) {
					return new \Admin\Model\AsistenciaEstudianteTable($sm);
				},				
				
				//AvanceSilaboTable
				'AvanceSilaboTable' => function ($sm) {
					return new \Admin\Model\AvanceSilaboTable($sm);
				},		
				
				//Mail Transport
				'MailTransport' => function($sm) {
					$config = include __DIR__ . '/config/mail.config.local.php';
					$transport = new Smtp();
					$transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
				
					return $transport;
				},
			)
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
						__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
	
	public function getFormElementConfig()
	{
		return array(
				'factories' => array(
						'PersonaFieldset' => function($sm){
							$fieldset = new \Admin\Form\Fieldset\PersonaFieldset($sm->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
							return $fieldset;
						}
				)
		);
	}
	
	public function getViewHelperConfig()
	{
		return array(
				'factories' => array(
						'usuario_helper' => function($sm) {
							$usuarioHelper = new View\Helper\UsuarioHelper($sm);
							return $usuarioHelper;
						},
						'config_helper' => function($sm) {
						     $configHelper = new View\Helper\ConfigHelper($sm);
						     return $configHelper;
						}
				)
		);
	}

	public function onRoute(\Zend\EventManager\EventInterface $e) {
	
		$application = $e->getApplication();
		$routeMatch = $e->getRouteMatch();
		$sm = $application->getServiceManager();
		$auth = $sm->get('AuthService');
		$acl = $sm->get('acl');
		// everyone is guest until logging in
		$role = \Admin\Acl\Acl::DEFAULT_ROLE; // The default role is guest $acl
	
		if ($auth->hasIdentity())
		{
			$user = $auth->getIdentity();
			$role = ($auth->getStorage()->read()['rol'] == 'administrador')?'administrador':'docente';
		}
	
		$controller = $routeMatch->getParam('controller');
		$action = $routeMatch->getParam('action');
	
		if (!$acl->hasResource($controller))
			throw new \Exception('Resource ' . $controller . ' not defined');
	
		if (!$acl->isAllowed($role, $controller, $action))
		{	
			$response = $e->getResponse();
	
			if($auth->hasIdentity())
			{				
				if($role == "docente"){
	
					$url = $e->getRouter()->assemble(array(), array('name' => 'cursos')); // Route que se debe dirigir si tiene sesión y es docente
					 
					$response->getHeaders()->addHeaders(array(
							array('Location' => $url)
					));
					$response->setStatusCode(302);
					$response->sendHeaders();
					exit;
				}else{
					$url = $e->getRouter()->assemble(array(), array('name' => 'home')); // Route que se debe dirigir si tiene sesión y es administrador
					
					$response->getHeaders()->addHeaders(array(
							array('Location' => $url)
					));
					$response->setStatusCode(302);
					$response->sendHeaders();
					exit;
				}
			}else{
	
				$url = $e->getRouter()->assemble(array(), array('name' => 'ingresar')); // Route que se tiene que dirigir si no tiene sesión
				 
				$response->getHeaders()->addHeaders(array(
						array('Location' => $url)
				));
				$response->setStatusCode(302);
				$response->sendHeaders();
				exit;
			}
		}
	}
}
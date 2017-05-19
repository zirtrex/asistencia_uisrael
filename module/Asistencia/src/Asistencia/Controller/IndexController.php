<?php

namespace Asistencia\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Miscellanea;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class IndexController extends AbstractActionController implements ServiceLocatorAwareInterface
{
    private $configTable;
    
	//Muestra los cursos que tiene asignado un docente, es procesado cuando se inicia sesión
	public function indexAction()
	{		
		if($this->identity())
		{	
		
		    $config = $this->getDBConfigTable()->obtenerConfiguracion(2);
		    
		    $codCicloAcademico = $config['value'];
		    
			$codDocente = $this->getDatosDocente()['codDocente'];
			
			$cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');
            
            $cursosDocente = $cargaAcademicaTable->obtenerCursos($this->getDatosDocente()['codDocente'], $codCicloAcademico);
            
            $sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
            
            $sesionesAbiertas = $sesionClaseTable->obtenerSesionesAbiertas($codDocente);         
            
            return new ViewModel(array(
                'cursosDocente' => $cursosDocente,
            	'sesionesAbiertas' => $sesionesAbiertas
            ));
        }
        
        return $this->redirect()->toRoute('ingresar');
	}
	
	//Inicia la sesion de clase o continua si hubiera una anterior
	public function iniciarSesionAction()
	{
		if($this->identity())
		{			
			$codDocente 			= $this->getDatosDocente()['codDocente'];
			$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codAula 				= (int) $this->params()->fromRoute('codaula', 0);
			$codSeccion 			= (int) $this->params()->fromRoute('codseccion', 0);
			
			//Verificamos que se envien los parámetros necesarios para listar las sesiones.
			if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo || !$codAula || !$codSeccion)
			{
				return $this->redirect()->toRoute('cursos');
			}
			
			$cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');			
							
			$cursoSeleccionado = $cargaAcademicaTable->obtenerCurso($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
				
			//Verificamos que los valores enviados coincidan con la carrera, el ciclo, el curso, docente, aula y turno para determinar si es único
			if($cursoSeleccionado)
			{				
				$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
				
				$sesionAbierta = $sesionClaseTable->obtenerSesionAbierta($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
				
				//Si existe una sesión abierta verificamos su estado
				if($sesionAbierta){
											
					if($sesionAbierta['asistenciaRealizada'] == 'No')
					{						
						return $this->forward()->dispatch('Asistencia\Controller\Index', array(
									'action'				=>'listar-estudiantes',
									'codsesionclase' 		=> $sesionAbierta['codSesionClase'],
									'codcargaacademica' 	=> $sesionAbierta['codCargaAcademica'],
									'codcicloacademico' 	=> $sesionAbierta['codCicloAcademico'],
									'codcurso' 				=> $sesionAbierta['codCurso'],
									'codmodalidad' 			=> $sesionAbierta['codModalidad'],
									'paralelo'				=> $sesionAbierta['paralelo'],
									'codaula' 				=> $sesionAbierta['codAula'],
									'codseccion' 			=> $sesionAbierta['codSeccion']
						));
					}
					else
					{						
						if($sesionAbierta['avanceSilabo'] == 'No')
						{								
							return $this->forward()->dispatch('Asistencia\Controller\Index', array(
										'action'				=>'avance-silabo',
										'codsesionclase' 		=> $sesionAbierta['codSesionClase'],
										'codcargaacademica' 	=> $sesionAbierta['codCargaAcademica'],
										'codcicloacademico' 	=> $sesionAbierta['codCicloAcademico'],
										'codcurso' 				=> $sesionAbierta['codCurso'],
										'codmodalidad' 			=> $sesionAbierta['codModalidad'],
										'paralelo' 				=> $sesionAbierta['paralelo'],
										'codaula' 				=> $sesionAbierta['codAula'],
										'codseccion' 			=> $sesionAbierta['codSeccion']
							));						
						}
						else
						{							
							return $this->forward()->dispatch('Asistencia\Controller\Index', array(
										'action'				=>'cerrar-sesion',
										'codsesionclase' 		=> $sesionAbierta['codSesionClase'],
										'codcargaacademica' 	=> $sesionAbierta['codCargaAcademica'],
										'codcicloacademico' 	=> $sesionAbierta['codCicloAcademico'],
										'codcurso' 				=> $sesionAbierta['codCurso'],
										'codmodalidad' 			=> $sesionAbierta['codModalidad'],
										'paralelo' 				=> $sesionAbierta['paralelo'],
										'codaula' 				=> $sesionAbierta['codAula'],
										'codseccion' 			=> $sesionAbierta['codSeccion']
							));
						}
						
					}
										
				}
				else //Creamos una nueva sesión de clase
				{					
					$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
					
					$sesion = array(
							'codSesionClase' 		=> NULL,
							'fecha' 				=> gmdate("Y-m-d",Miscellanea::getHoraLocal(-5)),
							'diaSemana' 			=> gmdate("N",Miscellanea::getHoraLocal(-5)),
							'horaInicio' 			=> gmdate("H:i:s.U" ,Miscellanea::getHoraLocal(-5)),
							'horaFin' 				=> NULL,
							'asistenciaRealizada' 	=> 'No',
							'totalEstudiantes' 		=> NULL,
							'estudiantesAsistieron' => NULL,
							'avanceSilabo' 			=> 'No',
							'totalTemas' 			=> NULL,
							'temasTerminados' 		=> NULL,
							'sesionTerminada' 		=> 'No',
							'observacion' 			=> NULL,
							'codCargaAcademica' 	=> $codCargaAcademica,
							'codCicloAcademico' 	=> $codCicloAcademico,
							'codCurso' 				=> $codCurso,
							'codModalidad' 			=> $codModalidad,
							'paralelo' 				=> $paralelo,							
							'codAula' 				=> $codAula,
							'codSeccion' 			=> $codSeccion,
							'codDocente' 			=> $codDocente,
					);
					
					$codSesionInsertada = $sesionClaseTable->nuevaSesion($sesion);
					
					if($codSesionInsertada != NULL)
					{					
						return $this->forward()->dispatch('Asistencia\Controller\Index', array(
								'action'					=>'listar-estudiantes',
								'codsesionclase' 			=> $codSesionInsertada,
								'codcargaacademica' 		=> $codCargaAcademica,
								'codcicloacademico' 		=> $codCicloAcademico,
								'codcurso' 					=> $codCurso,
								'codmodalidad' 				=> $codModalidad,
								'paralelo' 					=> $paralelo,
								'codaula' 					=> $codAula,
								'codseccion' 				=> $codSeccion
						));					
					}
					else
					{					
						\Zend\Debug\Debug::dump("La sesión no se ha creado correctamente");
						return;			
					}
					
				}			
				
			}
			else
			{				
				\Zend\Debug\Debug::dump("Los datos enviados son incorrectos");
				return;
			}
		}
		
		return $this->redirect()->toRoute('ingresar');
	}	
	
	//Muestra todos los estudiantes matriculados en el curso seleccionado
	public function listarEstudiantesAction()
	{
	
		if($this->identity())
		{
			$codDocente 			= $this->getDatosDocente()['codDocente'];
			$codSesionClase			= (int) $this->params()->fromRoute('codsesionclase', 0);
			$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codAula 				= (int) $this->params()->fromRoute('codaula', 0);
			$codSeccion 			= (int) $this->params()->fromRoute('codseccion', 0);
			
			//Verificamos que se envien los parámetros necesarios para listar los estudiantes.
			if(!$codCargaAcademica || !$codSesionClase || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo || !$codAula || !$codSeccion)
			{
				return $this->redirect()->toRoute('cursos');
			}
			
			$cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');		
				
			//
			$cursoSeleccionado = $cargaAcademicaTable->obtenerCurso($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
			
			if($cursoSeleccionado)
			{
			
				$matriculaTable = $this->getServiceLocator()->get('MatriculaTable');
			
				$estudiantesMatriculados = $matriculaTable->obtenerEstudiantesMatriculados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);	
			
				$this->layout()->setVariable('curso', $cursoSeleccionado);
				
				$this->layout()->setVariable('estudiantesMatriculados', $estudiantesMatriculados);
				
				return new ViewModel(array(
						'curso' => $cursoSeleccionado,
						'estudiantesMatriculados' => $estudiantesMatriculados,
						'dataUrl' => array($codSesionClase, $codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion)
				));
			
			}
			else
			{
				\Zend\Debug\Debug::dump("Los datos enviados son incorrectos");
				return ;
			}
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Registra la asistencia de los estudiantes
	public function registrarAsistenciaAction()
	{
		if($this->identity())
		{			
			$request = $this->getRequest(); //$this->layout('layout/login');
			
			if($request->isPost()){
					
				$dataEstudiantes = $request->getPost();	

				$codDocente 			= $this->getDatosDocente()['codDocente'];
				$codSesionClase 		= $dataEstudiantes->codSesionClase;
				$codCargaAcademica 		= $dataEstudiantes->codCargaAcademica;
				$codCicloAcademico 		= $dataEstudiantes->codCicloAcademico;
				$codCurso 				= $dataEstudiantes->codCurso;
				$codModalidad 			= $dataEstudiantes->codModalidad;
				$paralelo				= $dataEstudiantes->paralelo;
				$codAula 				= $dataEstudiantes->codAula;
				$codSeccion 			= $dataEstudiantes->codSeccion;
				
				$asistenciaEstudianteTable = $this->getServiceLocator()->get('AsistenciaEstudianteTable');
				
				$totalEstudiantes = 0;
				$estudiantesAsistieron = 0;
				
				foreach ($dataEstudiantes as $campo => $valor){
									
					if(is_int($campo)){
						
						$totalEstudiantes++;
						
						if($valor != 'Falta') $estudiantesAsistieron++;
						
						$asistencia = array(
								'estadoAsistenciaEstudiante'	=> $valor,
								'codEstudiante' 				=> $campo,
								'codSesionClase' 				=> $codSesionClase	
						);						
						
						$asistenciaEstudianteTable->registrarAsistencia($asistencia);
					}					
					
				}
				
				$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
				
				$sesion = array(
						'codSesionClase' => $codSesionClase,
						'totalEstudiantes' => $totalEstudiantes,
						'estudiantesAsistieron' => $estudiantesAsistieron,
						'asistenciaRealizada' => 'Si'
				);
				
				//Actualizamos la tabla sesion
				$sesionClaseTable->nuevaSesion($sesion);
				
				return $this->redirect()->toRoute('avance-silabo', array(
							'action'					=>'avance-silabo',
							'codsesionclase' 			=> $codSesionClase,
							'codcargaacademica' 		=> $codCargaAcademica,
							'codcicloacademico' 		=> $codCicloAcademico,
							'codcurso' 					=> $codCurso,
							'codmodalidad' 				=> $codModalidad,
							'paralelo' 					=> $paralelo,
							'codaula' 					=> $codAula,
							'codseccion' 				=> $codSeccion
				));				
				
			}
			
		}
		
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Muestra la ventana para agregar el avance de silabo
	public function avanceSilaboAction()
	{	
		if($this->identity())
		{			
			$codDocente 			= $this->getDatosDocente()['codDocente'];
			$codSesionClase			= (int) $this->params()->fromRoute('codsesionclase', 0);
			$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codAula 				= (int) $this->params()->fromRoute('codaula', 0);
			$codSeccion 			= (int) $this->params()->fromRoute('codseccion', 0);
			
			//Verificamos que se envien los parámetros necesarios.
			if(!$codCargaAcademica || !$codSesionClase || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo || !$codAula || !$codSeccion)
			{
				return $this->redirect()->toRoute('cursos');
			}
			
			$cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');		
				
			//Verificamos que los valores enviados coincidan.
			$cursoSeleccionado = $cargaAcademicaTable->obtenerCurso($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
			
			if($cursoSeleccionado){
						
				$silaboDetalleTable = $this->getServiceLocator()->get('SilaboDetalleTable');
				
				$avanceSilaboTable = $this->getServiceLocator()->get('AvanceSilaboTable');
				
				$temas = $silaboDetalleTable->obtenerTemasPorCiclo($codCurso, $codCicloAcademico);
		
				$temasAvanzados = $avanceSilaboTable->obtenerTemasAvanzados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
				
				$this->layout()->setVariable('curso', $cursoSeleccionado);
				
				$this->layout()->setVariable('temas', $temas);
				
				return new ViewModel(array(
						'curso' => $cursoSeleccionado,
						'temas' => $temas,
						'temasAvanzados' => $temasAvanzados,
						'dataUrl' => array($codSesionClase, $codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion)
				));				
				
			}
			else
			{
				\Zend\Debug\Debug::dump("Los datos enviados son incorrectos");
				return ;
			}			
			
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Registra el avance de sílabo
	public function registrarAvanceAction()
	{	
		if($this->identity())
		{			
			$request = $this->getRequest();
				
			if($request->isPost()){
					
				$dataAvance = $request->getPost();
			
				$codDocente 			= $this->getDatosDocente()['codDocente'];
				$codSesionClase			= $dataAvance->codSesionClase;
				$codCargaAcademica 		= $dataAvance->codCargaAcademica;
				$codCicloAcademico 		= $dataAvance->codCicloAcademico;
				$codCurso 				= $dataAvance->codCurso;
				$codModalidad 			= $dataAvance->codModalidad;
				$paralelo 				= $dataAvance->paralelo;
				$codAula 				= $dataAvance->codAula;
				$codSeccion 			= $dataAvance->codSeccion;
				$totalTemas 			= $dataAvance->totalTemas;
			
				$avanceSilaboTableTable = $this->getServiceLocator()->get('AvanceSilaboTable');	
			
				$avanceTema = array(
						'avance'              => $dataAvance->avance,
						'observaciones'       => $dataAvance->observaciones,
						'codSilaboDetalle'    => $dataAvance->codSilaboDetalle,
						'codSesionClase'      => $codSesionClase
				);	
			
				$result = $avanceSilaboTableTable->registrarAvanceTema($avanceTema);				
			
				if($result != null){
					
					$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
						
					$sesion = array(
							'codSesionClase' 	=> $codSesionClase,
							'avanceSilabo' 		=> 'Si',
							'totalTemas' 		=> $totalTemas,
					);
						
					//Actualizamos la tabla sesion
					$sesionClaseTable->nuevaSesion($sesion);
						
					return $this->redirect()->toRoute('cerrar-sesion', array(
								'action'				=> 'cerrar-sesion',
								'codsesionclase' 		=> $codSesionClase,
								'codcargaacademica' 	=> $codCargaAcademica,
								'codcicloacademico' 	=> $codCicloAcademico,
								'codcurso' 				=> $codCurso,
								'codmodalidad' 			=> $codModalidad,
								'paralelo' 				=> $paralelo,
								'codaula' 				=> $codAula,
								'codseccion' 			=> $codSeccion
					));
				}
			
			}
					
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Permite cerrar la sesión actual y marcar la salida de dicha sesión
	public function cerrarSesionAction()
	{		
		if($this->identity())
		{			
			$codDocente 			= $this->getDatosDocente()['codDocente'];
			$codSesionClase			= (int) $this->params()->fromRoute('codsesionclase', 0);
			$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codAula 				= (int) $this->params()->fromRoute('codaula', 0);
			$codSeccion 			= (int) $this->params()->fromRoute('codseccion', 0);
			
			$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
			
			$fechaRegistrada = $sesionClaseTable->obtenerSesion($codSesionClase)['fecha'];			
			
			$observacion = "La sesión ha finalizado correctamente.";
			
			if($fechaRegistrada !== gmdate("Y-m-d",Miscellanea::getHoraLocal(-5)))
			{
				$observacion = "La hora de salida corresponde a la fecha: " . gmdate("Y-m-d",Miscellanea::getHoraLocal(-5));
			}
			
			$avanceSilaboTable = $this->getServiceLocator()->get('AvanceSilaboTable');
			
			$temasTerminados = $avanceSilaboTable->obtenerTemasAvanzados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente, true);
			
			$sesion = array(
					'codSesionClase' 	=> $codSesionClase,
					'horaFin' 			=> gmdate("H:i:s.U" ,Miscellanea::getHoraLocal(-5)),
					'observacion' 		=> $observacion,
					'temasTerminados' 	=> $temasTerminados->count(),
					'sesionTerminada' 	=> 'Si'
			);
			
			//Actualizamos la tabla sesion
			$sesionClaseTable->nuevaSesion($sesion);
			
			return $this->redirect()->toRoute('cursos');
			
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//obtengo los datos del docente actual
	private function getDatosDocente()
	{		
		if($this->identity())
		{
			$docenteTable = $this->getServiceLocator()->get('DocenteTable');		
			return $dataDocente = $docenteTable->obtenerDocentePorCodUsuario($this->identity()['codUsuario']);
		}
		
		return;
	}
	
	private  function getDBConfigTable()
	{
	    if (!$this->configTable)
	    {
	        $this->configTable = $this->getServiceLocator()->get('ConfigTable');
	    }
	    return $this->configTable;
	}
}







<?php

namespace Reporte\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DOMPDFModule\View\Model\PdfModel;
use Reporte\Form\ReportForm;


class IndexController extends AbstractActionController
{
	private $areaConocimientoTable;
	private $carreraProfesionalTable;
	private $cicloAcademicoTable;
	private $docenteTable;
	private $cursoTable;
	private $modalidadTable;
	private $aulaTable;
	private $seccionTable;
	private $asistenciaEstudianteTable;
	private $cargaAcademicaTable;
	private $sesionClaseTable;

	//Avance de sílabo por docente
	public function primerReporteAction()
	{		
		if($this->identity())
		{
            $form = new ReportForm();
            $form->get("codAreaConocimiento")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerAreasConocimientoArray());
			$form->get("codCarreraProfesional")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCarrerasProfesionalesArray());
			$form->get("codCicloAcademico")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCiclosAcademicosArray());
            $form->get("codDocente")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerDocentesArray());

            $cursos = array();
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {            
            	$data = $request->getPost();
            	
            	$esComun 				= ($data['esComun'] != "") ? $data['esComun'] : null;
            	$codAreaConocimiento 	= ($data['codAreaConocimiento'] != "" && $data['esComun'] == "No") ? $data['codAreaConocimiento'] : null;
            	$codCarreraProfesional 	= ($data['codCarreraProfesional'] != "" && $data['esComun'] == "No") ? $data['codCarreraProfesional'] : null;
            	$codCicloAcademico 		= ($data['codCicloAcademico'] != "") ? $data['codCicloAcademico'] : null;
            	$codDocente 			= $data['codDocente'];
            	
            	$form->setData($data);     	    	            	
            	
            	$resulsetCursos = $this->getDBCargaAcademicaTable()->obtenerCargaAcademicaParaReportes($esComun, null, $codAreaConocimiento, $codCarreraProfesional, $codCicloAcademico, null, null, null, null, $codDocente, null);

            	if($resulsetCursos->count() != 0)
            	{            		
            		foreach ($resulsetCursos as $curso)
            		{            			
            			$totalTemas = $this->getDBSesionClaseTable()->obtenerTotalTemas($curso['codCurso']);            			
            			$curso['totalTemas'] = $totalTemas['totalTemas'];
            			
            			$temasTerminados = $this->getDBSesionClaseTable()->obtenerTotalTemasTerminados($curso['codCicloAcademico'], $curso['codCurso'], $curso['codModalidad'], $curso['paralelo'], $curso['codAula'], $curso['codSeccion'], $curso['codDocente']);            			
            			$curso['totalTemasTerminados'] = $temasTerminados['temasTerminados'];
            			
            			array_push($cursos, $curso);       			
            		}          		
            	}                       	 
            }
            
            $this->layout()->setVariable('titulo', 'Avance de Sílabo por Docente');
            
            return new ViewModel(array(
                	'form' 					=> $form,
            		'cursos' 				=> $cursos,
            ));
        }
        
        return $this->redirect()->toRoute('ingresar');
	}
	
	//Ranking 10 de avance de sílabo
	public function segundoReporteAction()
	{
		if($this->identity())
		{	
			$cursosMayor = array();			
			$cursosMenor = array();
		
			$sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
			 
			$resulsetCursosMayor = $sesionClaseTable->obtenerCursosAvanzadosParaReporte();

			if($resulsetCursosMayor->count() != 0)
			{
				foreach ($resulsetCursosMayor as $curso)
				{					 
					//$totalTemas = $sesionClaseTable->obtenerTotalTemas($curso['codCurso']);					 
					//$curso['totalTemas'] = $totalTemas['totalTemas'];
					 
					//$temasTerminados = $sesionClaseTable->obtenerTotalTemasTerminados($curso['codCicloAcademico'], $curso['codCurso'], $curso['codModalidad'], $curso['paralelo'], $curso['codAula'], $curso['codSeccion'], $curso['codDocente']);					 
					//$curso['totalTemasAvanzados'] = $temasTerminados['temasTerminados'];
					 
					array_push($cursosMayor, $curso);
				}
			}
			
			$resulsetCursosMenor = $sesionClaseTable->obtenerCursosAvanzadosParaReporte('ASC');
			
			if($resulsetCursosMenor->count() != 0){
			
				foreach ($resulsetCursosMenor as $curso)
				{			
					array_push($cursosMenor, $curso);			
				}			
			}
	
			return new ViewModel(array(
					'cursosMayor' => $cursosMayor,
					'cursosMenor' => $cursosMenor,
			));
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Avance de sílabo por carrera profesional
	public function tercerReporteAction()
	{
		if($this->identity())
		{			 
			$form = new ReportForm();
			$form->get("codCarreraProfesional")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCarrerasProfesionalesArray());
	
			$cursos = array();
			
			$request = $this->getRequest();
	
			if($request->isPost())
			{	
				$data = $request->getPost();
				
				$codCarreraProfesional = ($data['codCarreraProfesional'] != "") ? $data['codCarreraProfesional'] : null;
				 
				$form->setData($data);
					
            	$sesionClaseTable = $this->getDBSesionClaseTable();            	    	            	
            	
            	$resulsetCursos = $this->getDBCargaAcademicaTable()->obtenerCargaAcademicaParaReportes(null, null, null, $codCarreraProfesional, null, null, null, null, null, null);
	
				if($resulsetCursos->count() != 0)
				{	
					foreach ($resulsetCursos as $curso)
					{						 
						$totalTemas = $sesionClaseTable->obtenerTotalTemas($curso['codCurso']);						 
						$curso['totalTemas'] = $totalTemas['totalTemas'];
						 
						$temasTerminados = $sesionClaseTable->obtenerTotalTemasTerminados($curso['codCicloAcademico'], $curso['codCurso'], $curso['codModalidad'], $curso['paralelo'], $curso['codAula'], $curso['codSeccion'], $curso['codDocente']);						 
						$curso['totalTemasTerminados'] = $temasTerminados['temasTerminados'];
						 
						array_push($cursos, $curso);			 
					}	
				}
			}
			
			$this->layout()->setVariable('titulo', 'Avance de Sílabo por Carrera Profesional');
	
			return new ViewModel(array(
					'form' => $form,
					'cursos' => $cursos,
			));
		}	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Asistencia de docentes  por carrera profesional
	public function cuartoReporteAction()
	{
		if($this->identity())
		{	
			$form = new ReportForm();
			$form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
	
			$docentes = array();
			
			$request = $this->getRequest();
	
			if($request->isPost())
			{	
				$data = $request->getPost();
				
				$codCarreraProfesional = ($data['codCarreraProfesional'] != "") ? $data['codCarreraProfesional'] : null;
					
				$form->setData($data);
					
				$resulsetDocentes = $this->getDBSesionClaseTable()->obtenerNumeroAsistenciasDocenteParaReporte($codCarreraProfesional);
	
				if($resulsetDocentes->count() != 0)
				{	
					foreach ($resulsetDocentes as $docente)
					{							
						$resulsetRegistroAsistencia = $this->getDBSesionClaseTable()->obtenerRegistroAsistencia($docente['codCicloAcademico'], $docente['codCurso'], $docente['codModalidad'], $docente['paralelo'], $docente['codAula'], $docente['codSeccion'], $docente['codDocente']);
						
						$docente['registro'] = $resulsetRegistroAsistencia;
						
						array_push($docentes, $docente);							
					}	
				}	
			}
			
			$this->layout()->setVariable('titulo', 'Listado de asistencia docente por carrera profesional');
	
			return new ViewModel(array(
					'form' => $form,
					'docentes' => $docentes,
			));
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Reporte de asistencia de estudiantes para administradores
	public function quintoReporteAction()
	{
		if($this->identity())
		{			
			$form = new ReportForm();
			$form->get("codAreaConocimiento")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerAreasConocimientoArray());
			$form->get("codCarreraProfesional")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCarrerasProfesionalesArray());
			$form->get("codCicloAcademico")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCiclosAcademicosArray());
			$form->get("codCurso")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCursosArray());
			$form->get("codModalidad")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerModalidadesArray());
			$form->get("paralelo")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerParalelosArray());
			$form->get("codDocente")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerDocentesArray());
			//$form->get("codAula")->setValueOptions($this->getDBAulaTable()->obtenerAulasArray());
			//$form->get("codSeccion")->setValueOptions($this->getDBSeccionTable()->obtenerSeccionesArray());
	
			$estudiantes = array();
			
			$esComun 				= "";
			$codAreaConocimiento 	= "";
			$codCarreraProfesional 	= "";
			$codCicloAcademico 		= "";
			$codCurso 				= "";
			$codModalidad 			= "";
			$paralelo 				= "";
			$codDocente 			= "";
			$fechaInicio			= "";
			$fechaFin				= "";
			
			$request = $this->getRequest();
			
			$form->setAttribute('class', '');
	
			if($request->isPost())
			{
				$form->setInputFilter(new \Reporte\Form\Filter\ReportFormFilter());
				$form->setData($request->getPost());
					
				if($form->isValid())
				{
				
					$data = $form->getData();
				
					$esComun 				= ($data['esComun'] != "") ? $data['esComun'] : null;
					$codAreaConocimiento 	= ($data['codAreaConocimiento'] != "" && $data['esComun'] == "No") ? $data['codAreaConocimiento'] : null;
					$codCarreraProfesional 	= ($data['codCarreraProfesional'] != "" && $data['esComun'] == "No") ? $data['codCarreraProfesional'] : null;
					$codCicloAcademico 		= $data['codCicloAcademico'];
					$codCurso 				= $data['codCurso'];
					$codModalidad 			= $data['codModalidad'];
					$paralelo 				= $data['paralelo'];
					$codDocente 			= $data['codDocente'];
					$fechaInicio			= $data['fechaInicioClases'];
					$fechaFin				= $data['fechaFinClases'];
					
					//\Zend\Debug\Debug::dump($data); return;
				 
				}
				else 
				{
					 $form->setAttribute('class', 'has-error');
				}
				 
				$resulsetEstudiantes = $this->getDBAsistenciaEstudianteTable()->obtenerAsistenciaEstudiantes($esComun, $codAreaConocimiento, $codCarreraProfesional, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codDocente, $fechaInicio, $fechaFin, 2);
				
				if($resulsetEstudiantes->count() != 0)
				{	
					foreach ($resulsetEstudiantes as $estudiante)
					{						 
						$totalPuntual = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin,  "Puntual");
						 
						$estudiante['totalPuntual'] = $totalPuntual['totalEstado'];
						 
						$totalTardanza = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin,  "Tarde");
						 
						$estudiante['totalTarde'] = $totalTardanza['totalEstado'];
						
						$totalFalta = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin,  "Falta");
						 
						$estudiante['totalFalta'] = $totalFalta['totalEstado'];
						
						array_push($estudiantes, $estudiante);						 
					}
				}
			}
			
			$this->layout()->setVariable('titulo', 'Asistencia de estudiantes');
	
			return new ViewModel(array(
					'form' 			=> $form,
					'estudiantes' 	=> $estudiantes,
					'dataUrl' 		=> array($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codDocente, $fechaInicio, $fechaFin)
			));
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Reporte de asistencia de estudiantes para administradores
	public function quintoReportePdfAction()
	{
		if($this->identity())
		{
			$estudiantes = array();
			
			$imprimirpdf 			= $this->params()->fromRoute('imprimirpdf');
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codDocente	 			= $this->params()->fromRoute('coddocente', null);
			$fechaInicio			= $this->params()->fromRoute('fechainicio');
			$fechaFin				= $this->params()->fromRoute('fechafin');
			
			$resulsetEstudiantes = $this->getDBAsistenciaEstudianteTable()->obtenerAsistenciaEstudiantes(null, null, null, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codDocente, $fechaInicio, $fechaFin,  $limit = 2);			
			$sesionClase = $this->getDBSesionClaseTable()->obtenerSesionClase($codCicloAcademico, $codCurso, $codModalidad, $paralelo, null, null, $codDocente);
			
			if($resulsetEstudiantes->count() != 0)
			{
				foreach ($resulsetEstudiantes as $estudiante){
			
					$totalPuntual = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Puntual");
			
					$estudiante['totalPuntual'] = $totalPuntual['totalEstado'];
			
					$totalTardanza = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Tarde");
			
					$estudiante['totalTarde'] = $totalTardanza['totalEstado'];
			
					$totalFalta = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Falta");
			
					$estudiante['totalFalta'] = $totalFalta['totalEstado'];
						
					//Datos para imprimir PDF
					$cicloAcademico = $estudiante['anio'] . ' - ' . $estudiante['semestre'];
					$nombreCurso = $estudiante['curso'];
					$modalidad = $estudiante['modalidad'];
			
					array_push($estudiantes, $estudiante);
				}
					
			}
				
			if($imprimirpdf == 'si')
			{
				$pdf = new PdfModel();
				$pdf->setTerminal(true);
				$pdf->setTemplate('reporte/index/quinto-reporte-pdf.phtml');
				//$pdf->setOption('filename', 'Asistencia de estudiantes'); // Esta opcion fuerza la descarga del PDF.
				// La extension ".pdf" se agrega automaticamente
				$pdf->setOption('paperSize', 'a4'); // Tamaño del papel
				$pdf->setOption('paperOrientation', 'landscape'); // Defaults to "portrait"
					
				// Pasamos variables a la vista
				$pdf->setVariables(array(						
						'docente'			=> $sesionClase['primerApellido'] .' '. $sesionClase['segundoApellido'] .', '. $sesionClase['nombres'],
						'cicloAcademico' 	=> $sesionClase['anio'] .'-'.$sesionClase['semestre'],
						'nombreCurso' 		=> $sesionClase['curso'],
						'modalidad'			=> $sesionClase['modalidad'],
						'paralelo'			=> $sesionClase['paralelo'],
						'aula'				=> $sesionClase['numero'],
						'seccion'			=> $sesionClase['seccion'],
						'fechaInicio'		=> $fechaInicio,
						'fechaFin'			=> $fechaFin,
						'estudiantes' 		=> $estudiantes,
				));
			
				return $pdf;
			}
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Asistencia de estudiantes  por curso/docente
	public function sextoReporteAction()
	{
		if($this->identity())
		{
			$docente = $this->obtenerDatosUsuario();
			
			$form = new ReportForm();
			$form->get("codCicloAcademico")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCiclosAcademicosArray($docente['codDocente']));
			$form->get("codCurso")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerCursosArray($docente['codDocente']));
			$form->get("codModalidad")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerModalidadesArray($docente['codDocente']));
			$form->get("paralelo")->setValueOptions($this->getDBCargaAcademicaTable()->obtenerParalelosArray($docente['codDocente']));
			
			$estudiantes = array();
			
			$imprimirpdf 		= 'no';
			$codCicloAcademico 	= 0;			
			$codCurso 			= 0;
			$codModalidad 		= 0;
			$paralelo 			= null;
			$fechaInicio		= "";
			$fechaFin			= "";
			
			$request = $this->getRequest();
			
			$form->setAttribute('class', '');
			
			if($request->isPost())
			{									
				$data = $request->getPost();

				$codCicloAcademico 		= $data['codCicloAcademico'];
				$codCurso 				= $data['codCurso'];
				$codModalidad 			= $data['codModalidad'];
				$paralelo 				= $data['paralelo'];
				$fechaInicio			= $data['fechaInicioClases'];
				$fechaFin				= $data['fechaFinClases'];
				
				$form->setData($data);
			}
			else
			{
				$imprimirpdf 			= $this->params()->fromRoute('imprimirpdf');
	    		$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
	    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
	    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
	    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
	    		$fechaInicio			= $this->params()->fromRoute('fechainicio');
	    		$fechaFin				= $this->params()->fromRoute('fechafin');
			}
				
			$resulsetEstudiantes = $this->getDBAsistenciaEstudianteTable()->obtenerAsistenciaEstudiantes(null, null, null, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $docente['codDocente'], $fechaInicio, $fechaFin,  $limit = 2);
				
			//\Zend\Debug\Debug::dump($docente['codDocente']); return;
				
			if($resulsetEstudiantes->count() != 0)
			{
				foreach ($resulsetEstudiantes as $estudiante){
						
					$totalPuntual = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Puntual");
						
					$estudiante['totalPuntual'] = $totalPuntual['totalEstado'];
						
					$totalTardanza = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Tarde");
						
					$estudiante['totalTarde'] = $totalTardanza['totalEstado'];
						
					$totalFalta = $this->getDBAsistenciaEstudianteTable()->obtenerEstudiantesEstado($estudiante['codEstudiante'], $estudiante['codCicloAcademico'], $estudiante['codCurso'], $estudiante['paralelo'], $estudiante['codModalidad'], $fechaInicio, $fechaFin, "Falta");
						
					$estudiante['totalFalta'] = $totalFalta['totalEstado'];		
					
					//Datos para imprimir PDF
					$cicloAcademico = $estudiante['anio'] . ' - ' . $estudiante['semestre'];					
					$nombreCurso = $estudiante['curso'];
					$modalidad = $estudiante['modalidad'];
						
					array_push($estudiantes, $estudiante);						
				}
					
			}
			
			if($imprimirpdf == 'si')
			{
				$pdf = new PdfModel();
				$pdf->setTerminal(true);
				$pdf->setTemplate('reporte/index/sexto-reporte-pdf.phtml');
				//$pdf->setOption('filename', 'Asistencia de estudiantes'); // Esta opcion fuerza la descarga del PDF.
				// La extension ".pdf" se agrega automaticamente
				$pdf->setOption('paperSize', 'a4'); // Tamaño del papel
				$pdf->setOption('paperOrientation', 'landscape'); // Defaults to "portrait"
				 
				// Pasamos variables a la vista
				$pdf->setVariables(array(
						'estudiantes' 		=> $estudiantes,						
						'cicloAcademico'	=> $cicloAcademico,
						'nombreCurso' 		=> $nombreCurso,
						'modalidad'			=> $modalidad,
						'docente'			=> $docente['primerApellido'] .' '. $docente['segundoApellido'] .', '. $docente['nombres']
				));
				
				return $pdf;
			}

			// $this->layout('layout/login');			
			return new ViewModel(array(					
					'form' 			=> $form,
					'estudiantes' 	=> $estudiantes,
					'dataUrl' 		=> array($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $fechaInicio, $fechaFin)
			));
			
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Asistencia de docentes por Docente
	public function septimoReporteAction()
	{
		if($this->identity())
		{
			$form = new ReportForm();
			$form->get("codDocente")->setValueOptions($this->getDBDocenteTable()->obtenerDocentesArray());
	
			$docentes = array();
				
			$request = $this->getRequest();
	
			if($request->isPost())
			{
				$data = $request->getPost();
	
				$codDocente = ($data['codDocente'] != "") ? $data['codDocente'] : null;
					
				$form->setData($data);
					
				$resulsetDocentes = $this->getDBSesionClaseTable()->obtenerNumeroAsistenciasDocenteParaReporte(null, $codDocente);
	
				if($resulsetDocentes->count() != 0)
				{
					foreach ($resulsetDocentes as $docente)
					{
						$resulsetRegistroAsistencia = $this->getDBSesionClaseTable()->obtenerRegistroAsistencia($docente['codCicloAcademico'], $docente['codCurso'], $docente['codModalidad'], $docente['paralelo'], $docente['codAula'], $docente['codSeccion'], $docente['codDocente']);
	
						$docente['registro'] = $resulsetRegistroAsistencia;
	
						array_push($docentes, $docente);
					}
				}
			}
				
			$this->layout()->setVariable('titulo', 'Listado de asistencia docente por docente');
	
			return new ViewModel(array(
					'form' => $form,
					'docentes' => $docentes,
			));
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	//Asistencia de estudiantes  por curso/docente
	public function septimoReportePdfAction()
	{
		if($this->identity())
		{
			$docente = $this->obtenerDatosUsuario();
				
			$imprimirpdf 			= $this->params()->fromRoute('imprimirpdf');
			$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
			$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
			$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
			$paralelo	 			= $this->params()->fromRoute('paralelo', null);
			$codAula				= (int) $this->params()->fromRoute('codaula', 0);
			$codSeccion				= (int) $this->params()->fromRoute('codseccion', 0);
			$codDocente				= (int) $this->params()->fromRoute('coddocente', 0);
				
			$resulsetAsistenciaDocente = $this->getDBSesionClaseTable()->obtenerRegistroAsistencia($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
			$sesionClase = $this->getDBSesionClaseTable()->obtenerSesionClase($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente);
				
			if($imprimirpdf == 'si')
			{
				$pdf = new PdfModel();
				$pdf->setTerminal(true);
				$pdf->setTemplate('reporte/index/septimo-reporte-pdf.phtml');
				//$pdf->setOption('filename', 'Asistencia de estudiantes'); // Esta opcion fuerza la descarga del PDF.
				// La extension ".pdf" se agrega automaticamente
				$pdf->setOption('paperSize', 'a4'); // Tamaño del papel
				$pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"
					
				// Pasamos variables a la vista
				$pdf->setVariables(array(
						'docente'			=> $sesionClase['primerApellido'] .' '. $sesionClase['segundoApellido'] .', '. $sesionClase['nombres'],
						'cicloAcademico' 	=> $sesionClase['anio'] .'-'.$sesionClase['semestre'],
						'nombreCurso' 		=> $sesionClase['curso'],
						'modalidad'			=> $sesionClase['modalidad'],
						'paralelo'			=> $sesionClase['paralelo'],
						'aula'				=> $sesionClase['numero'],
						'seccion'			=> $sesionClase['seccion'],
						'asistencia'		=> $resulsetAsistenciaDocente
				));
	
				return $pdf;
			}
				
		}
	
		return $this->redirect()->toRoute('ingresar');
	}
	
	private function obtenerDatosUsuario()
	{
		$rol = $this->identity()['rol'];
		$codUsuario = $this->identity()['codUsuario'];
	
		if ($rol == "administrador")
		{
			$administradorTable = $this->getServiceLocator()->get('AdministradorTable');
			return $administradorTable->obtenerAdministradorPorCodUsuario($codUsuario);
		}
	
		else if($rol == "docente")
		{
			$docenteTable = $this->getServiceLocator()->get('DocenteTable');
			return $docenteTable->obtenerDocentePorCodUsuario($codUsuario);
		}
	
		return false;
	}
	
	private  function getDBAreaConocimientoTable()
	{
		if (!$this->areaConocimientoTable)
		{
			$this->areaConocimientoTable = $this->getServiceLocator()->get('AreaConocimientoTable');
		}
		return $this->areaConocimientoTable;
	}
	
	private  function getDBCarreraProfesionalTable()
	{
		if (!$this->carreraProfesionalTable)
		{
			$this->carreraProfesionalTable = $this->getServiceLocator()->get('CarreraProfesionalTable');
		}
		return $this->carreraProfesionalTable;
	}
	
	private  function getDBCicloAcademicoTable()
	{
		if (!$this->cicloAcademicoTable)
		{
			$this->cicloAcademicoTable = $this->getServiceLocator()->get('CicloAcademicoTable');
		}
		return $this->cicloAcademicoTable;
	}

	private  function getDBDocenteTable()
	{
		if (!$this->docenteTable)
		{
			$this->docenteTable = $this->getServiceLocator()->get('DocenteTable');
		}
		return $this->docenteTable;
	}
	
	private  function getDBCursoTable()
	{
		if (!$this->cursoTable)
		{
			$this->cursoTable = $this->getServiceLocator()->get('CursoTable');
		}
		return $this->cursoTable;
	}
	
	private  function getDBModalidadTable()
	{
		if (!$this->modalidadTable)
		{
			$this->modalidadTable = $this->getServiceLocator()->get('ModalidadTable');
		}
		return $this->modalidadTable;
	}
	
	private  function getDBAulaTable()
	{
		if (!$this->aulaTable)
		{
			$this->aulaTable = $this->getServiceLocator()->get('AulaTable');
		}
		return $this->aulaTable;
	}
	
	private  function getDBSeccionTable()
	{
		if (!$this->seccionTable)
		{
			$this->seccionTable = $this->getServiceLocator()->get('SeccionTable');
		}
		return $this->seccionTable;
	}
	
	private  function getDBAsistenciaEstudianteTable()
	{
		if (!$this->asistenciaEstudianteTable)
		{
			$this->asistenciaEstudianteTable = $this->getServiceLocator()->get('AsistenciaEstudianteTable');
		}
		return $this->asistenciaEstudianteTable;
	}
	
	private  function getDBCargaAcademicaTable()
	{
		if (!$this->cargaAcademicaTable)
		{
			$this->cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');
		}
		return $this->cargaAcademicaTable;
	}
	
	private  function getDBSesionClaseTable()
	{
		if (!$this->sesionClaseTable)
		{
			$this->sesionClaseTable = $this->getServiceLocator()->get('SesionClaseTable');
		}
		return $this->sesionClaseTable;
	}
	
}










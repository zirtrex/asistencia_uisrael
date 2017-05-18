<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Miscellanea;


class MatriculaController extends AbstractActionController
{
	private $matriculaTable;
	private $cargaAcademicaTable;
	private $cursoAperturadoTable;
	private $carreraProfesionalTable;
	private $cicloAcademicoTable;
	private $modalidadTable;
	private $aulaTable;
	private $seccionTable;
	private $cursoTable;
	private $docenteTable;
	private $estudianteTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{
    		$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
    		$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
    			
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo)    		
    			return $this->redirect()->toRoute('carga-academica');    		
    		
    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademica($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
    		
    		if(!$cargaAcademica)
    		{		
    			return $this->redirect()->toRoute('carga-academica');
    		}
    		
    		$estudiantesMatriculados = $this->getDBMatriculaTable()->obtenerEstudiantesMatriculados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
    	
    		$estudiantesEncontrados = null;
    		
    		$request = $this->getRequest(); 		
    		
    		if($request->isPost())
    		{
    			$data = $request->getPost();
    			$texto = $data['texto'];
    			$estudiantesEncontrados = $this->getDBEstudianteTable()->buscar($texto);
    		}
    		
    		return new ViewModel(array(
    				'cargaAcademica' => $cargaAcademica,
    				'estudiantesMatriculados' => $estudiantesMatriculados,
    				'estudiantesEncontrados' => $estudiantesEncontrados,
    		));
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }    

    public function matricularEstudianteAction()
    {
    	if($this->identity())
    	{ 	
    		$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
    		$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
    		$codEstudiante 			= (int) $this->params()->fromRoute('codestudiante', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo || !$codEstudiante)
    		{
    			//return $this->redirect()->toRoute('carga-academica');
    		}    		
    		
    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademica($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
    		
    		// \Zend\Debug\Debug::dump($cargaAcademica); return ;
    		
    		if(!$cargaAcademica)
    		{
    			return $this->redirect()->toRoute('carga-academica');
    		}
    		
    		$estudianteMatriculado = $this->getDBMatriculaTable()->obtenerEstudianteMatriculado($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codEstudiante);
			
			if(!$estudianteMatriculado)
			{    		
				$nuevaMatricula = array(
    				'fechaMatricula' 		=> gmdate("Y-m-d", Miscellanea::getHoraLocal(-5)),
					'codEstudiante' 		=> $codEstudiante,
					'codCargaAcademica' 	=> $codCargaAcademica,
    				'codCicloAcademico' 	=> $codCicloAcademico,
    				'codCurso' 				=> $codCurso,
					'codModalidad' 			=> $codModalidad,
					'paralelo' 				=> $paralelo,					
    			);
	    	
	    		if($this->getDBMatriculaTable()->insertar($nuevaMatricula))
	    		{
	    			$this->flashMessenger()->addErrorMessage('¡Estudiante agregado correctamente!');
	    		}
	    	}    	
	    	
	    	//header('Location: http://www.example.com/');
	    	//exit;
	    	
	    	$this->redirect()->toRoute('matricula', array(
	    			'action' 				=> 'index',
	    			'codcargaacademica' 	=> $codCargaAcademica,
	    			'codcicloacademico' 	=> $codCicloAcademico,
	    			'codcurso' 				=> $codCurso,
	    			'codmodalidad' 			=> $codModalidad,
	    			'paralelo' 				=> $paralelo,
	    	));
	    	
	    	$this->getResponse()->sendHeaders();
	    	exit();
	    	   	
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }    
    
    public function eliminarEstudianteAction()
    {
    	if($this->identity())
    	{
    		$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
    		$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
    		$codEstudiante 			= (int) $this->params()->fromRoute('codestudiante', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo || !$codEstudiante)
    			return $this->redirect()->toRoute('carga-academica');
    	
    	
    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademica($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
    	
    		if(!$cargaAcademica)
    			return $this->redirect()->toRoute('carga-academica');
    	
    		$estudianteMatriculado = $this->getDBMatriculaTable()->obtenerEstudianteMatriculado($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codEstudiante);
    			
    		if($estudianteMatriculado)
    		{
    			$eliminarMatricula = array(
    					'codCargaAcademica' 	=> $codCargaAcademica,
    					'codCicloAcademico' 	=> $codCicloAcademico,
    					'codCurso' 				=> $codCurso,
    					'codModalidad' 			=> $codModalidad,
    					'paralelo' 				=> $paralelo,
    					'codEstudiante' 		=> $codEstudiante,
    			);
    	
    			$this->getDBMatriculaTable()->eliminar($eliminarMatricula);
    		}
    	
    		return $this->redirect()->toRoute('matricula', array(
    				'action' 				=> 'index',
    				'codcargaacademica' 	=> $codCargaAcademica,
    				'codcicloacademico' 	=> $codCicloAcademico,
    				'codcurso' 				=> $codCurso,
    				'codmodalidad' 			=> $codModalidad,
    				'paralelo' 				=> $paralelo,
    		));
    	
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBMatriculaTable()
    {
    	if (!$this->matriculaTable)
    	{
    		$this->matriculaTable = $this->getServiceLocator()->get('MatriculaTable');
    	}
    	return $this->matriculaTable;
    }
    
    private  function getDBCargaAcademicaTable()
    {
    	if (!$this->cargaAcademicaTable)
    	{
    		$this->cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');
    	}
    	return $this->cargaAcademicaTable;
    }
    
    private  function getDBCursoAperturadoTable()
    {
    	if (!$this->cursoAperturadoTable)
    	{
    		$this->cursoAperturadoTable = $this->getServiceLocator()->get('CursoAperturadoTable');
    	}
    	return $this->cursoAperturadoTable;
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
    
    private  function getDBDocenteTable()
    {
    	if (!$this->docenteTable)
    	{
    		$this->docenteTable = $this->getServiceLocator()->get('DocenteTable');
    	}
    	return $this->docenteTable;
    }
    
    private  function getDBEstudianteTable()
    {
    	if (!$this->estudianteTable)
    	{
    		$this->estudianteTable = $this->getServiceLocator()->get('EstudianteTable');
    	}
    	return $this->estudianteTable;
    }
    
}

<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\FormInterface;
use Admin\Model\Miscellanea;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Form\BuscarForm; 

class CargaAcademicaController extends AbstractActionController
{
	private $codCargaAcademica;
	private $areaConocimientoTable;
	private $cargaAcademicaTable;
	private $cursoAperturadoTable;
	private $carreraProfesionalTable;
	private $cicloAcademicoTable;
	private $modalidadTable;
	private $aulaTable;
	private $seccionTable;
	private $cursoTable;
	private $docenteTable;
    private $matriculaTable;
    private $horarioTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{    	
			$buscarForm = new BuscarForm();
    		
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codCargaAcademica';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $buscarForm->setData($data);
                
                $texto = $data['texto'];
                $select->where->like('curso','%'.$texto.'%')
                ->or->like('nombres','%'.$texto.'%')
                ->or->like('primerApellido','%'.$texto.'%')
                ->or->like('segundoApellido','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $cargasAcademicas = $this->getDBCargaAcademicaTable()->obtenerCargasAcademicasPagination($select);

            $itemsPerPage = 10;
            
            $cargasAcademicas->current();
            
            $paginator = new Paginator(new paginatorIterator($cargasAcademicas));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
                    
            return new ViewModel(array(
                'cargasAcademicas' => $paginator,
                'buscarForm' => $buscarForm,
                'orderby' => $order_by,
                'order' => $order,
            )); 
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarCargaAcademicaAction()
    {
    	if($this->identity())
    	{
    		$form = new \Admin\Form\CargaAcademicaForm();
    		$form->get("codAreaConocimiento")->setValueOptions($this->getDBAreaConocimientoTable()->obtenerAreasConocimientoArray());
    		$form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
    		$form->get("codCicloAcademico")->setValueOptions($this->getDBCicloAcademicoTable()->obtenerCiclosAcademicosArray());
    		$form->get("codModalidad")->setValueOptions($this->getDBModalidadTable()->obtenerModalidadesArray());
    		$form->get("codAula")->setValueOptions($this->getDBAulaTable()->obtenerAulasArray());
    		$form->get("codSeccion")->setValueOptions($this->getDBSeccionTable()->obtenerSeccionesArray());
    		$form->get("codDocente")->setValueOptions($this->getDBDocenteTable()->obtenerDocentesArray());
    	
    		$request = $this->getRequest();
    		
    		if($request->isPost())
    		{
    			$data_post = $request->getPost();   			 
    			$form->setInputFilter(new \Admin\Form\Filter\CargaAcademicaFilter());
    			$form->setData($data_post);
    			
    			if ($form->isValid())
    			{
    				$data = $form->getData(FormInterface::VALUES_AS_ARRAY);
    			
    				$cursoAperturado = $this->getDBCursoAperturadoTable()->obtenerCursoAperturado($data['codCicloAcademico'], $data['codCurso']);
    				
    				if(!$cursoAperturado)
    				{
    					$nuevoCursoAperturado = array(
    							'fechaApertura' 		=> gmdate("Y-m-d", Miscellanea::getHoraLocal(-5)),    							
    							'codCicloAcademico' 	=> $data['codCicloAcademico'],
    							'codCurso' 				=> $data['codCurso'],
    					);
    					 
    					$this->getDBCursoAperturadoTable()->insertar($nuevoCursoAperturado);
    					 
    				}
    				
    				$cargaAcademica = array(
    						'fechaInicioClases' 		=> $data['fechaInicioClases'],
    						'paralelo' 					=> $data['paralelo'],
    						'esComun' 					=> $data['esComun'],
    						'codAreaConocimiento' 		=> $data['codAreaConocimiento'],
    						'codCarreraProfesional' 	=> $data['codCarreraProfesional'] != "" ? $data['codCarreraProfesional'] : null,
    						'codCicloAcademico' 		=> $data['codCicloAcademico'],
    						'codCurso' 					=> $data['codCurso'],
    						'codModalidad' 				=> $data['codModalidad'],
    						'codAula' 					=> $data['codAula'],
    						'codSeccion' 				=> $data['codSeccion'],
    						'codDocente' 				=> $data['codDocente'],
    				
    				);
    				
    				//\Zend\Debug\Debug::dump($this->getDBCargaAcademicaTable()->insertar($cargaAcademica)); return ;
    				
    				$lastInsertID = $this->getDBCargaAcademicaTable()->insertar($cargaAcademica);
    				
    				if($lastInsertID)
    				{
    					$this->flashMessenger()->addMessage('¡La Carga Académica ' . $lastInsertID. ' ha sido agregada correctamente!');
    					
    					return $this->redirect()->toRoute('carga-academica');
    					
    					//return $this->redirect()->toUrl('carga-academica'); // Verificar por que ninguno de estos métodos funciona
    					
    					//return $this->forward()->dispatch('Admin\Controller\CargaAcademica', array('action' => 'index'));
    				}
    			}
    			
    		}
    		else
    		{    		
	    		$codCurso = (int) $this->params()->fromRoute('codcurso', 0);
	    		 
	    		//Verificamos que se envien los parámetros necesarios.
	    		if(!$codCurso)
	    			return $this->redirect()->toRoute('curso');
	    		
	    		$cursoSeleccionado = $this->getDBCursoTable()->obtenerCurso($codCurso);
	    		
	    		if(!$cursoSeleccionado)
	    			return $this->redirect()->toRoute('curso');
	    				
	    		$form->get('codCurso')->setValue($cursoSeleccionado['codCurso']);
	    		$form->get('codigo')->setValue($cursoSeleccionado['codigo']);
	    		$form->get('curso')->setValue($cursoSeleccionado['curso']);    		
    		
    		}
    	
    		return new ViewModel(array(
    				'form' 		=> $form,
    				'text'		=>'Agregar',
    				'action'	=>'agregar-carga-academica'
    		));
    		
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }      
    
    public function editarCargaAcademicaAction()
    {
    	if($this->identity())
    	{
    		$form = new \Admin\Form\CargaAcademicaForm();
    		$form->get("codAreaConocimiento")->setValueOptions($this->getDBAreaConocimientoTable()->obtenerAreasConocimientoArray());
    		$form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
    		$form->get("codCicloAcademico")->setValueOptions($this->getDBCicloAcademicoTable()->obtenerCiclosAcademicosArray());
    		$form->get("codModalidad")->setValueOptions($this->getDBModalidadTable()->obtenerModalidadesArray());
    		$form->get("codAula")->setValueOptions($this->getDBAulaTable()->obtenerAulasArray());
    		$form->get("codSeccion")->setValueOptions($this->getDBSeccionTable()->obtenerSeccionesArray());
    		$form->get("codDocente")->setValueOptions($this->getDBDocenteTable()->obtenerDocentesArray());
    		
    		
    		$form->get('paralelo')->setAttribute('disabled', 'disabled');
    		$form->get('esComun')->setAttribute('disabled', 'disabled');
    		$form->get('codCarreraProfesional')->setAttribute('disabled', 'disabled');
    		$form->get('codCicloAcademico')->setAttribute('disabled', 'disabled');
    		$form->get('codModalidad')->setAttribute('disabled', 'disabled');    		
    		 
    		$request = $this->getRequest();
    		
    		if($request->isPost())
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
    				return $this->redirect()->toRoute('carga-academica');		
    			
    			$form->setInputFilter(new \Admin\Form\Filter\CargaAcademicaFilter());
    			$form->setValidationGroup(array('codCurso', 'fechaInicioClases', 'codAula', 'codSeccion', 'codDocente'));    			
    			$form->setData($cargaAcademica);
    			$form->setData($request->getPost());    			
    			
    			if ($form->isValid())
    			{
    				$data_form = $form->getData(FormInterface::VALUES_AS_ARRAY);
    			
    				//Debug::dump($data_form); return ;
    				
    				$editarCargaAcademica = array(
    						'fechaInicioClases' 		=> $data_form['fechaInicioClases'],    						
    						'codAula' 					=> $data_form['codAula'],
    						'codSeccion' 				=> $data_form['codSeccion'],
    						'codDocente' 				=> $data_form['codDocente']    				
    				);
    				
    				$where = array(
    						'codCargaAcademica' 	=> $codCargaAcademica,
    						'codCicloAcademico' 	=> $codCicloAcademico,
    						'codCurso' 				=> $codCurso,
    						'codModalidad' 			=> $codModalidad,
    						'paralelo' 				=> $paralelo
    				);
    				
    				if($this->getDBCargaAcademicaTable()->actualizar($editarCargaAcademica, $where))
    				{
    					$this->flashMessenger()->addMessage('¡La Carga Académica ' . $codCargaAcademica . ' ha sido editada correctamente!');
    					return $this->redirect()->toRoute('carga-academica');
    				}  				
    			}
    		}
    		else
    		{
    			$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
	    		$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
	    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
	    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
	    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
	    		 
	    		//Verificamos que se envien los parámetros necesarios.
	    		if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo)
	    		{
	    			return $this->redirect()->toRoute('carga-academica');
	    		}	    			
	    		
	    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademica($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
	    		
	    		if(!$cargaAcademica)
	    			return $this->redirect()->toRoute('carga-academica'); 		
	    		
	    		$form->setData($cargaAcademica);
    		} 		
    		
    		
    		$view = new ViewModel(array(
    				'form' 				=> $form,
    				'cargaAcademica' 	=> $cargaAcademica,
    				'text'				=> 'Editar',
    				'action'			=> 'editar-carga-academica'
    		));
    		 
    		$view->setTemplate('admin/carga-academica/agregar-carga-academica.phtml');
    		return $view;
    	}

    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function eliminarCargaAcademicaAction()
    {
        if($this->identity())
        {
        	$request = $this->getRequest();
        	
            if($request->isPost())
            {
                $data = $request->getPost();
                
             	$respuesta =  $data['eliminar'];

                if ($respuesta == 'si')
                {
                	$codCargaAcademica = $request->getPost('codCargaAcademica');
	                $codCicloAcademico = $request->getPost('codCicloAcademico');
	                $codCurso = $request->getPost('codCurso');
	                $codModalidad = $request->getPost('codModalidad');
	                $paralelo = $request->getPost('paralelo');
	                
	                if($this->getDBCargaAcademicaTable()->eliminar($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo))
	                {
	                	$this->flashMessenger()->addMessage('¡La Carga Académica ' . $codCargaAcademica . ', se ha eliminado correctamente!');	                	
	                }else{
	                	$this->flashMessenger()->addMessage('¡La Carga Académica ' . $codCargaAcademica . ', no ha sido eliminada correctamente!');
	                }
                }
                return $this->redirect()->toRoute('carga-academica');                
            
            }            
            else
            {
            	$codCargaAcademica 		= (int) $this->params()->fromRoute('codcargaacademica', 0);
            	$codCicloAcademico 		= (int) $this->params()->fromRoute('codcicloacademico', 0);
	    		$codCurso 				= (int) $this->params()->fromRoute('codcurso', 0);
	    		$codModalidad 			= (int) $this->params()->fromRoute('codmodalidad', 0);
	    		$paralelo	 			= $this->params()->fromRoute('paralelo', null);
            	 
            	//Verificamos que se envien los parámetros necesarios.
	    		if(!$codCargaAcademica || !$codCicloAcademico || !$codCurso || !$codModalidad || !$paralelo)
	    		{
	    			return $this->redirect()->toRoute('carga-academica');
	    		}
            }
            
            $cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademica($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo);
            
            if(!$cargaAcademica)
            	return $this->redirect()->toRoute('carga-academica');
            
            return array(
            		'cargaAcademica' => $cargaAcademica
            );

        }
        
        return $this->redirect()->toRoute('ingresar');
    	 
    }
    
    //Función Ajax que carga las Áreas del conocimiento
    public function areasConocimientoAjaxAction()
    {
    		$request = $this->getRequest();
    
    		if ($request->isPost())
    		{    
    			$response = $this->getResponse();
    
    			$areas = $this->getDBAreaConocimientoTable()->obtenerAreasConocimientoArray();
    			
    			$response->setContent(\Zend\Json\Json::encode($areas));   
    		}
    		
    		return $response;

    }
    
    //Función Ajax que carga las carreras profesionales de acuerdo al código de área del conocimiento
    public function carrerasProfesionalesAjaxAction()
    {
    	$request = $this->getRequest();
    
    	if ($request->isPost())
    	{
    		$data = $request->getPost();
    		
    		$codAreaConocimiento = $data['codAreaConocimiento'];
    		
    		$response = $this->getResponse();
    
    		$carreras = $this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesPorCodAreaConocimientoArray($codAreaConocimiento);
    		 
    		$response->setContent(\Zend\Json\Json::encode($carreras));
    	}
    
    	return $response;
    
    }
    
    private  function getDBAreaConocimientoTable()
    {
    	if (!$this->areaConocimientoTable)
    	{
    		$this->areaConocimientoTable = $this->getServiceLocator()->get('AreaConocimientoTable');
    	}
    	return $this->areaConocimientoTable;
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

    private  function getDBMatriculaTable()
    {
        if (!$this->matriculaTable)
        {
            $this->matriculaTable = $this->getServiceLocator()->get('MatriculaTable');
        }
        return $this->matriculaTable;
    }
    private  function getDBHorarioTable()
    {
        if (!$this->horarioTable)
        {
            $this->horarioTable = $this->getServiceLocator()->get('HorarioTable');
        }
        return $this->horarioTable;
    }
    
}

<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SilaboController extends AbstractActionController
{
	private $cargaAcademicaTable;
	private $semanaTable;
	private $tematicaTable;
	private $silaboDetalleTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{
    		$codCargaAcademica = (int) $this->params()->fromRoute('codcargaacademica', 0);
    			
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCargaAcademica){   		
    			return $this->redirect()->toRoute('carga-academica');
    		}	
    		
    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademicaPorCodigo($codCargaAcademica);
    		
    		if(!$cargaAcademica){
    			return $this->redirect()->toRoute('carga-academica');
    		}    			
    		
    		$temas = $this->getDBSilaboDetalleTable()->obtenerTemas($codCargaAcademica);
    		
    		$temasArray = $this->getDBTematicaTable()->obtenerTemasArray();
    		
    		$form = new \Admin\Form\SilaboForm();
    		
    		$form->get('codSemana')->setValueOptions($this->getDBSemanaTable()->obtenerSemanasArray());
    		
    		return new ViewModel(array(
    				'cargaAcademica' => $cargaAcademica,
    				'temas' => $temas,
    		        'temasArray' => $temasArray,
    				'form' 	=> $form,
    		));
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function autocompleteAction()
    {
        header('Content-Type: application/json');
        
        $temas = $this->getDBTematicaTable()->obtenerTemasArray();
        
        $valuesJson = \Zend\Json\Json::encode($temas);
        echo $valuesJson;
        die ();
    }

    public function agregarTemaAction()
    {
    	if($this->identity())
    	{  		
    		$codCargaAcademica = (int) $this->params()->fromRoute('codcargaacademica', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCargaAcademica)
    			return $this->redirect()->toRoute('carga-academica');

    		$cargaAcademica = $this->getDBCargaAcademicaTable()->obtenerCargaAcademicaPorCodigo($codCargaAcademica);
    		
    		if(!$cargaAcademica)
    			return $this->redirect()->toRoute('carga-academica');
    		
    		$request = $this->getRequest();
    		
    		if($request->isPost())
    		{
    			$form = new \Admin\Form\SilaboForm();
    			$form->get('codSemana')->setValueOptions($this->getDBSemanaTable()->obtenerSemanasArray());
    			$form->setInputFilter(new \Admin\Form\Filter\SilaboFilter());
    			$form->setData($request->getPost());
    			
    			if($form->isValid())
    			{
    				$data = $form->getData();
    				
    				$nuevaTematica = array(
    						'tematica' 	=> $data['tematica'],
    				);
    				
    				$codTematica = $this->getDBTematicaTable()->insertar($nuevaTematica);
    				
    				if($codTematica)
    				{
    					$silaboDetalle = array(
    						'codCargaAcademica'  => $codCargaAcademica,
    						'codSemana' 	     => $data['codSemana'],
    						'codTematica' 	     => $codTematica,
    					);
    					
    					$codSilaboDetalle = $this->getDBSilaboDetalleTable()->insertar($silaboDetalle);
    					
    					if($codSilaboDetalle)
    					{
    						return $this->redirect()->toRoute('silabo', array('action' => 'index', 'codcargaacademica' => $codCargaAcademica));    					
    					}
    					
    				}	
    				
    			}
    			
    			$temas = $this->getDBSilaboDetalleTable()->obtenerTemas($codCargaAcademica);

    			$view = new ViewModel(
    					array(
    							'cargaAcademica' => $cargaAcademica,
    							'temas'          => $temas,
    							'form' 	         => $form,
    					)
    			);
    			$view->setTemplate('admin/silabo/index.phtml');
    			return $view;    			
    		}	    	   	
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }    
    
    public function eliminarTemaAction()
    {
    	if($this->identity())
    	{
    		$codSilaboDetalle 	   = (int) $this->params()->fromRoute('codsilabodetalle', 0);    		
    		$codCargaAcademica     = (int) $this->params()->fromRoute('codcargaacademica', 0);
    		$codTematica           = (int) $this->params()->fromRoute('codtematica', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codSilaboDetalle || !$codCargaAcademica || !$codTematica)
    			return $this->redirect()->toRoute('carga-academica');   	
    		
    	
    		$silaboDetalle = $this->getDBSilaboDetalleTable()->obtenerSilaboDetalle($codSilaboDetalle);
    			
    		if($silaboDetalle)
    		{    	
    			if($this->getDBSilaboDetalleTable()->eliminar($codSilaboDetalle)){
    			    $this->getDBTematicaTable()->eliminar($codTematica);
    			}
    		}
    	
    		return $this->redirect()->toRoute('silabo', array('action' => 'index', 'codcargaacademica' => $codCargaAcademica));
    	
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBCargaAcademicaTable()
    {
        if (!$this->cargaAcademicaTable)
        {
            $this->cargaAcademicaTable = $this->getServiceLocator()->get('CargaAcademicaTable');
        }
        return $this->cargaAcademicaTable;
    }
    
    private  function getDBSilaboDetalleTable()
    {
    	if (!$this->silaboDetalleTable)
    	{
    		$this->silaboDetalleTable = $this->getServiceLocator()->get('SilaboDetalleTable');
    	}
    	return $this->silaboDetalleTable;
    }
    
    private  function getDBSemanaTable()
    {
    	if (!$this->semanaTable)
    	{
    		$this->semanaTable = $this->getServiceLocator()->get('SemanaTable');
    	}
    	return $this->semanaTable;
    }
    
    private  function getDBTematicaTable()
    {
    	if (!$this->tematicaTable)
    	{
    		$this->tematicaTable = $this->getServiceLocator()->get('TematicaTable');
    	}
    	return $this->tematicaTable;
    }  
    
}

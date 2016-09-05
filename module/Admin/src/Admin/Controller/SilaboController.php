<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SilaboController extends AbstractActionController
{
	private $cursoTable;
	private $semanaTable;
	private $tematicaTable;
	private $silaboDetalleTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{
    		$codCurso = (int) $this->params()->fromRoute('codcurso', 0);
    			
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCurso)
    		{   		
    			return $this->redirect()->toRoute('curso');
    		}	
    		
    		$curso = $this->getDBCursoTable()->obtenerCurso($codCurso);
    		
    		if(!$curso){
    			return $this->redirect()->toRoute('curso');
    		}    			
    		
    		$temas = $this->getDBSilaboDetalleTable()->obtenerTemas($codCurso);
    		
    		$form = new \Admin\Form\SilaboForm();
    		
    		$form->get('codSemana')->setValueOptions($this->getDBSemanaTable()->obtenerSemanasArray());
    		
    		return new ViewModel(array(
    				'curso' => $curso,
    				'temas' => $temas,
    				'form' 	=> $form,
    		));
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }    

    public function agregarTemaAction()
    {
    	if($this->identity())
    	{  		
    		$codCurso = (int) $this->params()->fromRoute('codcurso', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codCurso)
    			return $this->redirect()->toRoute('curso');

    		$curso = $this->getDBCursoTable()->obtenerCurso($codCurso);
    		
    		if(!$curso)
    			return $this->redirect()->toRoute('curso');
    		
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
    						'codCurso' 		=> $codCurso,
    						'codSemana' 	=> $data['codSemana'],
    						'codTematica' 	=> $codTematica,
    					);
    					
    					$codSilaboDetalle = $this->getDBSilaboDetalleTable()->insertar($silaboDetalle);
    					
    					if($codSilaboDetalle)
    					{
    						return $this->redirect()->toRoute('silabo', array('action' => 'index', 'codcurso' => $codCurso));    					
    					}
    					
    				}	
    				
    			}
    			
    			$temas = $this->getDBSilaboDetalleTable()->obtenerTemas($codCurso);

    			$view = new ViewModel(
    					array(
    							'curso' => $curso,
    							'temas' => $temas,
    							'form' 	=> $form,
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
    		$codSilaboDetalle 	= (int) $this->params()->fromRoute('codsilabodetalle', 0);    		
    		$codCurso 			= (int) $this->params()->fromRoute('codcurso', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codSilaboDetalle || !$codCurso)
    			return $this->redirect()->toRoute('curso');   	
    		
    	
    		$silaboDetalle = $this->getDBSilaboDetalleTable()->obtenerSilaboDetalle($codSilaboDetalle);
    			
    		if($silaboDetalle)
    		{    	
    			$this->getDBSilaboDetalleTable()->eliminar($codSilaboDetalle);
    		}
    	
    		return $this->redirect()->toRoute('silabo', array('action' => 'index', 'codcurso' => $codCurso));
    	
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBCursoTable()
    {
    	if (!$this->cursoTable)
    	{
    		$this->cursoTable = $this->getServiceLocator()->get('CursoTable');
    	}
    	return $this->cursoTable;
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

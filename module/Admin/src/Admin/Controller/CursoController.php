<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Entity\Curso;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Form\BuscarForm;

class CursoController extends AbstractActionController
{
	private $cursoTable;
	private $planEstudioTable;
	
    public function indexAction()
    {
        if($this->identity())
    	{
    		$select = new Select();
    		
    		$order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codcurso';
    		
    		$order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
    		
    		$select->order($order_by . ' ' . $order);
    		
    		$request = $this->getRequest();
    		 
    		if($request->isPost())
    		{
    			$data = $request->getPost();
    			$texto = $data['texto'];
    			$select->where->like('curso', '%' . $texto . '%');
    		}
    		
    		$page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
    		
    		$cursos = $this->getDBCursoTable()->obtenerCursosPagination($select);
    		
    		$itemsPerPage = 10;
    		
    		$cursos->current();
    		
    		$paginator = new Paginator(new paginatorIterator($cursos));
    		
    		$paginator->setCurrentPageNumber($page)
    					->setItemCountPerPage($itemsPerPage)
    					->setPageRange(6);  		
    	
    		return new ViewModel(array(
    				'cursos' => $paginator,
                    'buscarForm' => new BuscarForm(),
    				'orderby' => $order_by,
    				'order' => $order,
    		));
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarCursoAction()
    {
    	if($this->identity())
    	{    	
    		$form = new \Admin\Form\CursoForm();
    		$form->get("codPlanEstudio")->setValueOptions($this->getDBPlanEstudioTable()->obtenerPlanesEstudioArray());
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\CursoFilter());
    			$form->setData($request->getPost());
    	
    			if ($form->isValid())
    			{
    				$curso = $form->getData();
    						
    				if($this->getDBCursoTable()->insertar($curso))
    				{
    					return $this->redirect()->toRoute('curso');
    				}
    	
    			}
    			 
    		}
    	
    		return new ViewModel(array(
    				'form' => $form,
    				'text'=>'Agregar',
    				'action'=>'agregar-curso'
    		));
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }    
    
    
    public function editarCursoAction()
    {
    	if($this->identity())
    	{
    		$form = new \Admin\Form\CursoForm();
    		$form->get("codPlanEstudio")->setValueOptions($this->getDBPlanEstudioTable()->obtenerPlanesEstudioArray());
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\CursoFilter());
    	
    			$form->setData($request->getPost());
    			 
    			if ($form->isValid())
    			{
    				$curso = $form->getData();    				
    	
    				if($this->getDBCursoTable()->actualizar($curso))
    				{
    					return $this->redirect()->toRoute('curso');
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
    	
    			$curso = new Curso();
    	
    			$curso->exchangeArray($cursoSeleccionado);
    	
    			$form->bind($curso);
    	
    		}
    	
    		$view = new ViewModel(
    				array(
    						'form' => $form,
    						'text'=>'Editar',
    						'action'=>'editar-curso'
    				)
    		);
    		$view->setTemplate('admin/curso/agregar-curso.phtml');
    		return $view;
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function eliminarCursoAction()
    {
    	if($this->identity())
    	{
    		$request = $this->getRequest();
    		$response = $this->getResponse();
    		
    		if ($request->isPost())
    		{
    			$post_data = $request->getPost();
    	
    			$codCurso = $post_data['id'];
    			$estado = 0;
    	
    			if ($this->getDBCursoTable()->eliminar($codCurso, $estado))
    			{
    				$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
    			}
    			else
    			{
    				$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
    			}
    	
    		}
    		return $response;
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
    
    private  function getDBPlanEstudioTable()
    {
    	if (!$this->planEstudioTable)
    	{
    		$this->planEstudioTable = $this->getServiceLocator()->get('PlanEstudioTable');
    	}
    	return $this->planEstudioTable;
    }
    
}

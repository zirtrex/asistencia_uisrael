<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Entity\CicloAcademico;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Form\BuscarForm; 


class CicloAcademicoController extends AbstractActionController
{
	private $cicloAcademicoTable;
	
    public function indexAction()
    {

        if($this->identity())
        {  
            
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codcicloacademico';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('anio','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $ciclosAcademicos = $this->getDBCicloAcademicoTable()->obtenerCiclosAcademicosPagination($select);

            $itemsPerPage = 10;
            
            $ciclosAcademicos->current();
            
            $paginator = new Paginator(new paginatorIterator($ciclosAcademicos));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);     
            
            $request = $this->getRequest();
        
            return new ViewModel(array(
                'ciclosAcademicos' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            ));   
        }
        
        return $this->redirect()->toRoute('ingresar');
    }
    
    public function agregarCicloAction()
    {
    	if($this->identity())
    	{    	
    		$form = new \Admin\Form\CicloAcademicoForm();
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\CicloAcademicoFilter());
    			$form->setValidationGroup(array('anio', 'semestre'));
    			$form->setData($request->getPost());
    			 
    			if ($form->isValid())
    			{
    				$cicloAcademico = $form->getData();    				
    				
    				if($this->getDBCicloAcademicoTable()->insertar($cicloAcademico))    	
    					return $this->redirect()->toRoute('ciclo-academico');
    			}
    		}
    	
    		return new ViewModel(array(
    				'form' 		=> $form,
    				'text'		=>'Agregar',
    				'action'	=>'agregar-ciclo'
    		));    	
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    }    
    
    public function editarCicloAction()
    {
    	if($this->identity())
    	{
    		$form = new \Admin\Form\CicloAcademicoForm();
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\CicloAcademicoFilter());
    			$form->setData($request->getPost());
    			 
    			if ($form->isValid())
    			{
    				$cicloAcademico = $form->getData();
    	
    				if($this->getDBCicloAcademicoTable()->actualizar($cicloAcademico))
    					return $this->redirect()->toRoute('ciclo-academico');
    	
    			}    	
    		}
    		else
    		{
    			$codCicloAcademico = (int) $this->params()->fromRoute('codcicloacademico', 0);
    	
    			//Verificamos que se envien los parámetros necesarios.
    			if(!$codCicloAcademico)
    				return $this->redirect()->toRoute('ciclo-academico');
    	
    			$cicloAcademicoSeleccionado = $this->getDBCicloAcademicoTable()->obtenerCicloAcademico($codCicloAcademico);
    	
    			if(!$cicloAcademicoSeleccionado)
    				return $this->redirect()->toRoute('ciclo-academico');
    	
    			$cicloAcademico = new CicloAcademico();
    	
    			$cicloAcademico->exchangeArray($cicloAcademicoSeleccionado);
    	
    			$form->bind($cicloAcademico); //unimos el objeto Estudiante al formulario y lo carga correctamente
    	
    		}
    	
    		$view = new ViewModel(
    				array(
    						'form' 		=> $form,
    						'text'		=>'Editar',
    						'action'	=>'editar-ciclo'
    				)
    		);
    		$view->setTemplate('admin/ciclo-academico/agregar-ciclo.phtml');
    		return $view;
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function eliminarCicloAction()
    {
    	if($this->identity())
    	{
    		$request = $this->getRequest();
    		$response = $this->getResponse();
    		if ($request->isPost())
    		{
    			$data = $request->getPost();
    	
    			$codCicloAcademico = $data['id'];
    			$estado = 0;
    	
    			if ($this->getDBCicloAcademicoTable()->eliminar($codCicloAcademico, $estado))
    				$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
    			else
    				$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
    	
    		}
    		return $response;
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    } 
    
    private  function getDBCicloAcademicoTable()
    {
    	if (!$this->cicloAcademicoTable)
    	{
    		$this->cicloAcademicoTable = $this->getServiceLocator()->get('CicloAcademicoTable');
    	}
    	return $this->cicloAcademicoTable;
    }
    
}

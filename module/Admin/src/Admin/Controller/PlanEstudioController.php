<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\PlanEstudio;
use Admin\Form\BuscarForm;

class PlanEstudioController extends AbstractActionController
{
	private $carreraProfesionalTable;
	private $planEstudioTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{
    		
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codplanestudio';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('titulo','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $planesEstudio = $this->getDBPlanEstudioTable()->obtenerPlanesEstudioPagination($select);

            $itemsPerPage = 10;
            
            $planesEstudio->current();
            
            $paginator = new Paginator(new paginatorIterator($planesEstudio));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
        
            return new ViewModel(array(
                'planesEstudio' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            )); 
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarPlanAction()
    {
    	if($this->identity())
    	{
    		$form = new \Admin\Form\PlanEstudioForm();
    		$form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\PlanEstudioFilter());
    			$form->setValidationGroup(array('titulo', 'anioPlanEstudio', 'numeroCiclos', 'codCarreraProfesional'));
    			$form->setData($request->getPost());
    	
    			if ($form->isValid())
    			{
    				$planEstudio = $form->getData();										
    						
    				if($this->getDBPlanEstudioTable()->insertar($planEstudio))
    					return $this->redirect()->toRoute('plan-estudio');
    			}  			
    			 
    		}
    	
    		return new ViewModel(array(
    				'form' 		=> $form,
    				'text'		=>'Agregar',
    				'action'	=>'agregar-plan'
    		));
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }    
    
    
    public function editarPlanAction()
    {
    	$form = new \Admin\Form\PlanEstudioForm();
    	$form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
    	 
    	$request = $this->getRequest();
    	 
    	if($request->isPost())
    	{
    		$form->setInputFilter(new \Admin\Form\Filter\PlanEstudioFilter());
    		$form->setData($request->getPost());
    	
    		if ($form->isValid())
    		{
    			$planEstudio = $form->getData();
    			$planEstudio->setEstado("1");
    			 
    			if($this->getDBPlanEstudioTable()->actualizar($planEstudio))
    				return $this->redirect()->toRoute('plan-estudio');
    			 
    		}
    	}
    	else
    	{
    		$codPlanEstudio = (int) $this->params()->fromRoute('codplanestudio', 0);
    		 
    		//Verificamos que se envien los parámetros necesarios.
    		if(!$codPlanEstudio)
    			return $this->redirect()->toRoute('plan-estudio');
    		 
    		$planEstudioSeleccionado = $this->getDBPlanEstudioTable()->obtenerPlanEstudio($codPlanEstudio);
    		 
    		if(!$planEstudioSeleccionado)
    			return $this->redirect()->toRoute('plan-estudio');
    		 
    		$planEstudio = new PlanEstudio();
    		 
    		$planEstudio->exchangeArray($planEstudioSeleccionado);
    		 
    		$form->bind($planEstudio);
    		 
    	}
    	 
    	$view = new ViewModel(
    			array(
    					'form' 		=> $form,
    					'text'		=>'Editar',
    					'action'	=>'editar-plan'
    			)
    	);
    	$view->setTemplate('admin/plan-estudio/agregar-plan.phtml');
    	return $view;
    }
    
    public function eliminarPlanAction()
    {
    	if($this->identity())
    	{
            $request = $this->getRequest();

            if ($request->isPost()) 
            {                
                $data = $request->getPost();
                
                $respuesta =  $data['eliminar'];

                if ($respuesta == 'si')
                {
	                $codPlanEstudio = $request->getPost('codPlanEstudio');
	                
	                if($this->getDBPlanEstudioTable()->eliminar($codPlanEstudio))
	                {
	                	$this->flashMessenger()->addMessage('¡El Plan de Estudio con código: ' . $codPlanEstudio . ', se ha eliminado correctamente!');	                	
	                }
                }
                return $this->redirect()->toRoute('plan-estudio');
            }
            else
            {
            	$codPlanEstudio = (int) $this->params()->fromRoute('codplanestudio', 0);
            	
            	if (!$codPlanEstudio)
            	{
            		return $this->redirect()->toRoute('plan-estudio');
            	}	
            }
            
            $planEstudio = $this->getDBPlanEstudioTable()->obtenerPlanEstudio($codPlanEstudio);            

            return array(
            		'codPlanEstudio' => $codPlanEstudio,
            		'planEstudio' => $planEstudio
            );
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBPlanEstudioTable()
    {
    	if (!$this->planEstudioTable)
    	{
    		$this->planEstudioTable = $this->getServiceLocator()->get('PlanEstudioTable');
    	}
    	return $this->planEstudioTable;
    }
    
	private  function getDBCarreraProfesionalTable()
    {
    	if (!$this->carreraProfesionalTable)
    	{
    		$this->carreraProfesionalTable = $this->getServiceLocator()->get('CarreraProfesionalTable');
    	}
    	return $this->carreraProfesionalTable;
    }
    
}

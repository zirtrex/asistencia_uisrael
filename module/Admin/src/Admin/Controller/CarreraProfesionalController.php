<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\CarreraProfesional;
use Admin\Form\BuscarForm;


class CarreraProfesionalController extends AbstractActionController
{
    private  $carreraProfesionalTable;
    private  $areaConocimientoTable;

	public function indexAction()
    {
        if($this->identity())
        {            
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codcarreraprofesional';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

        	if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('carreraProfesional', '%'.$texto.'%'); // \Zend\Debug\Debug::dump($data);
            }
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $carrerasProfesionales = $this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesPagination($select);

            $itemsPerPage = 10;
            
            $carrerasProfesionales->current();
            
            $paginator = new Paginator(new paginatorIterator($carrerasProfesionales));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
            
            return new ViewModel(array(
                'carrerasProfesionales' => $paginator,
            	'messages' => $this->flashmessenger()->getMessages(),
            	'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            ));   
        }
        
        return $this->redirect()->toRoute('ingresar');
        
    }    
    
    public function agregarCarreraProfesionalAction()
    {
    	if($this->identity())
    	{  		
    		$form = new \Admin\Form\CarreraProfesionalForm();
    		$form->get("codAreaConocimiento")->setValueOptions($this->getDBAreaConocimientoTable()->obtenerAreasConocimientoArray());
    		  		
    		$request = $this->getRequest();
    		
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\CarreraProfesionalFilter());
    			$form->setValidationGroup(array('codigo', 'carreraProfesional', 'codAreaConocimiento'));
    			$form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $carrera = $form->getData();

                    if($this->getDBCarreraProfesionalTable()->insertar($carrera))
                    {
                        return $this->redirect()->toRoute('carrera-profesional');
                    }
                }
    		}    		
    		
    		return new ViewModel(array(
    				'form' 		=> $form,
                    'text'		=>'Agregar',
                    'action'	=>'agregar-carrera-profesional'
    		));    		
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	 
    }    
    
    public function editarCarreraProfesionalAction()
    {
    	if($this->identity())
        {       
            $form = new \Admin\Form\CarreraProfesionalForm();
            $form->get("codAreaConocimiento")->setValueOptions($this->getDBAreaConocimientoTable()->obtenerAreasConocimientoArray());
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\CarreraProfesionalFilter());             
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $carreraProfesional = $form->getData();

                    if($this->getDBCarreraProfesionalTable()->actualizar($carreraProfesional))
                    {
                        return $this->redirect()->toRoute('carrera-profesional');
                    }            
                }                
            }
            else
            {
                $codCarreraProfesional = (int) $this->params()->fromRoute('codcarreraprofesional', 0);
                
                //Verificamos que se envien los parámetros necesarios.
                if(!$codCarreraProfesional)
                {
                    return $this->redirect()->toRoute('carrera-profesional');
                }
                
                $carreraSeleccionada = $this->getDBCarreraProfesionalTable()->obtenerCarreraProfesional($codCarreraProfesional);           

                if(!$carreraSeleccionada)
                {
                    return $this->redirect()->toRoute('carrera-profesional');
                }
                
                $carrera = new CarreraProfesional();                
                $carrera->exchangeArray($carreraSeleccionada);
                
                $form->bind($carrera); //Unimos los datos de la consulta con el formulario              
                
            }

            $view = new ViewModel(array(
                          'form' 	=> $form,
                          'text'	=> 'Editar',
                          'action'	=> 'editar-carrera-profesional'
            ));
            
            $view->setTemplate('admin/carrera-profesional/agregar-carrera-profesional.phtml');
            
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function eliminarCarreraProfesionalAction()
    {
    	if($this->identity())
    	{
            $request = $this->getRequest();

            if ($request->isPost()) 
            {                
                $data = $request->getPost(); // \Zend\Debug\Debug::dump($data); return ;
                
                $respuesta =  $data['eliminar'];

                if ($respuesta == 'si')
                {
	                $codCarreraProfesional = $request->getPost('codCarreraProfesional');
	                
	                if($this->getDBCarreraProfesionalTable()->eliminar($codCarreraProfesional))
	                {
	                	$this->flashMessenger()->addMessage('¡La Carrera Profesional con código: ' . $codCarreraProfesional . ', se ha eliminado correctamente!');	                	
	                }
                }
                return $this->redirect()->toRoute('carrera-profesional');
            }
            else
            {
            	$codCarreraProfesional = (int) $this->params()->fromRoute('codcarreraprofesional', 0);
            	
            	if (!$codCarreraProfesional)
            	{
            		return $this->redirect()->toRoute('carrera-profesional');
            	}	
            }
            
            $carreraProfesional = $this->getDBCarreraProfesionalTable()->obtenerCarreraProfesional($codCarreraProfesional);            

            return array(
            		'codCarreraProfesional' => $codCarreraProfesional,
            		'carreraProfesional' => $carreraProfesional
            );
    	}
    
    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function eliminarCarreraProfesionalAjaxAction()
    {        
        if($this->identity())
        {
            $request = $this->getRequest();
            
            if ($request->isPost())
            {
                $data = $request->getPost();
                
                $codCarreraProfesional = $data['id'];
                $estado = 0; 
                
                $response = $this->getResponse();
                
                if ($this->getDBCarreraProfesionalTable()->eliminar($codCarreraProfesional, $estado))
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
        
        throw new \Exception("!No se encuentra autenticado!");
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
    
}

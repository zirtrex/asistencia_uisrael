<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Form\BuscarForm;
use Admin\Entity\AreaConocimiento;


class AreaConocimientoController extends AbstractActionController
{
    private  $areaConocimientoTable;

	public function indexAction()
    {
        if($this->identity())
        {            
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codareaconocimiento';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

        	if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('areaConocimiento', '%'.$texto.'%'); // \Zend\Debug\Debug::dump($data);
            }
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $areasConocimiento = $this->getDBAreaConocimientoTable()->obtenerAreasConocimientoPagination($select);

            $itemsPerPage = 10;
            
            $areasConocimiento->current();
            
            $paginator = new Paginator(new paginatorIterator($areasConocimiento));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
            
            return new ViewModel(array(
                'areasConocimiento' => $paginator,
            	'messages' => $this->flashmessenger()->getMessages(),
            	'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            ));   
        }
        
        return $this->redirect()->toRoute('ingresar');
        
    }    
    
    public function agregarAreaConocimientoAction()
    {
    	if($this->identity())
    	{  		
    		$form = new \Admin\Form\AreaConocimientoForm();
    		  		
    		$request = $this->getRequest();
    		
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\AreaConocimientoFilter());
    			$form->setValidationGroup(array('areaConocimiento'));
    			$form->setData($request->getPost());              
                 
                if ($form->isValid())
                {                   
                    $areaConocimiento = $form->getData();

                    if($this->getDBAreaConocimientoTable()->insertar($areaConocimiento))
                    {
                        return $this->redirect()->toRoute('area-conocimiento');
                    }
                }
    		}    		
    		
    		return new ViewModel(array(
    				'form' 		=> $form,
                    'text'		=>'Agregar',
                    'action'	=>'agregar-area-conocimiento'
    		));    		
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	 
    }    
    
    public function editarAreaConocimientoAction()
    {
    	if($this->identity())
        {       
            $form = new \Admin\Form\AreaConocimientoForm();         
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\AreaConocimientoFilter());             
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $areaConocimiento = $form->getData();

                    if($this->getDBAreaConocimientoTable()->actualizar($areaConocimiento))
                    {
                        return $this->redirect()->toRoute('area-conocimiento');
                    }            
                }                
            }
            else
            {
                $codAreaConocimiento = (int) $this->params()->fromRoute('codareaconocimiento', 0);
                
                //Verificamos que se envien los parámetros necesarios.
                if(!$codAreaConocimiento)
                {
                    return $this->redirect()->toRoute('area-conocimiento');
                }
                
                $areaSeleccionada = $this->getDBAreaConocimientoTable()->obtenerAreaConocimiento($codAreaConocimiento);           

                if(!$areaSeleccionada)
                {
                    return $this->redirect()->toRoute('area-conocimiento');
                }
                
                $areaConocimiento = new AreaConocimiento();
                $areaConocimiento->exchangeArray($areaSeleccionada);
                
                $form->bind($areaConocimiento); //Unimos los datos de la consulta con el formulario              
                
            }

            $view = new ViewModel(array(
                          'form' 	=> $form,
                          'text'	=>'Editar',
                          'action'	=>'editar-area-conocimiento'
            ));
            
            $view->setTemplate('admin/area-conocimiento/agregar-area-conocimiento.phtml');
            
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function eliminarAreaConocimientoAction()
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
	                $codAreaConocimiento = $request->getPost('codAreaConocimiento');
	                
	                if($this->getDBAreaConocimientoTable()->eliminar($codAreaConocimiento))
	                {
	                	$this->flashMessenger()->addMessage('¡El Área del Conocimiento con código: ' . $codAreaConocimiento . ', se ha eliminado correctamente!');	                	
	                }
                }
                return $this->redirect()->toRoute('area-conocimiento');
            }
            else
            {
            	$codAreaConocimiento = (int) $this->params()->fromRoute('codareaconocimiento', 0);
            	
            	if (!$codAreaConocimiento)
            	{
            		return $this->redirect()->toRoute('area-conocimiento');
            	}	
            }
            
            $areaConocimiento = $this->getDBAreaConocimientoTable()->obtenerAreaConocimiento($codAreaConocimiento);            

            return array(
            		'codAreaConocimiento' => $codAreaConocimiento,
            		'areaConocimiento' => $areaConocimiento
            );
    	}
    
    	return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBAreaConocimientoTable()
    {
    	if (!$this->areaConocimientoTable)
    	{
    		$this->areaConocimientoTable = $this->getServiceLocator()->get('AreaConocimientoTable');
    	}
    	return $this->areaConocimientoTable;
    }
    
}


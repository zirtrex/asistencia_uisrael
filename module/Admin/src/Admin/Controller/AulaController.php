<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\Aula;
use Admin\Form\BuscarForm;

class AulaController extends AbstractActionController
{
    private  $aulaTable;

	public function indexAction()
    {
        if($this->identity())
        {  
            
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codaula';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('numero','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $aulas = $this->getDBAulaTable()->obtenerAulasPagination($select);

            $itemsPerPage = 10;
            
            $aulas->current();
            
            $paginator = new Paginator(new paginatorIterator($aulas));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);           
        
            return new ViewModel(array(
                'aulas' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            )); 
        }
        
        return $this->redirect()->toRoute('ingresar');
        
    }
    
    public function agregarAulaAction()
    {
        if($this->identity())
        {    
        	$form = new \Admin\Form\AulaForm();         
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\AulaFilter());
                $form->setData($request->getPost());
                $form->setValidationGroup(array('numero','piso','capacidad'));
                 
                if ($form->isValid())
                {                   
                    $aula = $form->getData();
                    
                    if($this->getDBAulaTable()->insertar($aula))
                        return $this->redirect()->toRoute('aula');                    
            
                }
                
            }
            
            return new ViewModel(array(
                          'form' => $form,
                          'text'=>'Agregar',
                          'action'=>'agregar-aula'
            ));
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    }
    
    public function editarAulaAction()
    {
    	if($this->identity())
        {       
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\AulaForm');  
          
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\AulaFilter());
                $form->setValidationGroup(array('codAula','numero','piso','capacidad'));
                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $aula = $form->getData();
                  //  die(var_dump($aula));
                    
                    if($this->getDBAulaTable()->actualizar($aula))
                        return $this->redirect()->toRoute('aula');                    
            
                }
                
            }
            else
            {
                $codAula = (int) $this->params()->fromRoute('codaula', 0);
                
                //Verificamos que se envien los parámetros necesarios.
                if(!$codAula)
                    return $this->redirect()->toRoute('aula');
                
                $aulaSeleccionado = $this->getDBAulaTable()->obtenerAula($codAula);         
                
                if(!$aulaSeleccionado)
                    return $this->redirect()->toRoute('aula');
                
                $aula = new Aula();
                
                $aula->exchangeArray($aulaSeleccionado);
                
                $form->bind($aula);         
                
            }

            $view = new ViewModel(
                array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-aula'
                      )
                );
            $view->setTemplate('admin/aula/agregar-aula.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function eliminarAulaAction()
    {
        
        if($this->identity())
        {
            $request = $this->getRequest();
            $response = $this->getResponse();
            if ($request->isPost())
            {
                $data = $request->getPost();
                
                $codAula = $data['id'];
                $estado = 0; 

                if ($this->getDBAulaTable()->eliminar($codAula, $estado))
                    $response->setContent(\Zend\Json\Json::encode(array('response' => true)));
                else
                    $response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                
            }
            return $response;
        }
        
        return $this->redirect()->toRoute('ingresar');
    }    
    
    private  function getDBAulaTable()
    {
    	if (!$this->aulaTable)
    	{
    		$this->aulaTable = $this->getServiceLocator()->get('AulaTable');
    	}
    	return $this->aulaTable;
    }
    
}

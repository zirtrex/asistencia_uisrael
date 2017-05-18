<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\Usuario;
use Admin\Form\BuscarForm;

class UsuarioController extends AbstractActionController
{
    private  $usuarioTable;

	public function indexAction()
    {
        if($this->identity())
        {        
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codusuario';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('usuario','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $usuarios = $this->getDBUsuarioTable()->obtenerUsuariosPagination($select);

            $itemsPerPage = 10;
            
            $usuarios->current();
            
            $paginator = new Paginator(new paginatorIterator($usuarios));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
            
            $request = $this->getRequest();         
            
        
            return new ViewModel(array(
                'usuarios' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            )); 
        }
        
        return $this->redirect()->toRoute('ingresar');
        
    }
    
    public function editarUsuarioAction()
    {
    	if($this->identity())
        {       
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\UsuarioForm');  
          
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\UsuarioFilter());
                //$form->setValidationGroup(array('codUsuario','usuario','rol'));
                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $usuario = $form->getData();
                    
                    if($this->getDBUsuarioTable()->actualizar($usuario))
                    {
                        return $this->redirect()->toRoute('usuario');
                    }                   
            
                }
                
            }
            else
            {
                $codUsuario = (int) $this->params()->fromRoute('codusuario', 0);
                
                //Verificamos que se envien los parámetros necesarios.
                if(!$codUsuario)
                    return $this->redirect()->toRoute('usuario');
                
                $usuarioSeleccionado = $this->getDBUsuarioTable()->obtenerUsuario($codUsuario);         
                
                if(!$usuarioSeleccionado)
                    return $this->redirect()->toRoute('usuario');
                
                $usuario = new Usuario();
                
                $usuario->exchangeArray($usuarioSeleccionado);
                
                $form->bind($usuario);         
                
            }

            $view = new ViewModel(
                array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-usuario'
                      )
                );
            $view->setTemplate('admin/usuario/agregar-usuario.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function eliminarUsuarioAction()
    {
        
        if($this->identity())
        {
            $request = $this->getRequest();
            $response = $this->getResponse();
            if ($request->isPost())
            {
                $data = $request->getPost();
                
                $codUsuario = $data['id'];
                
                if ($this->getDBUsuarioTable()->eliminar($codUsuario))
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
    
    
    private  function getDBUsuarioTable()
    {
    	if (!$this->usuarioTable)
    	{
    		$this->usuarioTable = $this->getServiceLocator()->get('UsuarioTable');
    	}
    	return $this->usuarioTable;
    }
    
}

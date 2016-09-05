<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\Administrador;
use Admin\Entity\Usuario;
use Admin\Form\BuscarForm;

class AdministradorController extends AbstractActionController
{
	private $administradorTable;
	private $personaTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codadministrador';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('nombres','%'.$texto.'%');
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

            $administradores = $this->getDBAdministradorTable()->obtenerAdministradoresPagination($select);

            $itemsPerPage = 10;
            
            $administradores->current();
            
            $paginator = new Paginator(new paginatorIterator($administradores));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
            
            $request = $this->getRequest();         
            
        
            return new ViewModel(array(
                'administradores' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            )); 
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarAdministradorAction()
    {
        if($this->identity())
        {    
        
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\AdministradorForm');         
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\AdministradorFilter());
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   

                    $administrador = $form->getData();
                    
                    $persona = $administrador->getPersona();

                    $codPersona = $this->getDBPersonaTable()->insertar($persona);
                    
                    if($codPersona)
                    {                        
                        $administrador->getPersona()->setCodPersona($codPersona);

                        if($this->getDBAdministradorTable()->insertar($administrador))
                            return $this->redirect()->toRoute('administrador');
                    
                    }
            
                }
                
            }
            
            return new ViewModel(array(
                          'form' => $form,
                          'text'=>'Agregar',
                          'action'=>'agregar-administrador'
            ));
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 

    public function asignarUsuarioAction()
    {
        if($this->identity())
        {        
            $form = new \Admin\Form\UsuarioForm();         
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                //primero guardar el usuario y obtener su id
                $form->setInputFilter(new \Admin\Form\Filter\UsuarioFilter());                
                $form->setValidationGroup(array('codUsuario','usuario','clave','rol'));
                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $usuario = $form->getData();
                    $codAdministrador = $usuario->getCodUsuario();
                    
                    $codUsuario = $this->getDBUsuarioTable()->insertar($usuario);

                    if($codUsuario){
                        //ahora actualizar el administrador

                        $administradorSeleccionado = $this->getDBAdministradorTable()->obtenerAdministrador($codAdministrador);
                        
                        $administrador = new Administrador();
                        $administrador->exchangeArray($administradorSeleccionado);
                        $administrador->getUsuario()->setCodUsuario($codUsuario);

                        if($this->getDBAdministradorTable()->actualizar($administrador))
                        {
                           return $this->redirect()->toRoute('administrador');
                        }
                    }                                           
            
                }
                
            }
            else
            {
                $codAdministrador = (int) $this->params()->fromRoute('codadministrador', 0); //codigo para luego modificarlo con el nuevo id de usuario

                //Verificamos que se envien los parámetros necesarios.
                if(!$codAdministrador)
                {
                    return $this->redirect()->toRoute('administrador');
                }
                
                $administradorSeleccionado = $this->getDBAdministradorTable()->obtenerAdministrador($codAdministrador);
                
                if(!$administradorSeleccionado)
                {
                    return $this->redirect()->toRoute('administrador');
                }

                $administrador = new Administrador();
                $administrador->exchangeArray($administradorSeleccionado);
                $nombrePersona = $administrador->getPersona()->getNombres() . ' ' . $administrador->getPersona()->getPrimerApellido();
                
                
                $usuario = new Usuario();
                
                $usuario->setRol('administrador'); //predeterminar la casilla del combo
                $usuario->setCodUsuario($codAdministrador); //guardar el codigo en el formulario usuario 
                
                $form->bind($usuario);
            }

            
            $view = new ViewModel(array(
                          'form' => $form,
                          'text'=>'Asignar usuario a ' . $nombrePersona . ' (Administrador)',
                          'controller'=>'administrador',
                          'action'=>'asignar-usuario'
            ));

            $view->setTemplate('admin/usuario/agregar-usuario.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 

    public function editarAdministradorAction()
    {
        if($this->identity())
        {        
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\AdministradorForm');         
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\AdministradorFilter());
                $form->setValidationGroup(array(
                		'codAdministrador',
                		'Persona' => array('codPersona', 'nombres', 'primerApellido', 'segundoApellido', 'tipoDocumento', 'numeroDocumento', 'fechaNacimiento','correo', 'celular')
                ));
                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $administrador = $form->getData();
                    
                    $persona = $administrador->getPersona();
                    
                    if($this->getDBPersonaTable()->actualizar($persona))
                    {
                        return $this->redirect()->toRoute('administrador');
                    }
                    
            
                }
                
            }
            else
            {
            	$codAdministrador = (int) $this->params()->fromRoute('codadministrador', 0);
            	
            	//Verificamos que se envien los parámetros necesarios.
            	if(!$codAdministrador)
                {
            		return $this->redirect()->toRoute('administrador');
                }
            	
            	$administradorSeleccionado = $this->getDBAdministradorTable()->obtenerAdministrador($codAdministrador);
            	
            	if(!$administradorSeleccionado)
                {
            		return $this->redirect()->toRoute('administrador');
                }
            	
            	$administrador = new Administrador();
            	
            	$administrador->exchangeArray($administradorSeleccionado);
            	
            	$form->bind($administrador); //unimos el objeto administrador al formulario y lo carga correctamente            	
                
            }

            $view = new ViewModel(array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-administrador'
            ));
            
            $view->setTemplate('admin/administrador/agregar-administrador.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 
    
    public function eliminarAdministradorAction()
    {        
        if($this->identity())
        {
            $request = $this->getRequest();
            $response = $this->getResponse();
            
            if ($request->isPost())
            {
                $data = $request->getPost();
                
                $codAdministrador = $data['id'];
                
                if(!$codAdministrador)
                {
                	$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }
                 
                $administradorSeleccionado = $this->getDBAdministradorTable()->obtenerAdministrador($codAdministrador);
                 
                if(!$administradorSeleccionado)
                {
                	$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }
                else
                {
                	$administrador = new Administrador();
                	 
                	$administrador->exchangeArray($administradorSeleccionado);
                	
                	if ($this->getDBAdministradorTable()->eliminar($codAdministrador))
                	{
                		$this->getDBUsuarioTable()->eliminar($administrador->getUsuario()->getCodUsuario());
                		
                		$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
                	
                	}
					$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }    
            }
            return $response;
        }
        
        return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBAdministradorTable()
    {
    	if (!$this->administradorTable)
    	{
    		$this->administradorTable = $this->getServiceLocator()->get('administradorTable');
    	}
    	return $this->administradorTable;
    }
    
    private  function getDBUsuarioTable()
    {
        if (!$this->personaTable)
        {
            $this->personaTable = $this->getServiceLocator()->get('UsuarioTable');
        }
        return $this->personaTable;
    }
    
    private  function getDBPersonaTable()
    {
        if (!$this->personaTable)
        {
            $this->personaTable = $this->getServiceLocator()->get('PersonaTable');
        }
        return $this->personaTable;
    }

    
}

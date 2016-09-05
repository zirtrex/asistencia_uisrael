<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Entity\Docente;
use Admin\Entity\Usuario;
use Admin\Form\BuscarForm; 

class DocenteController extends AbstractActionController
{
	private $docenteTable;
	private $personaTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{    		
            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'coddocente';
            
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

            $docentes = $this->getDBDocenteTable()->obtenerDocentesPagination($select);

            $itemsPerPage = 10;
            
            $docentes->current();
            
            $paginator = new Paginator(new paginatorIterator($docentes));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);            
        
            return new ViewModel(array(
                'docentes' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
            )); 
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarDocenteAction()
    {
    	if($this->identity())
    	{    	
    		$formManager = $this->getServiceLocator()->get('FormElementManager');
    		$form = $formManager->get('Admin\Form\DocenteForm');
    	
    		$request = $this->getRequest();
    	
    		if($request->isPost())
    		{
    			$form->setInputFilter(new \Admin\Form\Filter\DocenteFilter());
    			$form->setData($request->getPost());
    	
    			if ($form->isValid())
    			{
    				$docente = $form->getData();
    	
    				$persona = $docente->getPersona();
    	
    				$codPersona = $this->getDBPersonaTable()->insertar($persona);
    	
    				if($codPersona)
    				{
    					$docente->getPersona()->setCodPersona($codPersona); 					
    						
    					if($this->getDBDocenteTable()->insertar($docente))
                        {
    						return $this->redirect()->toRoute('docente');
                        }
    				}
    	
    			}
    			 
    		}
    	
    		return new ViewModel(array(
    				'form' 		=> $form,
                    'text'		=>'Agregar',
                    'action'	=>'agregar-docente'
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
                $form->setData($request->getPost());
                $form->setValidationGroup(array('codUsuario', 'usuario', 'clave', 'rol'));
                 
                if ($form->isValid())
                {                   
                    $usuario = $form->getData();
                    $usuario->setClave(md5($usuario->getClave()));
                    $codDocente = $usuario->getCodUsuario();
                    
                    $codUsuario = $this->getDBUsuarioTable()->insertar($usuario);

                    if($codUsuario)
                    {
                        //ahora actualizar docente

                        $docenteSeleccionado = $this->getDBDocenteTable()->obtenerDocente($codDocente);
                        
                        $docente = new Docente();
                        $docente->exchangeArray($docenteSeleccionado);
                        $docente->getUsuario()->setCodUsuario($codUsuario);

                        if($this->getDBDocenteTable()->actualizar($docente))
                        {
                           return $this->redirect()->toRoute('docente');
                        }
                    }                                           
            
                }
                
            }
            else
            {
                $codDocente = (int) $this->params()->fromRoute('coddocente', 0); //codigo para luego modificarlo con el nuevo id de usuario

                //Verificamos que se envien los parámetros necesarios.

                if(!$codDocente)
                {
                    return $this->redirect()->toRoute('docente');
                }
                
                $docenteSeleccionado = $this->getDBDocenteTable()->obtenerDocente($codDocente);
                
                if(!$docenteSeleccionado)
                    return $this->redirect()->toRoute('docente');

                $docente = new Docente();
                $docente->exchangeArray($docenteSeleccionado);
                $numeroDocumento = $docente->getPersona()->getNumeroDocumento();
                $nombrePersona = $docente->getPersona()->getNombres() . ' ' . $docente->getPersona()->getPrimerApellido();
                
                $usuario = new Usuario();
                
                $usuario->setRol('docente'); //predeterminar la casilla del combo
                $usuario->setUsuario($numeroDocumento);
                $usuario->setCodUsuario($codDocente); //guardar el codigo en el formulario usuario 
                
                $form->bind($usuario);
            }

            
            $view = new ViewModel(array(
                          'form' => $form,
                          'text'=>'Asignar usuario a ' . $nombrePersona . ' (Docente)',
                          'controller'=>'docente',
                          'action'=>'asignar-usuario'
            ));

            $view->setTemplate('admin/usuario/agregar-usuario.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 
    
    public function editarDocenteAction()
    {
    	if($this->identity())
        {        
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\DocenteForm');         
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\DocenteFilter());
                $form->setValidationGroup(array(
                        'codDocente', 'modalidad', 'gradoAcademico','profesion',
                        'Persona' => array('codPersona', 'nombres', 'primerApellido', 'segundoApellido', 'tipoDocumento', 'numeroDocumento', 'fechaNacimiento','correo', 'celular')
                ));
                
                $form->setData($request->getPost());                
                

                if ($form->isValid())
                {                   
                    $docente = $form->getData();
                    
                    $persona = $docente->getPersona();
                    
                    if($this->getDBPersonaTable()->actualizar($persona))
                    {   
                                        

                        if($this->getDBDocenteTable()->actualizar($docente))
                        {
                            return $this->redirect()->toRoute('docente');
                        }
                    }
            
                }
                
            }
            else
            {
                $coddocente = (int) $this->params()->fromRoute('coddocente', 0);
                
                //Verificamos que se envien los parámetros necesarios.

                if(!$coddocente)
                    return $this->redirect()->toRoute('docente');
                
                $docenteSeleccionado = $this->getDBDocenteTable()->obtenerdocente($coddocente);    

                if(!$docenteSeleccionado)
                {
                    return $this->redirect()->toRoute('docente');
                }

                
                $docente = new Docente();
                
                $docente->exchangeArray($docenteSeleccionado);
                
                $form->bind($docente); //unimos el objeto docente al formulario y lo carga correctamente              
                
            }

            $view = new ViewModel(
                array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-docente'
                      )
                );
            $view->setTemplate('admin/docente/agregar-docente.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    }
    
    public function eliminarDocenteAction()
    {
    	if($this->identity())
        {
            $request = $this->getRequest();
            $response = $this->getResponse();
            
            if ($request->isPost())
            {
                $post_data = $request->getPost();                
                $codDocente = $post_data['id'];
                
                if(!$codDocente)
                {
                	$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }
                
                $docenteSeleccionado = $this->getDBDocenteTable()->obtenerdocente($codDocente);
                
                if(!$docenteSeleccionado)
                {
                	$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }
                else
                {
                	$docente = new Docente();
                	
                	$docente->exchangeArray($docenteSeleccionado);
                
	                if ($this->getDBDocenteTable()->eliminar($codDocente))
	                {
	                	$this->getDBUsuarioTable()->eliminar($docente->getUsuario()->getCodUsuario());
	                	
	                	$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
	                	
	                }               
	                $response->setContent(\Zend\Json\Json::encode(array('response' => false)));
                }
                
            }
            
            return $response;
        }
        
        return $this->redirect()->toRoute('ingresar');
    }
    
    private  function getDBDocenteTable()
    {
    	if (!$this->docenteTable)
    	{
    		$this->docenteTable = $this->getServiceLocator()->get('DocenteTable');
    	}
    	return $this->docenteTable;
    }
    
    private  function getDBPersonaTable()
    {
    	if (!$this->personaTable)
    	{
    		$this->personaTable = $this->getServiceLocator()->get('PersonaTable');
    	}
    	return $this->personaTable;
    }
    private  function getDBUsuarioTable()
    {
        if (!$this->personaTable)
        {
            $this->personaTable = $this->getServiceLocator()->get('UsuarioTable');
        }
        return $this->personaTable;
    }
    
}

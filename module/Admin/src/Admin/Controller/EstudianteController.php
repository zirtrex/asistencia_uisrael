<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Entity\Estudiante;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Admin\Form\BuscarForm; 


class EstudianteController extends AbstractActionController
{
	private $estudianteTable;
	private $carreraProfesionalTable;
	private $personaTable;
	
    public function indexAction()
    {
    	if($this->identity())
    	{   

            $select = new Select();
            
            $order_by = $this->params()->fromRoute('orderby') ? $this->params()->fromRoute('orderby') : 'codestudiante';
            
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            
            $select->order($order_by . ' ' . $order);
            
            $request = $this->getRequest();

            if($request->isPost())
            {
                $data = $request->getPost();
                $texto = $data['texto'];
                $select->where->like('nombres',$texto);
            } 
            
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

    		$estudiantes = $this->getDBEstudianteTable()->obtenerEstudiantePagination($select);

            $itemsPerPage = 10;
            
            $estudiantes->current();
            
            $paginator = new Paginator(new paginatorIterator($estudiantes));
            
            $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($itemsPerPage)
                        ->setPageRange(6);
    		
    		return new ViewModel(array(
				'estudiantes' => $paginator,
                'buscarForm' => new BuscarForm(),
                'orderby' => $order_by,
                'order' => $order,
    		));
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function agregarEstudianteAction()
    {
        if($this->identity())
        {    
        
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\EstudianteForm');         
            
            $form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\EstudianteFilter());
                $form->setData($request->getPost());              
                 
                if ($form->isValid())
                {                   
                    $estudiante = $form->getData();
                    
                    $persona = $estudiante->getPersona();                                 
                    
                    $codPersona = $this->getDBPersonaTable()->insertar($persona);
                    
                    if($codPersona)
                    {               
                        $estudiante->getPersona()->setCodPersona($codPersona); 

                        if($this->getDBEstudianteTable()->insertar($estudiante))
                        {
                            return $this->redirect()->toRoute('estudiante');
                        }
                    }
            
                }
                
            }
            
            return new ViewModel(array(
                          'form' => $form,
                          'text'=>'Agregar',
                          'action'=>'agregar-estudiante'
            ));
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 

    public function editarEstudianteAction()
    {
        if($this->identity())
        {    
        
            $formManager = $this->getServiceLocator()->get('FormElementManager');
            $form = $formManager->get('Admin\Form\EstudianteForm');         
            
            $form->get("codCarreraProfesional")->setValueOptions($this->getDBCarreraProfesionalTable()->obtenerCarrerasProfesionalesArray());
            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                $form->setInputFilter(new \Admin\Form\Filter\EstudianteFilter());
                $form->setValidationGroup(array(
                		'codEstudiante', 'codCarreraProfesional', 'anioIngreso',
                		'Persona' => array('codPersona', 'nombres', 'primerApellido', 'segundoApellido', 'tipoDocumento', 'numeroDocumento', 'fechaNacimiento','correo', 'celular')
                ));
                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $estudiante = $form->getData();
                    
                    $persona = $estudiante->getPersona();
                    $codCarreraProfesional = $estudiante->getCodCarreraProfesional();

                    
                    if($this->getDBPersonaTable()->actualizar($persona))
                    {                        
                        if($this->getDBEstudianteTable()->actualizar($estudiante))
                        {
                            return $this->redirect()->toRoute('estudiante');
                        }
                    }
            
                }
                
            }
            else
            {
            	$codEstudiante = (int) $this->params()->fromRoute('codestudiante', 0);
            	
            	//Verificamos que se envien los parámetros necesarios.
            	if(!$codEstudiante)
            		return $this->redirect()->toRoute('estudiante');
            	
            	$estudianteSeleccionado = $this->getDBEstudianteTable()->obtenerEstudiante($codEstudiante);        	
            	
            	if(!$estudianteSeleccionado)
            		return $this->redirect()->toRoute('estudiante');
            	
            	$estudiante = new Estudiante();
            	
            	$estudiante->exchangeArray($estudianteSeleccionado);
            	
            	$form->bind($estudiante); //unimos el objeto Estudiante al formulario y lo carga correctamente            	
                
            }

            $view = new ViewModel(
                array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-estudiante'
                      )
                );
            $view->setTemplate('admin/estudiante/agregar-estudiante.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    } 
    
    public function eliminarEstudianteAction()
    {
    	if($this->identity())
        {
            $request = $this->getRequest();
            $response = $this->getResponse();
            
            if ($request->isPost())
            {
                $post_data = $request->getPost();
                
                $codEstudiante = $post_data['id'];
                $estado = 0; 
                
                if ($this->getDBEstudianteTable()->eliminar($codEstudiante, $estado))
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
    
    private  function getDBEstudianteTable()
    {
    	if (!$this->estudianteTable)
    	{
    		$this->estudianteTable = $this->getServiceLocator()->get('EstudianteTable');
    	}
    	return $this->estudianteTable;
    }
    
    private  function getDBCarreraProfesionalTable()
    {
    	if (!$this->carreraProfesionalTable)
    	{
    		$this->carreraProfesionalTable = $this->getServiceLocator()->get('CarreraProfesionalTable');
    	}
    	return $this->carreraProfesionalTable;
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

<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Entity\Config;
use Admin\Form\ConfigForm;

class ConfigController extends AbstractActionController
{
    private  $configTable;

	public function indexAction()
    {
        if($this->identity())
        {  
            $configuraciones = $this->getDBConfigTable()->obtenerConfiguraciones();        
        
            return new ViewModel(array(
                'config' => $configuraciones,
            )); 
        }        
        return $this->redirect()->toRoute('ingresar');        
    }
    
    public function agregarConfigAction()
    {
        if($this->identity())
        {    
        	$form = new \Admin\Form\ConfigForm();            
            $request = $this->getRequest();
            
            if($request->isPost())
            {
                //$form->setInputFilter(new \Admin\Form\Filter\AulaFilter());
                $form->setData($request->getPost());
                //$form->setValidationGroup(array('numero','piso','capacidad'));
                 
                if ($form->isValid())
                {                   
                    $config = $form->getData();
                    
                    
                    
                    if($this->getDBConfigTable()->insertar(array('name' => $config->getName(), 'value' => $config->getValue()))){
                        return $this->redirect()->toRoute('config');                    
                    }
                }                
            }
            
            return new ViewModel(array(
                          'form' => $form,
                          'text'=>'Agregar',
                          'action'=>'agregar-config'
            ));
        }
         
        return $this->redirect()->toRoute('ingresar');
         
    }
    
    public function editarConfigAction()
    {
    	if($this->identity())
        {       
            $form = new \Admin\Form\ConfigForm();
            $request = $this->getRequest();
            
            if($request->isPost())
            {                
                $form->setData($request->getPost());
                 
                if ($form->isValid())
                {                   
                    $config = $form->getData();
                    
                    if($this->getDBConfigTable()->actualizar($config)){
                        return $this->redirect()->toRoute('config'); 
                    }            
                }                
            }
            else
            {
                $idConfig = (int) $this->params()->fromRoute('idconfig', 0);
                
                //Verificamos que se envien los parámetros necesarios.
                if(!$idConfig){
                    return $this->redirect()->toRoute('config');
                }
                
                $configSeleccionado = $this->getDBconfigTable()->obtenerConfiguracion($idConfig);    
                
                if(!$configSeleccionado){
                    return $this->redirect()->toRoute('config');
                }
                
                $config = new Config();
                
                $config->exchangeArray($configSeleccionado);
                
                $form->bind($config);
                
            }

            $view = new ViewModel(
                array(
                          'form' => $form,
                          'text'=>'Editar',
                          'action'=>'editar-config'
                      )
                );
            $view->setTemplate('admin/config/agregar-config.phtml');
            return $view;
        }
         
        return $this->redirect()->toRoute('ingresar');
    	
    }
    
    public function eliminarConfigAction()
    {
        
        if($this->identity())
        {
            $idConfig = (int) $this->params()->fromRoute('idconfig', 0);           

            if ($this->getDBConfigTable()->eliminar($idConfig))
            {
                return $this->redirect()->toRoute('config');
            }
            else
            {
                \Zend\Debug\Debug::dump("No se ha podido eliminar la configuración: " . $idConfig);
                
                $configuraciones = $this->getDBConfigTable()->obtenerConfiguraciones();
                
                $view = new ViewModel(array('config' => $configuraciones));
                
                $view->setTemplate('admin/config/index.phtml');
                
                return $view;
            }                
            
        }
        
        return $this->redirect()->toRoute('ingresar');
    }    
    
    private  function getDBConfigTable()
    {
    	if (!$this->configTable)
    	{
    		$this->configTable = $this->getServiceLocator()->get('ConfigTable');
    	}
    	return $this->configTable;
    }
    
}

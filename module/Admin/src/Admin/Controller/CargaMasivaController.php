<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\File\Transfer\Adapter\Http;


class CargaMasivaController extends AbstractActionController
{    
    public function indexAction()
    {
    	if($this->identity())
    	{    		
    		$errorMessage = null;
    		
    		$form = new \Admin\Form\FileForm();    		
    		$form->setInputFilter(new \Admin\Form\Filter\FileFilter());

    		$request = $this->getRequest();
    		
    		if($request->isPost())
    		{
    			$data = array_merge_recursive(
    					$this->getRequest()->getPost()->toArray(),
    					$this->getRequest()->getFiles()->toArray()
    			);
    			
    			$form->setData($data); 			
    			
    			if($form->isValid())
    			{
    				$data = $form->getData();
    				
    				$uploadFile = $data['csv'];
    				
    				$nombreArchivo = explode("." , $uploadFile['name']);
    				
    				$destination = $this->getFileUploadLocation();
    				
    				$adapter = new Http();
    				
    				$adapter->setDestination($destination);
    				
    				if($data['tabla'] == $nombreArchivo[0])
    				{    				
	    				if($adapter->receive($uploadFile['name']))
	    				{   					
	    					return $this->redirect()->toRoute('carga-masiva', array('action' => 'previsualizar', 'file' => $nombreArchivo[0]));
	    				}else{
	    					throw new \Exception('¡El archivo no pudo ser cargado!');
	    				}
    				}
    				else
    				{
    					$errorMessage = "¡El nombre de la tabla y el archivo subido no coinciden!";
    				}
    				
    			}   			
    			
    		}
    		
    		return new ViewModel(array(
    				'form' => $form,
    				'errorMessage' => $errorMessage
    		));    		
    		
    	}
    	 
    	return $this->redirect()->toRoute('ingresar');
    	 
    }
    
    public function previsualizarAction()
    {
    	if($this->identity())
    	{
	    	$file = $this->params()->fromRoute('file');
	    	 
	    	if($file)
	    	{    	
	    		$path = $this->getFileUploadLocation();
	    					
	    		$sourceFileName = $path . '/' . $file . '.csv';
	    		
	    		if(file_exists ($sourceFileName))
	    		{    	
		    		$registros = array();
		    	
		    		if (($fichero = fopen($sourceFileName, "r")) !== FALSE)
		    		{
		    			$nombres_campos = fgetcsv($fichero, 0, ";", "\"", "\\");
		    			$num_campos = count($nombres_campos);
		    						
		    			while (($datos = fgetcsv($fichero, 0, ";", "\"", "\\")) !== FALSE)
		    			{
		    				for($icampo = 0; $icampo < $num_campos; $icampo++)
		    				{
		    					$registro[$nombres_campos[$icampo]] = trim($datos[$icampo]);
		    				}
		    				
		    				$registros[] = $registro;
		    			}
		    			
		    			fclose($fichero);
		    			  					 
	    				return new ViewModel(array(    							
	    						'nombres_campos' => $nombres_campos,
	    						'registros' => $registros,
	    						'file' => $file,
	    				));
		    		}
		    		
	    		}
	    		    	
	    	}
	    	return $this->redirect()->toRoute('carga-masiva');
	    	
    	}
    	
    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function guardarAction()
    {
    	if($this->identity())
    	{
    		$file = $this->params()->fromRoute('file');
    	
	    	if($file)
	    	{
	    		$table = null;
	    		$errorMessages = array();
	    		
				switch ($file){
					case "area_conocimiento" : $table = $this->getServiceLocator()->get("AreaConocimientoTable");
					break;
					case "carrera_profesional" : $table = $this->getServiceLocator()->get("CarreraProfesionalTable");
					break;
					case "usuario" : $table = $this->getServiceLocator()->get("UsuarioTable");
					break;
					case "persona" : $table = $this->getServiceLocator()->get("PersonaTable");
					break;
					case "administrador" : $table = $this->getServiceLocator()->get("AdministradorTable");
					break;
					case "estudiante" : $table = $this->getServiceLocator()->get("EstudianteTable");
					break;
					case "docente" : $table = $this->getServiceLocator()->get("DocenteTable");
					break;
					case "plan_estudio" : $table = $this->getServiceLocator()->get("PlanEstudioTable");
					break;
					case "curso" : $table = $this->getServiceLocator()->get("CursoTable");					
					break;
					case "semana" : $table = $this->getServiceLocator()->get("SemanaTable");
					break;
					case "tematica" : $table = $this->getServiceLocator()->get("TematicaTable");
					break;
					case "silabo_detalle" : $table = $this->getServiceLocator()->get("SilaboDetalleTable");
					break;
					case "ciclo_academico" : $table = $this->getServiceLocator()->get("CicloAcademicoTable");
					break;
					case "curso_aperturado" : $table = $this->getServiceLocator()->get("CursoAperturadoTable");
					break;
					case "modalidad" : $table = $this->getServiceLocator()->get("ModalidadTable");
					break;
					case "aula" : $table = $this->getServiceLocator()->get("AulaTable");
					break;
					case "seccion" : $table = $this->getServiceLocator()->get("SeccionTable");
					break;
					case "carga_academica" : $table = $this->getServiceLocator()->get("CargaAcademicaTable");
					break;
					case "dia_semana" : $table = $this->getServiceLocator()->get("DiaSemanaTable");
					break;
					case "horario" : $table = $this->getServiceLocator()->get("HorarioTable");
					break;
					case "matricula" : $table = $this->getServiceLocator()->get("MatriculaTable");
					break;
				}    		
	    	 
	    		$path = $this->getFileUploadLocation();
	    		$sourceFileName = $path . '/' . $file . '.csv';
	    	 
		    	if(file_exists ($sourceFileName))
		    	{
		    		$registros = array();
		    	
		    		if (($fichero = fopen($sourceFileName, "r")) !== FALSE)
		    		{
		    			$nombres_campos = fgetcsv($fichero, 0, ";", "\"", "\\");
		    			$num_campos = count($nombres_campos);
		    			$num_registros_insertados= 0;
		    	
		    			while (($datos = fgetcsv($fichero, 0, ";", "\"", "\\")) !== FALSE)
		    			{
		    				for($icampo = 0; $icampo < $num_campos; $icampo++){
		    					$registro[$nombres_campos[$icampo]] = trim((string)$datos[$icampo]);
		    				}
		    	
		    				try{
		    					if($table->insertarCM($registro))
		    					{
		    						$num_registros_insertados++;
		    					}
		    				}
		    				catch (\Exception $e)
		    				{
		    					$errorMessages[] = "Registro con código: " . $registro[$nombres_campos[0]] . ", No se ha insertado correctamente, comuníquese, con el administrador del sistema";
		    					$errorMessages[] = $e->getMessage();
		    				}
		    	
		    			}
		    			fclose($fichero);
		    			 
		    			$errorMessages[] = "Se han insertado " . $num_registros_insertados ." registros";		 
		    			
		    		}
		    	}
		    	else
		    	{
		    		\Zend\Debug\Debug::dump("Archivo: " . $sourceFileName . ", no existe");
		    	}
		    	
    		}
    		
    		return new ViewModel(array(
    				'errorMessages' => $errorMessages
    		));
    	
    	}

    	return $this->redirect()->toRoute('ingresar');
    }
    
    public function getFileUploadLocation()
    {
    	$config = $this->getServiceLocator()->get('config');
    	return $config['module_config']['upload_location'];
    }
}

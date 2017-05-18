<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Admin\Entity\Docente;

class DocenteTable extends AbstractTableGateway
{
	protected $table = 'docente';
    
	public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerDocentes()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_docente')
	    		->columns(array('*'))
	    		->order('codDocente ASC');

    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }
    
    public function obtenerDocentesPagination(Select $select = null)
    {
        if (null === $select)
        {
            $select = new Select();
        }
        
        $select->from('vw_docente')
		        ->columns(array('*'))
		        ->order('codDocente ASC');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function obtenerDocente($codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();    	

		$select->from('vw_docente')
        	->columns(array('*'))
			->where(array('codDocente' => $codDocente));
        
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute();
		     
    	return $resultSet->current();
    }
    
    public function obtenerDocentePorCodUsuario($codUsuario)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_docente')
    	->columns(array('*'))
    	->where(array('codUsuario' => $codUsuario));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet->current();
    }
    
    //Array Asociativo para utilizar en los despegables
    public function obtenerDocentesArray()
    {
    	$docentes = array();
    
    	foreach ($this->obtenerDocentes() as $row)
    	{
    		$docentes[$row['codDocente']] = $row['nombres'] . ' ' . $row['primerApellido'] . ' ' . $row['segundoApellido'];
    	}
    
    	return $docentes;
    }
    
    public function insertar(Docente $docente)
    {
        $data = array(
                'modalidad' 		=> $docente->getModalidad(),
                'gradoAcademico' 	=> $docente->getGradoAcademico(),
                'profesion' 		=> $docente->getProfesion(),
                'codUsuario' 		=> null, /*Por defecto se crea un docente son asignarle usuario*/
                'codPersona' 		=> $docente->getPersona()->getCodPersona(),
                'estado' 			=> '1'
        );
        
        try{
            $this->insert($data);
            return $this->lastInsertValue;
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }
    }

    public function actualizar(Docente $docente)
    {
        $data = array(
                'modalidad' 		=> $docente->getModalidad(),
                'gradoAcademico' 	=> $docente->getGradoAcademico(),
                'profesion' 		=> $docente->getProfesion(),
                'codUsuario' 		=> $docente->getUsuario()->getCodUsuario(),
                'codPersona' 		=> $docente->getPersona()->getCodPersona(),
                'estado' 			=> '1'
        );
        
        try{
            $id = (int)  $docente->getCodDocente();        
            $this->update($data, array('codDocente' =>$id));
            return true;
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }  

        return false;
    }

    public function eliminar($codDocente)
    {
        $data = array('estado' => '0');

        try{
            $id = (int) $codDocente;
            return $this->update($data,array('codDocente' => $id));
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }
    }
    
    //Método especial de inserción de carga masiva
    public function insertarCM(Array $data)
    {
    	try{
    		$this->insert($data);
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}
    	return true;
    }

}

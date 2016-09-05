<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\Estudiante;
use Zend\Db\Sql\Select;


class EstudianteTable extends AbstractTableGateway
{
	
	protected $table = 'estudiante';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerEstudiantes()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('e' => 'vw_estudiante'))
    			->columns(array('*'))
    			->order('e.codEstudiante ASC');
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }

    public function obtenerEstudiantePagination(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from('vw_estudiante');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function obtenerEstudiante($codEstudiante)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('e' => 'vw_estudiante'))
    			->columns(array('*'))
    			->where(array('e.codEstudiante' => $codEstudiante));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
		$select->from(array('e' => 'vw_estudiante'))
                  ->columns(array('*'))
                  ->where(new \Zend\Db\Sql\Predicate\Like('e.primerApellido', "%" . $texto . "%"));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function insertar(Estudiante $estudiante)
    {
    	$data = array(
    			'anioIngreso' => $estudiante->getAnioIngreso(),
    			'codCarreraProfesional' => $estudiante->getCodCarreraProfesional(),
    			'codPersona' => $estudiante->getPersona()->getCodPersona(),
    			'estado' => '1'
    	);
    	
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    }

    public function actualizar(Estudiante $estudiante)
    {
    	$data = array(
    			'anioIngreso' => $estudiante->getAnioIngreso(),
    			'codCarreraProfesional' => $estudiante->getCodCarreraProfesional(),
    			'codPersona' => $estudiante->getPersona()->getCodPersona(),
    			'estado' => '1'
    	);
    	
        try{
            $id = (int) $estudiante->getCodEstudiante();  //cambie $id = (int)$id;      
            $this->update($data, array('codEstudiante' => $id)); //cambié  $this->update($data, array('codEstudiante' =>$estudiante->getCodEstudiante()));
            return true;
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }  

        return false;
    }

    public function eliminar($codEstudiante,$estado)
    {
        $data = array('estado' => $estado);

        try{
            $id = (int) $codEstudiante;
            return $this->update($data,array('codEstudiante' => $id));
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

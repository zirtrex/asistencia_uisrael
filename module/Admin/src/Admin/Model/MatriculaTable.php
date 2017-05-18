<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class MatriculaTable extends AbstractTableGateway
{
	protected $table = 'matricula';
	
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo todos los alumnos matriculados
    public function obtenerEstudiantesMatriculados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();    	

		$select->from(array('m' => 'vw_matricula'))
        	->columns(array('*'))
        	->where(array('m.codCargaAcademica' => $codCargaAcademica))
	    	->where(array('m.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('m.codCurso' => $codCurso))
	    	->where(array('m.codModalidad' => $codModalidad))
	    	->where(array('m.paralelo' => $paralelo))
			->order('m.primerApellido ASC');
        
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute();
		     
    	return $resultSet;
    } 

    public function obtenerEstudianteMatriculado($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codEstudiante)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('m' => 'vw_matricula'))
                ->columns(array('*'))
                ->where(array('m.codCargaAcademica' => $codCargaAcademica))
                ->where(array('m.codCicloAcademico' => $codCicloAcademico))
                ->where(array('m.codCurso' => $codCurso))
                ->where(array('m.codModalidad' => $codModalidad))
                ->where(array('m.paralelo' => $paralelo))
                ->where(array('m.codEstudiante' => $codEstudiante));
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet->current();
    }

    public function obtenerRefEstudianteMatriculado($keysMatricula)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('m' => 'vw_matricula'))
                ->columns(array('*'))
                ->where($keysMatricula);
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet->current();
    }

    public function insertar(Array $data)
    {
    	try{
    		$this->insert($data);
    		return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}
    	
    	return true;
    }
    
    public function eliminar(Array $where)
    {
    	try{
    		$this->delete($where);
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}    
    	return true;
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

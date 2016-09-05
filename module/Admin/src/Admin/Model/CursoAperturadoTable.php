<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class CursoAperturadoTable extends AbstractTableGateway
{
	
	protected $table = 'curso_aperturado';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerCursosAperturados()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ca' => 'vw_curso_aperturado'))
	    	->columns(array('*'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }
    
    public function obtenerCursoAperturado($codCicloAcademico, $codCurso)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ca' => 'vw_curso_aperturado'))
    		->columns(array('*'))
    		->where(array('ca.codCicloAcademico' => $codCicloAcademico, 'ca.codCurso' => $codCurso));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }

    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ca' => 'vw_curso_aperturado'))
	    	->columns(array('*'))
	    	->where(new \Zend\Db\Sql\Predicate\Like('ca.curso', "%" . $texto . "%"));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function insertar(Array $data)
    {
    	try{
    		$this->insert($data);;
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

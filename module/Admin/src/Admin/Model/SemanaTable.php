<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class SemanaTable extends AbstractTableGateway
{
	
	protected $table = 'semana';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerSemanas()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('s' => 'semana'))
    			->columns(array('*'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }
    
    //Array Asociativo para utilizar en los despegables
    public function obtenerSemanasArray()
    {
    	$semanas = array();
    	 
    	foreach ($this->obtenerSemanas() as $row)
    	{
    		$semanas[$row['codSemana']] = $row['semana'];
    	}
    
    	return $semanas;
    }
    
    public function insertar(Array $data)
    {
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
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

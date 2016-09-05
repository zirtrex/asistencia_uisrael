<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class SeccionTable extends AbstractTableGateway
{

    protected $table = 'seccion';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerSecciones()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('se' => 'seccion'))
    	->columns(array('*'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    public function obtenerSeccion($codSeccion)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('se' => 'seccion'))
    	->columns(array('*'))
    	->where(array('se.codSeccion' => $codSeccion));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    //Array Asociativo para utilizar en los despegables
    public function obtenerSeccionesArray()
    {
    	$secciones = array();
    
    	foreach ($this->obtenerSecciones() as $row)
    	{
    		$secciones[$row['codSeccion']] = $row['seccion'];
    	}
    
    	return $secciones;
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

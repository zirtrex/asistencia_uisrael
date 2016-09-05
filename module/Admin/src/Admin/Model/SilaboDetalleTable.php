<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;  


class SilaboDetalleTable extends AbstractTableGateway
{
	protected $table = 'silabo_detalle';
    
	public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo los temas por $codCurso
    public function obtenerTemas($codCurso)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sd' => 'vw_silabo_detalle'))
	    	->columns(array('*'))
	    	->where(array('sd.codCurso' => $codCurso));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }

    public function obtenerSilaboDetalle($codSilaboDetalle)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sd' => 'vw_silabo_detalle'))
	    	->columns(array('*'))
	    	->where(array('sd.codSilaboDetalle' => $codSilaboDetalle));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
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
    
    public function eliminar($codSilaboDetalle)
    {
    	try{
    		$this->delete(array('codSilaboDetalle' => $codSilaboDetalle));
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}
    	return true;
    }
    
}

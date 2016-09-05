<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class HorarioTable extends AbstractTableGateway
{

    protected $table = 'horario';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
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

    public function obtenerRefHorario($KeysHorario)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('m' => 'vw_matricula'))
                ->columns(array('*'))
                ->where($KeysHorario);
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet->current();
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

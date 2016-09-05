<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class ModalidadTable extends AbstractTableGateway
{

    protected $table = 'modalidad';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerModalidades()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('mo' => 'modalidad'))
            ->columns(array('codModalidad', 'modalidad'));
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet;
    }
    
    public function obtenerModalidad($codModalidad)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('mo' => 'modalidad'))
                ->columns(array('*'))
                ->where(array('codModalidad' => $codModalidad));
    
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
    
        return $resultSet->current();
    }

    //Array Asociativo para utilizar en los despegables
    public function obtenerModalidadesArray()
    {
        $modalidad = array();
        
        foreach ($this->obtenerModalidades() as $row)
        {
            $modalidad[$row['codModalidad']] = $row['modalidad'];
        }
    
        return $modalidad;
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

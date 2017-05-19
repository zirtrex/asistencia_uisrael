<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\Config;


class ConfigTable extends AbstractTableGateway
{
	
	protected $table = 'config';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerConfiguraciones()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('config')->columns(array('*'));    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function obtenerConfiguracion($idConfig)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('config')
            ->columns(array('*'))
            ->where(array('idConfig' => $idConfig));    
    
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
    
    public function actualizar(Config $config)
    {
        $data = array(
            'name'		=> $config->getName(),
            'value'	    => $config->getValue(),
        );
    
        try{
            $id = (int) $config->getIdConfig();
            return $this->update($data, array('idConfig' => $id));
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }
    
        return false;
    }
    
    public function eliminar($idConfig)
    {
        try{
            return $this->delete(array('idConfig' => $idConfig));
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
            return false;
        }
    }
}

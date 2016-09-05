<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\AreaConocimiento;
use Zend\Db\Sql\Select;


class AreaConocimientoTable extends AbstractTableGateway
{
	
	protected $table = 'area_conocimiento';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo todas las carreras profesionales 
    public function obtenerAreasConocimiento()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('area_conocimiento')
            ->columns(array('*'))
            ->where(array('estado' => '1'));
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet;
    }
	
    //Obtengo todas las carreras profesionales para la pagincación
    public function obtenerAreasConocimientoPagination(Select $select = null)
    {
        if (null === $select)
        {
        	$select = new Select();
        }           
        
        $select->from('area_conocimiento');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
	
    //Obtener area de conocimiento por codigo de area de conocimiento
    public function obtenerAreaConocimiento($codAreaConocimiento)
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('area_conocimiento')
                ->columns(array('codAreaConocimiento', 'areaConocimiento', 'estado'))
                ->where(array('codAreaConocimiento' => (int) $codAreaConocimiento));
    
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
    
        return $resultSet->current();
    }
    
    //Obtener carreras profesionales en Array Asociativo para utilizar en los despegables
    public function obtenerAreasConocimientoArray()
    {
    	$areasConocimiento = array();
    	
    	foreach ($this->obtenerAreasConocimiento() as $row)
    	{
    		$areasConocimiento[$row['codAreaConocimiento']] = $row['areaConocimiento'];
    	}
    
    	return $areasConocimiento;
    }

    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('area_conocimiento')
	    	->columns(array('*'))
	    	->where(new \Zend\Db\Sql\Predicate\Like('areaConocimiento', "%" . $texto . "%"));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }    
   
    public function insertar(AreaConocimiento $areaConocimiento)
    {
    	$data = array(
            'areaConocimiento'	=> $areaConocimiento->getAreaConocimiento(),
            'estado'			=> '1',
        );

    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function actualizar(AreaConocimiento $areaConocimiento)
    {
    	try{
	    	$id = (int) $areaConocimiento->getCodAreaConocimiento();

            $data = array(
                'areaConocimiento' 	=> $areaConocimiento->getAreaConocimiento()
            );

	    	$this->update($data, array('codAreaConocimiento' => $id));
	    	return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function eliminar($codAreaConocimiento)
    {
        $data = array('codAreaConocimiento' => (int) $codAreaConocimiento);

    	try
    	{
    		return $this->delete($data);
    	}
    	catch (\Exception $e)
    	{    		
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    }
    
    public function eliminarAjax($codAreaConocimiento, $estado)
    {
    	$data = array('estado' => $estado);
    
    	try{
    		$id = (int) $codAreaConocimiento;
    		
    		return $this->update($data, array('codAreaConocimiento' => $id));
    		
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

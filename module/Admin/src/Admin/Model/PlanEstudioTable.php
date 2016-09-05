<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\PlanEstudio;
use Zend\Db\Sql\Select;


class PlanEstudioTable extends AbstractTableGateway
{
	
	protected $table = 'plan_estudio';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerPlanesEstudio()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('pe' => 'vw_plan_estudio'))
    		->columns(array('*'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    public function obtenerPlanesEstudioPagination(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from('vw_plan_estudio');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function obtenerPlanEstudio($codPlanEstudio)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('pe' => 'vw_plan_estudio'))
    		->columns(array('*'))
    		->where(array('codPlanEstudio' => $codPlanEstudio));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }

    //Array Asociativo para utilizar en los despegables
    public function obtenerPlanesEstudioArray()
    {
    	$planesEstudio = array();
    	 
    	foreach ($this->obtenerPlanesEstudio() as $row)
    	{
    		$planesEstudio[$row['codPlanEstudio']] = $row['titulo'];
    	}
    	
    	return $planesEstudio;
    }
    
    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('pe' => 'vw_plan_estudio'))
	    	->columns(array('*'))
	    	->where(new \Zend\Db\Sql\Predicate\Like('pe.titulo', "%" . $texto . "%"));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function insertar(PlanEstudio $planEstudio)
    {
    	$data = array(
    			'titulo'				=> $planEstudio->getTitulo(),
    			'anioPlanEstudio'		=> $planEstudio->getAnioPlanEstudio(),
    			'numeroCiclos'			=> $planEstudio->getNumeroCiclos(),
    			'codCarreraProfesional'	=> $planEstudio->getCodCarreraProfesional(),
    			'estado'				=> '1'
    	);
    	
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function actualizar(PlanEstudio $planEstudio)
    {
    	try{
    		$id = (int) $planEstudio->getCodPlanEstudio();
    
    		$data = array(
    			'titulo'				=> $planEstudio->getTitulo(),
    			'anioPlanEstudio'		=> $planEstudio->getAnioPlanEstudio(),
    			'numeroCiclos'			=> $planEstudio->getNumeroCiclos(),
    			'codCarreraProfesional'	=> $planEstudio->getCodCarreraProfesional(),
    			'estado' 				=> $planEstudio->getEstado(),
    		);
    
    		$this->update($data, array('codPlanEstudio' => $id));
    		return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function eliminar($codPlanEstudio)
    {
    	try{
    		$id = (int) $codPlanEstudio;
    		return $this->delete(array('codPlanEstudio' => $id));
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

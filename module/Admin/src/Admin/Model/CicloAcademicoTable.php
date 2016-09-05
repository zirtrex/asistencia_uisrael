<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
//use Zend\Db\Sql\Predicate\Like;
use Admin\Entity\CicloAcademico;
use Zend\Db\Sql\Select;



class CicloAcademicoTable extends AbstractTableGateway
{
	
	protected $table = 'ciclo_academico';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerCiclosAcademicos()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ciac' => 'ciclo_academico'))
	    	->columns(array('*'))
    		->where(array('ciac.estado' => '1'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }
    public function obtenerCiclosAcademicosPagination(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from('ciclo_academico');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function obtenerCicloAcademico($codCicloAcademico)
    {
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ciac' => 'ciclo_academico'))
	    	->columns(array('*'))
	    	->where(array('ciac.codCicloAcademico' => $codCicloAcademico, 'ciac.estado' => '1'));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    //Array asociativo para utilizar en los despegables
    public function obtenerCiclosAcademicosArray()
    {
    	$ciclosAcademicos = array();
    	 
    	foreach ($this->obtenerCiclosAcademicos() as $row)
    	{
    		$ciclosAcademicos[$row['codCicloAcademico']] = $row['anio'] . " - " . $row['semestre'];
    	}
    
    	return $ciclosAcademicos;
    }
    
    public function buscar($texto = "")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ciac' => 'ciclo_academico'))
	    	->columns(array('*'))
	    	->where('(ciac.semestre LIKE "%'. $texto .'%" OR ciac .anio LIKE "%' .$texto. '%")')
	    	//->where(array(new Like('ciac.semestre', "%" . $texto . "%"), new Like('ciac.anio', "%" . $texto . "%")), PredicateSet::OP_OR)
    		->where(array('ciac.estado' => '1'));   	
    	
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
	public function insertar(CicloAcademico $cicloAcademico)
    {
    	$data = array(
            'codCicloAcademico'	=> $cicloAcademico->getCodCicloAcademico(),
            'anio'				=> $cicloAcademico->getAnio(),
            'semestre'			=> $cicloAcademico->getSemestre(),
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
    
    public function actualizar(CicloAcademico $cicloAcademico)
    {
    	try{
    		$id = (int) $cicloAcademico->getCodCicloAcademico();
    
    		$data = array(
    				'anio'		=>$cicloAcademico->getAnio(),
    				'semestre'	=>$cicloAcademico->getSemestre(),
    		);
    
    		$this->update($data, array('codCicloAcademico' => $id));
    		return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function eliminar($codCicloAcademico,$estado)
    {
    	$data = array('estado' => $estado);
    
    	try{
    		$id = (int) $codCicloAcademico;
    		return $this->update($data,array('codCicloAcademico' => $id));
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

<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\Curso;
use Zend\Db\Sql\Select;

class CursoTable extends AbstractTableGateway
{
	
	protected $table = 'curso';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerCursos()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('c' => 'vw_curso'))
    		->columns(array('*'))
    		->order('c.codCurso ASC');
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function obtenerCursosPagination(Select $select = null)
    {
    	if (null === $select)
            $select = new Select();
    	
    	$select->from('vw_curso');
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }
    
	public function obtenerCurso($codCurso)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();    	

		$select->from(array('c' => 'vw_curso'))
        	->columns(array('*'))
			->where(array('c.codCurso' => $codCurso));
        
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute();
		     
    	return $resultSet->current();
    }
    
    //Obtener carreras profesionales en Array Asociativo para utilizar en los despegables
    public function obtenerCursosArray()
    {
    	$cursos = array();
    	 
    	foreach ($this->obtenerCursos() as $row)
    	{
    		$cursos[$row['codCurso']] = $row['curso'];
    	}
    
    	return $cursos;
    }
    
    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('c' => 'vw_curso'))
	    	->columns(array('*'))
	    	->where(new \Zend\Db\Sql\Predicate\Like('c.curso', "%" . $texto . "%"));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function insertar(Curso $curso)
    {
    	$data = array(
    			'codigo' 			=> $curso->getCodigo(),
    			'curso' 			=> $curso->getCurso(),
    			'nivel' 			=> $curso->getNivel(),
    			'abreviatura' 		=> $curso->getAbreviatura(),
    			'creditos' 			=> $curso->getCreditos(),
    			'codPlanEstudio' 	=> $curso->getCodPlanEstudio(),
    			'estado' 			=> '1'
    	);
    	
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function actualizar(Curso $curso)
    {
    	$id = (int) $curso->getCodCurso();
    	
    	$data = array(
    			'codigo' 			=> $curso->getCodigo(),
    			'curso' 			=> $curso->getCurso(),
    			'nivel' 			=> $curso->getNivel(),
    			'abreviatura' 		=> $curso->getAbreviatura(),
    			'creditos' 			=> $curso->getCreditos(),
    			'codPlanEstudio' 	=> $curso->getCodPlanEstudio(),
    	);
    	 
    	try{
    		$this->update($data, array('codCurso' => $id));
    		return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function eliminar($codCurso, $estado)
    {
    	$data = array('estado' => $estado);
    
    	try{
    		$id = (int) $codCurso;
    		return $this->update($data,array('codCurso' => $id));
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
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


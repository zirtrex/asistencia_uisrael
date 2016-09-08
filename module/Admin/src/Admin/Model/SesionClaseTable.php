<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;


class SesionClaseTable extends AbstractTableGateway
{
	
	protected $table = 'sesion_clase';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo todos las sesiones o clases que el docente a creado y no han sido cerradas correctamente
    public function obtenerSesionesAbiertas($codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_sesion_clase'))
    	->columns(array('*'))
    	->where(array('sc.codDocente' => $codDocente))
    	->where(array('sc.sesionTerminada' => "No"))
    	->order('sc.fecha DESC');
    	 
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    public function obtenerSesionAbierta($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_sesion_clase'))
	    	->columns(array('*'))
	    	->where(array('sc.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('sc.codCurso' => $codCurso))
	    	->where(array('sc.codModalidad' => $codModalidad))
	    	->where(array('sc.paralelo' => $paralelo))
	    	->where(array('sc.codAula' => $codAula))
	    	->where(array('sc.codSeccion' => $codSeccion))
    		->where(array('sc.codDocente' => $codDocente))
	    	->where(array('sc.sesionTerminada' => "No"))
	    	->order('sc.fecha DESC');
    	
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    public function obtenerTotalTemas($codCurso)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_sesion_clase'))
	    	->columns(array('totalTemas'))
	    	->where(array('sc.codCurso' => $codCurso))
    		->order(array('sc.totalTemas' => 'DESC'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    
    public function obtenerTotalTemasTerminados($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_sesion_clase'))
    		->columns(array('temasTerminados' => new Expression('MAX(temasTerminados)')))
	    	->where(array('sc.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('sc.codCurso' => $codCurso))
	    	->where(array('sc.codModalidad' => $codModalidad))
	    	->where(array('sc.paralelo' => $paralelo))	    	
	    	->where(array('sc.codAula' => $codAula))
	    	->where(array('sc.codSeccion' => $codSeccion))
	    	->where(array('sc.codDocente' => $codDocente))
	    	->order(array('sc.temasTerminados' => 'DESC'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    
    public function obtenerCursosAvanzadosParaReporte($order = 'DESC', $limit = 10)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_sesion_clase');
	    	$select->columns(array('*', 'maxTemasTerminados' => new Expression('MAX(temasTerminados)'), 'avance' => new Expression('max(temasTerminados) * 100 / totalTemas')));
	    	$select->group(array('codCicloAcademico', 'codCurso', 'codModalidad', 'paralelo', 'codAula', 'codSeccion', 'codDocente'));
	    	$select->order(array('avance' => $order));
    		//$select->limit($limit);
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    
    public function obtenerNumeroAsistenciasDocenteParaReporte($codCarreraProfesional = null, $codDocente = null, $limit = 2)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_sesion_clase');
	    $select->columns(array('*', 'asistencias' => new Expression('COUNT(codSesionClase)')));
	    
    	if($codCarreraProfesional !== null){ $select->where(array('codCarreraProfesional' => $codCarreraProfesional)); }
    	if($codDocente !== null){ $select->where(array('codDocente' => $codDocente)); }
    	
	    $select->group(array('codCicloAcademico', 'codCurso', 'codModalidad', 'paralelo', 'codAula', 'codSeccion', 'codDocente'));
    	//$select->limit($limit);
    
    	$resultset = $this->selectWith($select);
    	$resultset->buffer();
    	return $resultset;    	
    }
    
    
    public function obtenerRegistroAsistencia($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_horario_sesion_clase'))
	    	->columns(array('*'))
	    	->where(array('sc.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('sc.codCurso' => $codCurso))
	    	->where(array('sc.codModalidad' => $codModalidad))
	    	->where(array('sc.paralelo' => $paralelo))	    	
	    	->where(array('sc.codAula' => $codAula))
	    	->where(array('sc.codSeccion' => $codSeccion))
    		->where(array('sc.codDocente' => $codDocente));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }

    public function obtenerSesionClase($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'vw_sesion_clase'))
	    	->columns(array('*'))
	    	->where(array('sc.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('sc.codCurso' => $codCurso))
	    	->where(array('sc.codModalidad' => $codModalidad))
	    	->where(array('sc.paralelo' => $paralelo))
    		->where(array('sc.codDocente' => $codDocente));
    	
    	if($codAula !== null){$select->where(array('sc.codAula' => $codAula));}
    	if($codSeccion !== null){$select->where(array('sc.codSeccion' => $codSeccion));}
	    	
	    $select->limit(1);
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    
    public function obtenerSesion($codSesionClase)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('sc' => 'sesion_clase'))
	    	->columns(array('*'))
	    	->where(array('sc.codSesionClase' => $codSesionClase));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }

    
    public function nuevaSesion(Array $data)
    {    
    	$codSesionClase = (int) $data['codSesionClase'];
    
    	if ($codSesionClase == NULL) {
    		try{
    			$this->insert($data);
    			return $this->lastInsertValue;
    		}catch (\Exception $e){
    			throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		}
    	} else {
    		if ($this->ObtenerSesion($codSesionClase)) {
    			try{
    				$this->update($data, array('codSesionClase' => $codSesionClase));
    			}catch (\Exception $e){
    				throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    			}
    		} else {
    			throw new \Exception('La sesión no existe!');
    		}
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

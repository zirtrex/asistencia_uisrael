<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;


class AsistenciaEstudianteTable extends AbstractTableGateway
{
    
	protected $table = 'asistencia_estudiante';
	
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo todos los estudiantes que han asistido a las clases filtrandolos por curso
    /*public function obtenerAsistenciaEstudiantesPorCurso($codCurso)
    {
    	 
    	$select = new Select();
    	 
    	$select->from('vw_asistencia_estudiante')
	    	->columns(array('*', 'numeroClases' => New Expression('COUNT(estadoAsistenciaEstudiante)')))
	    	->where(array('codCurso' => $codCurso))
	    	->group('codEstudiante');
    	 
    	$resultset = $this->selectWith($select);
    	$resultset->buffer();
    	 
    	return $resultset;
    }*/
    
    //Obtengo todos los estudiantes que han asistido a las clases a traves del parametro proporcionado
    public function obtenerAsistenciaEstudiantes($esComun = null, $codAreaConocimiento = null, $codCarreraProfesional = null, $codCicloAcademico = null, $codCurso = null, $codModalidad = null, $paralelo = null, $codDocente = null, $limit = 2)
    {    	
    	$select = new Select();
    	
    	$select->from('vw_asistencia_estudiante')
    		->columns(array('*', 'numeroClases' => New Expression('COUNT(estadoAsistenciaEstudiante)')));
    	
    	if($esComun !== null){ $select->where(array('esComun' => $esComun)); }
    	if($codAreaConocimiento !== null){ $select->where(array('codAreaConocimiento' => $codAreaConocimiento)); }
    	if($codCarreraProfesional !== null){ $select->where(array('codCarreraProfesional' => $codCarreraProfesional)); }
    	if($codCicloAcademico !== null){ $select->where(array('codCicloAcademico' => $codCicloAcademico)); }
    	if($codDocente !== null){ $select->where(array('codDocente' => $codDocente)); }
    	if($codCurso !== null){ $select->where(array('codCurso' => $codCurso)); }
    	if($codModalidad !== null){ $select->where(array('codModalidad' => $codModalidad)); }
    	if($paralelo != null){ $select->where(array('paralelo' => $paralelo)); }
    	
		$select->group('codEstudiante');
		//$select->limit($limit);
    	
    	$resultset = $this->selectWith($select);
    	$resultset->buffer();
    	
    	return $resultset;
    }
    
    //Obtengo las cantidades de estado Asistencias, tardanzas y faltas
    public function obtenerEstudiantesEstado($codEstudiante, $codCicloAcademico, $codCurso, $paralelo, $codModalidad, $estado = "Puntual")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ae' => 'vw_asistencia_estudiante'))
	    	->columns(array('totalEstado' => New Expression('COUNT(estadoAsistenciaEstudiante)')))
	    	->where(array('ae.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('ae.codCurso' => $codCurso))
	    	->where(array('ae.paralelo' => $paralelo))
	    	->where(array('ae.codModalidad' => $codModalidad))
	    	->where(array('ae.codEstudiante' => $codEstudiante))
	    	->where(array('ae.estadoAsistenciaEstudiante' => $estado));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    //
	public function registrarAsistencia(Array $data)
    {
    	try{    		
    		$this->insert($data);      		
    		return $this->lastInsertValue;
    		
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
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

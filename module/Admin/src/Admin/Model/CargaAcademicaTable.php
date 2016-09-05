<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;


class CargaAcademicaTable extends AbstractTableGateway
{
    
	protected $table = 'carga_academica';
	
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerCargasAcademicas()
    {
    	$select = new Select();
    
    	$select->from('vw_carga_academica')
    			->columns(array('*'));
    
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	
    	return $resultSet;
    }
    
    public function obtenerCargasAcademicasPagination(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from('vw_carga_academica');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }
    
    public function obtenerCargaAcademica($codCicloAcademico, $codCurso, $codModalidad, $paralelo)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ca' => 'vw_carga_academica'))
	    	->columns(array('*'))
	    	->where(array('ca.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('ca.codCurso' => $codCurso))
	    	->where(array('ca.codModalidad' => $codModalidad))
	    	->where(array('ca.paralelo' => $paralelo));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    } 
    
    public function obtenerCurso($codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from(array('ca' => 'vw_carga_academica'))
	    	->columns(array('*'))
	    	->where(array('ca.codCicloAcademico' => $codCicloAcademico))
	    	->where(array('ca.codCurso' => $codCurso))
	    	->where(array('ca.codModalidad' => $codModalidad))
	    	->where(array('ca.paralelo' => $paralelo))
	    	->where(array('ca.codAula' => $codAula))
	    	->where(array('ca.codSeccion' => $codSeccion))
    		->where(array('ca.codDocente' => $codDocente)); 
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet->current();
    }

    //Obtengo todos los cursos que existen en la tabla carga_academica de los parámetros proporcionados (Usado para el primer y tercer reporte)
    public function obtenerCargaAcademicaParaReportes($esComun = null, $paralelo = null, $codAreaConocimiento = null, $codCarreraProfesional = null, $codCicloAcademico = null, $codCurso = null, $codModalidad = null, $codAula = null, $codSeccion = null, $codDocente = null, $orderBy = array('carreraProfesional' => 'ASC'))
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_carga_academica')->columns(array('*'));
    	
    	if($esComun !== null){ $select->where(array('esComun' => $esComun)); }
    	if($paralelo !== null){ $select->where(array('paralelo' => $paralelo)); }
    	if($codCarreraProfesional !== null){ $select->where(array('codCarreraProfesional' => $codCarreraProfesional)); }
    	if($codAreaConocimiento !== null){ $select->where(array('codAreaConocimiento' => $codAreaConocimiento)); }
    	if($codCicloAcademico !== null){ $select->where(array('codCicloAcademico' => $codCicloAcademico)); }
    	if($codCurso !== null){ $select->where(array('codCurso' => $codCurso)); }
    	if($codModalidad !== null){ $select->where(array('codModalidad' => $codModalidad)); }
    	if($codAula !== null){ $select->where(array('codAula' => $codAula)); }
    	if($codSeccion !== null){ $select->where(array('codSeccion' => $codSeccion)); }
    	if($codDocente !== null){ $select->where(array('codDocente' => $codDocente)); }
    	
    	if($orderBy !== null){ $select->order($orderBy); }
    	 
    	$resultset = $this->selectWith($select);
    	$resultset->buffer();
    	 
    	return $resultset;
    }
    
    //Obtengo todos los cursos que tiene asignado el docente para el módulo de Asistencia
    public function obtenerCursos($codDocente)
    {
    	return  $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    }
    
    public function obtenerAreasConocimientoArray($codDocente = null)
    {
    	$cursos = array();
    	 
    	if($codDocente !== null) {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codAreaConocimiento']] = $row['areaConocimiento'];
    	}
    
    	return $cursos;
    }
   
    public function obtenerCarrerasProfesionalesArray($codDocente = null)
    {
    	$cursos = array();
    	
    	if($codDocente !== null) {    	
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codCarreraProfesional']] = $row['carreraProfesional'];
    	}
    
    	return $cursos;
    }
    
    public function obtenerCiclosAcademicosArray($codDocente = null)
    {
    	$cursos = array();
    	
    	if($codDocente !== null) {    	
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codCicloAcademico']] = $row['anio'] .' - '. $row['semestre'];
    	}
    
    	return $cursos;
    }
    
    public function obtenerCursosArray($codDocente = null)
    {
    	$cursos = array();
    	
    	if($codDocente !== null) {    	
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codCurso']] = $row['curso'];
    	}
    
    	return $cursos;
    }
    
    public function obtenerModalidadesArray($codDocente = null)
    {
    	$cursos = array();
    	
    	if($codDocente !== null) {    	
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codModalidad']] = $row['modalidad'];
    	}
    
    	return $cursos;
    }
    
    public function obtenerParalelosArray($codDocente = null)
    {
    	$cursos = array();
    	
    	if($codDocente !== null) {    	
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['paralelo']] = $row['paralelo'];
    	}
    
    	return $cursos;
    }
    
    public function obtenerDocentesArray($codDocente = null)
    {
    	$cursos = array();
    	 
    	if($codDocente !== null) {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, $codDocente, null);
    	}else {
    		$cargaAcademica = $this->obtenerCargaAcademicaParaReportes(null, null, null, null, null, null, null, null, null, null, null);
    	}
    
    	foreach ($cargaAcademica as $row)
    	{
    		$cursos[$row['codDocente']] = $row['nombres'] .' '. $row['primerApellido'] .' '. $row['segundoApellido'];
    	}
    
    	return $cursos;
    }
    
    public function insertar(Array $data)
    {
    	try{
    		$this->insert($data);  
    		return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}
    	
    	return true;
    }
    
    public function actualizar(Array $data, Array $where)
    {
    	try{ 
    		$this->update($data, $where);
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    		return false;
    	}
    	 
    	return true;
    }

    public function eliminar($codCicloAcademico, $codCurso, $codModalidad, $paralelo)
    {
    	$where = array(
    			'codCicloAcademico' => (int) $codCicloAcademico,
    			'codCurso' 			=> (int) $codCurso,
    			'codModalidad' 		=> (int) $codModalidad,
    			'paralelo' 			=> $paralelo
    	);
    	
    	try
    	{       
        	return $this->delete($where);
    	}
        catch (\Exception $e)
        {
        	throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }
    }
    
    //Método especial de inserción de carga masiva(CM)
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



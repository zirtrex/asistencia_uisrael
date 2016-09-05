<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\CarreraProfesional;
use Zend\Db\Sql\Select;


class CarreraProfesionalTable extends AbstractTableGateway
{
	
	protected $table = 'carrera_profesional';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo todas las carreras profesionales 
    public function obtenerCarrerasProfesionales()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('vw_carrera_profesional')
            ->columns(array('*'));
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
         
        return $resultSet;
    }
	
    //Obtengo todas las carreras profesionales para la pagincación
    public function obtenerCarrerasProfesionalesPagination(Select $select = null)
    {
        if (null === $select)
        {
        	$select = new Select();
        }           
        
        $select->from('vw_carrera_profesional');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
	
    //Obtener carrera profesional por codigo de carrera profesional
    public function obtenerCarreraProfesional($codCarreraProfesional)
    {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('carrera_profesional')
                ->columns(array('codCarreraProfesional', 'codigo', 'carreraProfesional', 'codAreaConocimiento', 'estado'))
                ->where(array('codCarreraProfesional' => (int) $codCarreraProfesional));
    
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
    
        return $resultSet->current();
    }
    
    //Obtener carreras profesionales por codigo de area del conocimiento
    public function obtenerCarrerasProfesionalesPorCodAreaConocimiento($codAreaConocimiento)
    {
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('carrera_profesional')
    	->columns(array('codCarreraProfesional', 'codigo', 'carreraProfesional', 'codAreaConocimiento', 'estado'))
    	->where(array('codAreaConocimiento' => (int) $codAreaConocimiento));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }
    
    //Obtener carreras profesionales en Array Asociativo para utilizar en los despegables
    public function obtenerCarrerasProfesionalesArray()
    {
    	$carrerasProfesionales = array();
    	
    	foreach ($this->obtenerCarrerasProfesionales() as $row)
    	{
    		$carrerasProfesionales[$row['codCarreraProfesional']] = $row['carreraProfesional'];
    	}
    
    	return $carrerasProfesionales;
    }
    
    //Obtener carreras profesionales en Array Asociativo para utilizar en los despegables
    public function obtenerCarrerasProfesionalesPorCodAreaConocimientoArray($codAreaConocimiento)
    {
    	$carrerasProfesionales = array();
    	 
    	foreach ($this->obtenerCarrerasProfesionalesPorCodAreaConocimiento($codAreaConocimiento) as $row)
    	{
    		$carrerasProfesionales[$row['codCarreraProfesional']] = $row['carreraProfesional'];
    	}
    
    	return $carrerasProfesionales;
    }

    public function buscar($texto = "%")
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('vw_carrera_profesional')
	    	->columns(array('*'))
	    	->where(new \Zend\Db\Sql\Predicate\Like('carreraProfesional', "%" . $texto . "%"));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet;
    }    
   
    public function insertar(CarreraProfesional $carreraProfesional)
    {
    	$data = array(
            'codigo'				=> $carreraProfesional->getCodigo(),
            'carreraProfesional'	=> $carreraProfesional->getCarreraProfesional(),
    		'codAreaConocimiento'	=> $carreraProfesional->getCodAreaConocimiento(),
            'estado'				=> '1',
        );

    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function actualizar(CarreraProfesional $carreraProfesional)
    {
    	try{
	    	$id = (int) $carreraProfesional->getCodCarreraProfesional();

            $data = array(
                'codigo'				=> $carreraProfesional->getCodigo(),
                'carreraProfesional'	=> $carreraProfesional->getCarreraProfesional(),
            	'codAreaConocimiento'	=> $carreraProfesional->getCodAreaConocimiento(),
                'estado'				=> '1',
            );

	    	$this->update($data, array('codCarreraProfesional' => $id));
	    	return true;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    	return false;
    }
    
    public function eliminar($codCarreraProfesional)
    {
        $data = array('codCarreraProfesional' => (int) $codCarreraProfesional);

    	try
    	{
    		return $this->delete($data);
    	}
    	catch (\Exception $e)
    	{    		
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    }
    
    public function eliminarAjax($codCarreraProfesional,$estado)
    {
    	$data = array('estado' => $estado);
    
    	try{
    		$id = (int) $codCarreraProfesional;
    		return $this->update($data,array('codCarreraProfesional' => $id));
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

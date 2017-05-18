<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;


class AvanceSilaboTable extends AbstractTableGateway
{
    
    protected $table = 'avance_silabo';
	
	public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    //Obtengo los temas avanzados más no terminados
    public function obtenerTemasAvanzados($codCargaAcademica, $codCicloAcademico, $codCurso, $codModalidad, $paralelo, $codAula, $codSeccion, $codDocente, $terminados = false)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();    	

		if($terminados)
		{		
			$select->from(array('avsi' => 'vw_avance_silabo'))
	        	->columns(array('*'))        	
	        	->where(array('avsi.codCargaAcademica' => $codCargaAcademica))
		    	->where(array('avsi.codCicloAcademico' => $codCicloAcademico))
		    	->where(array('avsi.codCurso' => $codCurso))
		    	->where(array('avsi.codModalidad' => $codModalidad))
		    	->where(array('avsi.paralelo' => $paralelo))
		    	->where(array('avsi.codAula' => $codAula))
		    	->where(array('avsi.codSeccion' => $codSeccion))
		    	->where(array('avsi.codDocente' => $codDocente))
				->where(array('avsi.avance' => "Terminado"));
		}
		else
		{
			$select->from(array('avsi' => 'vw_avance_silabo'))
				->columns(array('*'))
				->where(array('avsi.codCargaAcademica' => $codCargaAcademica))
		    	->where(array('avsi.codCicloAcademico' => $codCicloAcademico))
		    	->where(array('avsi.codCurso' => $codCurso))
		    	->where(array('avsi.codModalidad' => $codModalidad))
		    	->where(array('avsi.paralelo' => $paralelo))
		    	->where(array('avsi.codAula' => $codAula))
		    	->where(array('avsi.codSeccion' => $codSeccion))
		    	->where(array('avsi.codDocente' => $codDocente));
		}
        
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute();
		     
    	return $resultSet;
    } 

    public function registrarAvanceTema(Array $data)
    {
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
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

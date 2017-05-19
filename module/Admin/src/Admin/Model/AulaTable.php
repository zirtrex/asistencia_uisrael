<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\Aula;
use Zend\Db\Sql\Select;

class AulaTable extends AbstractTableGateway
{

    protected $table = 'aula';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
    public function obtenerAulas()
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('aula')
    		->columns(array('*'))
            ->where(array('estado' => '1'));
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	 
    	return $resultSet;
    }
    
    public function obtenerAulasPagination(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from('aula')
        	->columns(array('*'))
        	->where(array('estado' => '1'));
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function obtenerAula($codAula)
    {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    
    	$select->from('aula')
	    	->columns(array('*'))
	    	->where(array('codAula' => $codAula));
    
    
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    
    	return $resultSet->current();
    }
    
    public function buscar($texto = "%")
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from('aula')
                  ->columns(array('*'))
                  ->where(new \Zend\Db\Sql\Predicate\Like('numero', "%" . $texto . "%"));
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
    
        return $resultSet;
    }

    //Array Asociativo para utilizar en los despegables
    public function obtenerAulasArray()
    {
    	$aulas = array();
    
    	foreach ($this->obtenerAulas() as $row)
    	{
    		$aulas[$row['codAula']] = $row['numero'];
    	}
    
    	return $aulas;
    }
    
    public function insertar(Aula $aula)
    {
        $data = array(
              'numero'		=> $aula->getNumero(),
              'piso'		=> $aula->getPiso(),
              'capacidad'	=> $aula->getCapacidad(),
              'estado'		=> '1'
            );

    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    }
    
    public function actualizar(Aula $aula)
    {
        $data = array(
              'numero'		=>$aula->getNumero(),
              'piso'		=>$aula->getPiso(),
              'capacidad'	=>$aula->getCapacidad(),
            );

        try{
            $id = (int) $aula->getCodAula();
            $this->update($data, array('codAula' => $id));
            return true;
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }  

        return false;
    }

    public function eliminar($codAula,$estado)
    {
        $data = array('estado' => $estado);

        try{
            $id = (int) $codAula;
            $this->update($data, array('codAula' => $id));
            return true;
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
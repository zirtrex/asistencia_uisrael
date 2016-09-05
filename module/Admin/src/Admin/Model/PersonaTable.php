<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Admin\Entity\Persona;


class PersonaTable extends AbstractTableGateway
{
	
	protected $table = 'persona';
    
    public function __construct($sm)
    {
    	$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
    	$this->initialize();
    }
    
   	//cargamos la persona
   	public function obtenerPersona($codPersona)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
    
        $select->from(array('ad' => 'vw_persona'))
                ->columns(array('*'))
                ->where(array('ad.codPersona' => $codPersona));
    
    
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
    
        return $resultSet->current();
    }


   	public function insertar(Persona $persona)
    {
        $data = array(
               'nombres'=>$persona->getNombres(),
               'primerApellido'=>$persona->getprimerApellido(),
               'segundoApellido'=>$persona->getsegundoApellido(),
               'tipoDocumento'=>$persona->getTipoDocumento(),
               'numeroDocumento'=>$persona->getNumeroDocumento(),
               'fechaNacimiento'=>$persona->getfechaNacimiento(),
               'correo'=>$persona->getCorreo(),
               'celular'=>$persona->getCelular()
            );
    	try{
    		$this->insert($data);
    		return $this->lastInsertValue;
    	}catch (\Exception $e){
    		throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
    	}
    }

    public function actualizar(Persona $persona)
    {
        $data = array(
               'nombres'=>$persona->getNombres(),
               'primerApellido'=>$persona->getprimerApellido(),
               'segundoApellido'=>$persona->getsegundoApellido(),
               'tipoDocumento'=>$persona->getTipoDocumento(),
               'numeroDocumento'=>$persona->getNumeroDocumento(),
               'fechaNacimiento'=>$persona->getfechaNacimiento(),
               'correo'=>$persona->getCorreo(),
               'celular'=>$persona->getCelular()
            );

        try{
            $id = (int) $persona->getCodPersona();        
            $this->update($data, array('codPersona' => $id));
            return true;
        }catch (\Exception $e){
            throw new \Exception($e->getPrevious()->getMessage(),$e->getPrevious()->getCode(), $e->getPrevious()->getPrevious());
        }  

        return false;
    }

    public function actualizarImagen($persona)
    {
        $data = array(
               'imagen'=>$persona['imagen'],
        );

        try{
            $id = (int) $persona['codPersona'];        
            $this->update($data, array('codPersona' => $id));
            return true;
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

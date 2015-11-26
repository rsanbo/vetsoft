<?php

/**
 * Project:     Framework G - G Light
 * File:        db.php
 * 
 * For questions, help, comments, discussion, etc., please join to the
 * website www.frameworkg.com
 * 
 * @link http://www.frameworkg.com/
 * @copyright 2013-02-07
 * @author Group Framework G  <info at frameworkg dot com>
 * @version 1.2
 */

class db
{
    var $server = C_DB_SERVER; //DB server
	var $user = C_DB_USER; //DB user
    var $pass = C_DB_PASS; //DB password
	var $db = C_DB_DATABASE_NAME; //DB database name
	var $limit = C_DB_LIMIT; //DB limit of elements by page
	var $cn;
	var $numpages;
	
	public function db(){}
	
	//connect to database
	public function connect()
	{
		$this->cn = mysqli_connect($this->server, $this->user, $this->pass);
		if(!$this->cn) {die("Failed connection to the database: ".mysqli_error($this->cn));}
		if(!mysqli_select_db($this->cn,$this->db)) {die("Unable to communicate with the database $db: ".mysqli_error($this->cn));}
		mysqli_query($this->cn,"SET NAMES utf8");
	}
	
	//function for doing multiple queries
	public function do_operation($operation, $class = NULL)
	{
		$result = mysqli_query($this->cn, $operation) ;
		if(!$result) {$this->throw_sql_exception($class);}	
	}
	
	//function for obtain data from db in object form
	private function get_data($operation)
	{		
		$data = array(); 
		$result = mysqli_query($this->cn, $operation) or die(mysqli_error($this->cn));
		while ($row = mysqli_fetch_object($result))
		{
			array_push($data, $row);
		}
		return $data;
	}
	
	//throw exception to web document
	private function throw_sql_exception($class)
    {
		$errno = mysqli_errno($this->cn); $error = mysqli_error($this->cn);
		$msg = $error."<br /><br /><b>Error number:</b> ".$errno;
        throw new Exception($msg);
    }
	
	//for avoid sql injections, this functions cleans the variables
	private function escape_string(&$data)
	{
		if(is_object($data))
		{
			foreach ($data->metadata() as $key => $attribute)
			{if(!is_empty($data->get($key))){$data->set($key,mysqli_real_escape_string($this->cn,$data->get($key)));}}
		}
		else if(is_array($data))
		{
			foreach ($data as $key => $value) 
			{if(!is_array($value)){$data[$key]=mysqli_real_escape_string($this->cn,$value);}}
		}
	}
	
	//function for add data to db
	public function insert($options,$object) 
	{
		switch($options['lvl1'])
		{																																																																																													
			case "tratamiento":
				switch($options['lvl2']){
				
				case "normal":
					$titulo=mysqli_real_escape_string($this->cn,$object->get('titulo'));
					$descripcion=mysqli_real_escape_string($this->cn,$object->get('descripcion'));
					$fecha=mysqli_real_escape_string($this->cn,$object->get('fecha'));
					$hora=mysqli_real_escape_string($this->cn, $object->get('hora'));
					$lugar=mysqli_real_escape_string($this->cn,$object->get('lugar'));
					$estado=mysqli_real_escape_string($this->cn,$object->get('estado'));
					$animal=mysqli_real_escape_string($this->cn,$object->get('animal'));
					$veterinario=mysqli_real_escape_string($this->cn,$object->get('veterinario'));
					$this->do_operation("INSERT INTO tratamiento (codigo, titulo, descripcion, fecha, hora, duracion, lugar, estado, resultado, animal, veterinario) VALUES (NULL, '$titulo', '$descripcion', '$fecha', '$hora', NULL, '$lugar', '$estado', NULL, '$animal', '$veterinario');");
					break;
			
			}
			break;

			case "cita":
				switch($options['lvl2']){
				
				case "normal":
					$motivo=mysqli_real_escape_string($this->cn,$object->get('motivo'));
					$fecha=mysqli_real_escape_string($this->cn,$object->get('fecha'));
					$hora=mysqli_real_escape_string($this->cn, $object->get('hora'));
					$lugar=mysqli_real_escape_string($this->cn,$object->get('lugar'));
					$estado=mysqli_real_escape_string($this->cn,$object->get('estado'));
					$animal=mysqli_real_escape_string($this->cn,$object->get('animal'));
					$veterinario=mysqli_real_escape_string($this->cn,$object->get('veterinario'));
					$this->do_operation("INSERT INTO cita (codigo, motivo, fecha, hora, lugar, condicion, estado, diagnostico, animal, veterinario) VALUES (NULL, '$motivo', '$fecha', '$hora', '$lugar', NULL, '$estado', NULL, '$animal', '$veterinario');");
					break;
			}
			break;

			case "uso_de_producto":
			switch($options['lvl2'])
			{
				
				case "desde_cita":
					$cantidad=mysqli_real_escape_string($this->cn,$object->get('cantidad'));
					$producto=mysqli_real_escape_string($this->cn,$object->get('producto'));
					$cita=mysqli_real_escape_string($this->cn, $object->get('cita'));
					$this->do_operation("INSERT INTO uso_de_producto (id, cantidad, producto, cita, tratamiento) VALUES (NULL, '$cantidad', '$producto', '$cita', NULL);");
					break;
				
				case "desde_tratamiento":
					$cantidad=mysqli_real_escape_string($this->cn,$object->get('cantidad'));
					$producto=mysqli_real_escape_string($this->cn,$object->get('producto'));
					$tratamiento=mysqli_real_escape_string($this->cn, $object->get('tratamiento'));
					$this->do_operation("INSERT INTO uso_de_producto (id, cantidad, producto, cita, tratamiento) VALUES (NULL, '$cantidad', '$producto', NULL, '$tratamiento');");
					break;
			}	
			break;
			
			default: break;
		}
	}
	
	//function for edit data from db
	public function update($options,$object) 
	{
		switch($options['lvl1'])
		{																																																																																																		
			case "cita":
			switch($options['lvl2'])
			{
				case "normal_atender":
					$codigo=mysqli_real_escape_string($this->cn,$object->get('codigo'));
					$condicion=mysqli_real_escape_string($this->cn,$object->get('condicion'));
					$diagnostico=mysqli_real_escape_string($this->cn,$object->get('diagnostico'));
					$estado=mysqli_real_escape_string($this->cn,$object->get('estado'));
					$this->do_operation("UPDATE cita SET condicion = '$condicion', diagnostico = '$diagnostico', estado = '$estado' WHERE codigo = '$codigo';");
					break;

				case "normal":
					$codigo=mysqli_real_escape_string($this->cn,$object->get('codigo'));
                    $motivo=mysqli_real_escape_string($this->cn,$object->get('motivo'));
                    $fecha=mysqli_real_escape_string($this->cn, $object->get('fecha'));
                    $hora=mysqli_real_escape_string($this->cn,$object->get('hora'));
                    $lugar=mysqli_real_escape_string($this->cn,$object->get('lugar'));
                    $this->do_operation("UPDATE cita SET motivo ='$motivo',hora= '$hora', lugar='$lugar', fecha='$fecha'  WHERE codigo='$codigo';");
					break;
			}
			break;

			case "tratamiento":
			switch($options['lvl2'])
			{
				case "normal":
					$codigo=mysqli_real_escape_string($this->cn,$object->get('codigo'));
					$duracion=mysqli_real_escape_string($this->cn,$object->get('duracion'));
					$resultado=mysqli_real_escape_string($this->cn,$object->get('resultado'));
					$estado=mysqli_real_escape_string($this->cn,$object->get('estado'));
					$this->do_operation("UPDATE tratamiento SET duracion = '$duracion', resultado = '$resultado', estado = '$estado' WHERE codigo = '$codigo';");
					break;
			}
			break;

			case "producto":
			switch($options['lvl2'])
			{
				case "normal":
					$id=mysqli_real_escape_string($this->cn,$object->get('id'));
					$cantidad=mysqli_real_escape_string($this->cn,$object->get('cantidad'));
					$this->do_operation("UPDATE producto SET cantidad = '$cantidad' WHERE id = '$id';");
					break;
			}
			break;
			
			default: break;
		}
	}
	
	//function for delete data from db
	public function delete($options,$object)
	{
		switch($options['lvl1'])
		{																																																																																												
			case "cita":
			switch($options['lvl2'])
			{
				case "normal":
					$codigo=mysqli_real_escape_string($this->cn,$object->get('codigo'));
					$this->do_operation("DELETE FROM cita WHERE codigo = '$codigo';");
					break;
			}
			break;

			case "tratamiento":
			switch($options['lvl2'])
			{
				case "normal":
					$codigo=mysqli_real_escape_string($this->cn,$object->get('codigo'));
					$this->do_operation("DELETE FROM tratamiento WHERE codigo = '$codigo';");
					break;
			}
			break;
            
			default: break;			  
		}
	}
	
	//function that returns an array with data from a operation
	public function select($option,$data)
	{
		$info = array();
		switch($option['lvl1'])
		{																																																																																																									
			case "administrador":
			switch($option['lvl2'])
			{
				case "all": 
					//
				break;

				case "one_login":
					$user = mysqli_real_escape_string($this->cn, $data['user']);
					$pass = $data['pass'];
					$result = $this->get_data("SELECT user, pass FROM administrador WHERE user='$user';");
					$hasher = new PasswordHash(8, FALSE);
					if ($hasher->CheckPassword($pass, $result[0]->pass))
						$info = $this->get_data("SELECT * FROM administrador WHERE user = '$user';");
					unset($hasher);
					break;
			}
			break;

			case "veterinario":
			switch($option['lvl2'])
			{
				case "all": 
				$info=$this->get_data("SELECT * FROM veterinario;");
				break;


				case "one_login":
					$user = mysqli_real_escape_string($this->cn, $data['user']);
					$pass = $data['pass'];
					$result = $this->get_data("SELECT user, pass FROM veterinario WHERE user='$user';");
					$hasher = new PasswordHash(8, FALSE);
					if ($hasher->CheckPassword($pass, $result[0]->pass))
						$info = $this->get_data("SELECT * FROM veterinario WHERE user = '$user';");
					unset($hasher);
					break;
			}
			break;

			case "cita":
			switch($option['lvl2'])
			{	

				case "all": 
					$info=$this->get_data("SELECT * FROM cita;");
					break;
					
				case "por_codigo": 
					$this->escape_string($data);
					$codigo=$data['codigo'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.codigo = '$codigo';"); 
					break;

				case "by_all":
					$this->escape_string($data);
					$identificacion=$data['identificacion']; 
                
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;
                
                case "all_2":
					$info=$this->get_data("SELECT * FROM cita;"); 
					break;
				
				case "by_codigo": 
					$this->escape_string($data);
					$codigo=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.codigo like '%$codigo%' AND c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;
                
                case "one":
                	$codigo = mysqli_real_escape_string($this->cn,$data['codigo']);
					$info=$this->get_data("SELECT * FROM cita WHERE codigo ='$codigo' ;"); 
					break;

				case "by_motivo": 
					$this->escape_string($data);
					$motivo=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.motivo like '%$motivo%' AND c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;

				case "by_fecha": 
					$this->escape_string($data);
					$fecha=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.fecha like '%$fecha%' AND c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;

				case "by_hora": 
					$this->escape_string($data);
					$hora=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE c.hora like '%$hora%' AND c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;

				case "by_animal": 
					$this->escape_string($data);
					$animal=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT c.*, a.nombre as nombre_animal FROM cita c, animal a WHERE a.nombre like '%$animal%' AND c.animal = a.id AND c.veterinario = '$identificacion';"); 
					break;

			}
			break;
                
			case "animal":
			switch($option['lvl2'])
			{
				case "all": 
					$info=$this->get_data("SELECT * FROM animal;"); 
					break;
				break;

				case "by_id": 
					$this->escape_string($data);
					$id=$data['valor'];
					$info=$this->get_data("SELECT * FROM animal WHERE id like '%$id%';"); 
					break;

				case "by_nombre": 
					$this->escape_string($data);
					$nombre=$data['valor'];
					$info=$this->get_data("SELECT * FROM animal WHERE nombre like '%$nombre%';"); 
					break;

				case "by_especie": 
					$this->escape_string($data);
					$especie=$data['valor'];
					$info=$this->get_data("SELECT * FROM animal WHERE especie like '%$especie%';"); 
					break;
					
				case "by_fecha": 
					$this->escape_string($data);
					$fecha=$data['valor'];
					$info=$this->get_data("SELECT * FROM animal WHERE fecha_de_nacimiento like '%$fecha%';"); 
					break;
			}
			break;

			case "tratamiento":
			switch($option['lvl2'])
			{
				case "all": 
					$info=$this->get_data("SELECT * FROM tratamiento;");
					break;

				case "por_codigo": 
					$this->escape_string($data);
					$codigo=$data['codigo'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.codigo = '$codigo';"); 
					break;
				
				case "by_all":
					$this->escape_string($data);
					$identificacion=$data['identificacion']; 
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
				
				
				case "by_codigo": 
					$this->escape_string($data);
					$codigo=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.codigo like '%$codigo%' AND t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
				
				case "by_titulo": 
					$this->escape_string($data);
					$titulo=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.titulo like '%$titulo%' AND t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
				
				case "by_fecha": 
					$this->escape_string($data);
					$fecha=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.fecha like '%$fecha%' AND t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
				
				case "by_hora": 
					$this->escape_string($data);
					$hora=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE t.hora like '%$hora%' AND t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
				
				case "by_animal": 
					$this->escape_string($data);
					$animal=$data['valor'];
					$identificacion=$data['identificacion'];
					$info=$this->get_data("SELECT t.*, a.nombre as nombre_animal FROM tratamiento t, animal a WHERE a.nombre like '%$animal%' AND t.animal = a.id AND t.veterinario = '$identificacion';"); 
					break;
			}
			break;

			case "producto":
			switch($option['lvl2'])
			{	
				case "por_id": 
					$this->escape_string($data);
					$id=$data['id'];
					$info=$this->get_data("SELECT * FROM producto WHERE id = '$id';"); 
					break;
				case "by_nombre": 
					$this->escape_string($data);
					$nombre=$data['nombre'];
					$tipo=$data['tipo'];
					$info=$this->get_data("SELECT * FROM producto WHERE nombre like '%$nombre%' AND tipo = '$tipo';"); 
					break;
				case "by_all":
					$this->escape_string($data);
					$tipo=$data['tipo']; 
					$info=$this->get_data("SELECT * FROM producto WHERE tipo = '$tipo';"); 
					break;
			}
			break;

>>>>>>> cu19
			default: break;
		}
		return $info;
	}
	
	//close the db connection
	public function close()
	{
		if($this->cn){mysqli_close($this->cn);}
	}
	
}

?>
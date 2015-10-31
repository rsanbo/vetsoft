<?php
 
	class veterinario extends object_standard{

		protected $identificacion;
		protected $nombre;
		protected $telefono;
		protected $email;
		protected $sueldo;
		protected $usuario;
		protected $contraseña;
		
		//components
		var $components = array();
		
		//auxiliars for primary key and for files
		var $auxiliars = array();
		
		//data about the attributes
		public function metadata(){
			return array("identificacion" => array(), "nombre" => array(), "telefono" => array(), "email" => array(), "sueldo" => array(), "usuario" => array(), "contraseña" => array()); 
		}

		public function primary_key(){
			return array("identificacion");
		}	

		public function relational_keys($class, $rel_name){
			switch ($class) {
				
				default:
				break;
			}
		}
	}

?>
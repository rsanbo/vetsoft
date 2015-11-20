<?php

require('configs/include.php');

class c_buscar_animal extends super_controller {

    public function buscar(){
        
        // function edad($fechanacimiento){
        //     list($ano,$mes,$dia) = explode("-",$fechanacimiento);
        //     $ano_diferencia  = date("Y") - $ano;
        //     if($mes<date("m")){
        //         $mes_diferencia  = date("m") - $mes;
                
        //     }elseif($mes==date("m")){
        //         $mes_diferencia  =1;
        //     }else{
        //         $mes_diferencia  = (12 - date("m")) + $mes;
        //     }
        //     return ($ano_diferencia*12)+$mes_diferencia;
        // }

        $id= $_POST['codigo'];
        if(is_empty($id)){
            $this->engine->assign('error1',1);
            $this->mensaje("warning","Error","","El campo de texto está vacío");
            throw_exception("");   
        }elseif(is_numeric($id)){
            $options['animal']['lvl2'] = "some";
            $cod['animal']['id'] = $id;
            $this->orm->connect();
            $this->orm->read_data(array("animal"), $options, $cod);
            $animales = $this->orm->get_objects("animal");
            $this->orm->close();
            if (is_empty($animales)){
                $this->engine->assign('error3',3);
                $this->mensaje("warning","Error","","Codigo no existe o no ha sido asignado");
                throw_exception("");
            }else{
                $this->engine->assign("animal",$animales);
            }
        }else{
            $this->engine->assign('error2',2);
            $this->mensaje("warning","Error","","Dato incorrecto");
            throw_exception("");
        }
    }
    
    
    public function display(){
        $this->engine->assign('title', "Buscar Animal");
        $this->engine->assign('nombre',$this->session['usuario']['nombre']);
        $this->engine->assign('tipo',$this->session['usuario']['tipo']);
        $this->engine->display('cabecera.tpl');
        if (($this->session['usuario']['tipo'] == "administrador") OR ($this->session['usuario']['tipo'] == "veterinario")){
            $this->engine->display($this->temp_aux);
            $this->engine->display('buscar_animal.tpl');
        }else{
            $direccion=$gvar['l_global']."index.php";
            $this->mensaje("warning","Informacion",$direccion,"Lo sentimos, usted no tiene permisos para acceder");
            $this->engine->display($this->temp_aux); 
        }
        $this->engine->display('piedepagina.tpl');
    }
    
    public function run(){
        try {
            if (isset($this->get->option)) {
                if ($this->get->option == "buscar")
                    $this->{$this->get->option}();
                else
                    throw_exception("Opción ". $this->get->option." no disponible");
            }
        } catch (Exception $e) {
            #$this->error=1;
            $this->msg_warning=$e->getMessage();
            $this->temp_aux = 'message.tpl';
            $this->engine->assign('type_warning',$this->type_warning);
            $this->engine->assign('msg_warning',$this->msg_warning);
        }
        $this->display();
    }
        
}

    $call = new c_buscar_animal();
    $call->run();


?>
<?php

require('configs/include.php');

class c_perfil_administrador extends super_controller {
    
    public function mensaje($icon, $type, $dir, $content){
        $msg_icon=$icon;
        $msg_dir=$dir;
        $msg_type=$type;
        $msg_content=$content;

        $this->temp_aux = 'message.tpl';
        $this->engine->assign('msg_icon',$msg_icon);
        $this->engine->assign('msg_dir',$msg_dir);
        $this->engine->assign('msg_type',$msg_type);
        $this->engine->assign('msg_content',$msg_content);
    }

    public function display(){
        $this->engine->assign('title', "Perfil Administrador");
        $this->engine->assign('nombre',$this->session['usuario']['nombre']);
        $this->engine->assign('tipo',$this->session['usuario']['tipo']);
        $this->engine->display('cabecera.tpl');
        if ($this->session['usuario']['tipo'] == "administrador") {
            $_SESSION['mensaje']['tipo'] = '';
            $_SESSION['mensaje']['texto'] = '';
            $_SESSION['mensaje']['codigo'] = '';
            $this->engine->display($this->temp_aux);
            $this->engine->display('perfil_administrador.tpl');
        }else{
            $direccion=$gvar['l_global']."index.php";
            self::mensaje("warning","Informacion",$direccion,"Lo sentimos, usted no tiene permisos para acceder");
            $this->engine->display($this->temp_aux);    
        }
        $this->engine->display('piedepagina.tpl');
    }
    
    public function run() {
        $_SESSION['animal']['dueno']['cedula'] = "";
        $this->session = $_SESSION;

        $this->display();
    }
        
}

    $call = new c_perfil_administrador();
    $call->run();


?>
<?php

require('configs/include.php');
require('modules/m_phpass/PasswordHash.php');

class c_login extends super_controller {

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

    public function login() {

         $message1 = "";
         $message2 = "";

        if (is_empty($this->post->user)){
            $this->engine->assign("error1",0);
            $message1 = " Campo usuario vacío";
        }

        if (is_empty($this->post->pass)){
            $this->engine->assign("error2",0);
            $message2 = " Campo contraseña vacío";
        }

        if ($message1<>"" || $message2<>""){
            $direccion=$gvar['l_global']."perfil_administrador.php";
            self::mensaje("warning","Error",$direccion,"Hay campos vacíos");
            throw_exception("");
        }

        $options['administrador']['lvl2'] = "one_login";
        $cod['administrador']['user'] = $this->post->user;
        $cod['administrador']['pass'] = $this->post->pass;

        $options['veterinario']['lvl2'] = "one_login";
        $cod['veterinario']['user'] = $this->post->user;
        $cod['veterinario']['pass'] = $this->post->pass;

        $this->orm->connect();
        $this->orm->read_data(array("administrador","veterinario"), $options, $cod);
        $administrador = $this->orm->get_objects("administrador");
        $veterinario = $this->orm->get_objects("veterinario");
        $this->orm->close();

        if (is_empty($administrador) && is_empty($veterinario)){
            self::mensaje("warning","Error","","Nombre de usuario o contraseña invalidos");
            throw_exception("");
        }

        if (!is_empty($administrador)){
            $_SESSION['usuario']['identificacion'] = $administrador[0]->get('identificacion');
            $_SESSION['usuario']['nombre'] = $administrador[0]->get('nombre');
            $_SESSION['usuario']['telefono'] = $administrador[0]->get('telefono');
            $_SESSION['usuario']['email'] = $administrador[0]->get('email');
            $_SESSION['usuario']['tipo'] = "administrador";
            
            $this->session = $_SESSION;

        }elseif (!is_empty($veterinario)){
            $_SESSION['usuario']['identificacion'] = $veterinario[0]->get('identificacion');
            $_SESSION['usuario']['nombre'] = $veterinario[0]->get('nombre');
            $_SESSION['usuario']['telefono'] = $veterinario[0]->get('telefono');
            $_SESSION['usuario']['email'] = $veterinario[0]->get('email');
            $_SESSION['usuario']['sueldo'] = $veterinario[0]->get('sueldo');
            $_SESSION['usuario']['tipo'] = "veterinario";

            $this->session = $_SESSION;     
        }

        self::mensaje("check-square","Confirmacion","","Acceso exitoso");
        
        if ($this->session['usuario']['tipo']=="administrador"){
            header('Location: perfil_administrador.php');
        }elseif ($this->session['usuario']['tipo']=="veterinario") {
            header('Location: perfil_veterinario.php');
        }
        
    }

    public function logout() {
        session_destroy();
        unset($this->session);
        header('Location: index.php');
    }
    
    public function display(){
        $this->engine->assign('title', "Inicio de sesión");
        $this->engine->display('cabecera.tpl');
        $this->engine->display($this->temp_aux);
        $this->engine->display('login.tpl');
        $this->engine->display('piedepagina.tpl');
    
    }
    
    public function run() {
        try {
            if (isset($this->get->option)) {
                if ($this->get->option == "login"){
                    $this->{$this->get->option}();
                }elseif ($this->get->option == "logout") {
                    $this->{$this->get->option}();
                }else{
                    throw_exception("Opción ". $this->get->option." no disponible");
                }
            }
        } catch (Exception $e) {
            $this->error=1;
            $this->msg_warning=$e->getMessage();
            $this->temp_aux = 'message.tpl';
            $this->engine->assign('type_warning',$this->type_warning);
            $this->engine->assign('msg_warning',$this->msg_warning);
        }

        $this->display();
    }
        
}

    $call = new c_login();
    $call->run();


?>
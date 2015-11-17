<?php

require('configs/include.php');

class c_adquirir_producto extends super_controller {
    
    public function asignar_datos($nombre, $marca, $cantidad){
        $this->engine->assign('nombre_p',$nombre);
        $this->engine->assign('marca',$marca);
        $this->engine->assign('cantidad',$cantidad);      
    }

    public function asignar_vacios($nombre, $marca, $cantidad, $tipo){
        if (is_empty($nombre)){
            $this->engine->assign("nombre_vacio",0);
        }
        if (is_empty($marca)){
            $this->engine->assign("marca_vacio",0);
        }
        if (is_empty($cantidad)){
            $this->engine->assign("cantidad_vacio",0);
        }
    }

    public function asignar_invalidos($producto){
        if ((!is_numeric($producto->get('cantidad'))) OR ($producto->get('cantidad') <= 0)){
            $this->engine->assign("cantidad_invalido",0);     
        }
        if (($producto->get('tipo') != "medicamento") AND ($producto->get('tipo') != "implemento")){
            $this->engine->assign("tipo_invalido",0);    
        }             
    }

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

    public function agregar(){
        self::asignar_datos($this->post->nombre,$this->post->marca,$this->post->cantidad,$this->post->tipo);

        $producto = new producto($this->post);

        $producto->set('fecha_de_adquisicion',date('Y-m-d'));

        $incompletitud_producto = producto::validar_completitud($producto);

        if ($incompletitud_producto){
            self::asignar_vacios($this->post->nombre,$this->post->marca,$this->post->cantidad,$this->post->tipo);
            self::mensaje("warning","Error","","Hay campos vacíos");
            throw_exception("");  
        }

        if($this->post->tipo=="sel"){
            $this->engine->assign("tipo_vacio",0);
            self::mensaje("warning","Error","","Por favor seleccione un tipo");  
            throw_exception("");
        }
            
        $incorrectitud_producto = producto::validar_correctitud($producto);

        if ($incorrectitud_producto){
            self::asignar_invalidos($producto);
            self::mensaje("warning","Error","","Hay datos invalidos");
            throw_exception("");
        }

        $this->orm->connect();
        $this->orm->insert_data("normal",$producto);
        $this->orm->close();

        $dir=$gvar['l_global']."perfil_administrador.php";
        self::mensaje("check-circle","Confirmación",$dir,"Producto ingresado satisfactoriamente");
    }

    public function cancelar(){
        $msg_dir=$gvar['l_global']."perfil_administrador.php";
        self::mensaje("info","Informacion",$msg_dir,"Operación cancelada por el administrador");
    }

    public function display(){
        $this->engine->assign('title', "Adquirir Producto");
        $this->engine->assign('nombre',$this->session['usuario']['nombre']);
        $this->engine->assign('tipo',$this->session['usuario']['tipo']);
        $this->engine->display('cabecera.tpl');
        if ($this->session['usuario']['tipo'] == "administrador") {
            $this->engine->display($this->temp_aux);
            $this->engine->display('adquir_producto.tpl');
        }else{
            $direccion=$gvar['l_global']."index.php";
            self::mensaje("warning","Informacion",$direccion,"Lo sentimos, usted no tiene permisos para acceder");
            $this->engine->display($this->temp_aux); 
        }
        $this->engine->display('piedepagina.tpl');
    }
    
    public function run() {
        try {
            if ($_POST[agregar]){
                $this->get->option = "agregar";   
            }elseif ($_POST[cancelar]){
                $this->get->option = "cancelar";  
            }

            if (isset($this->get->option)) {
                if ($this->get->option == "agregar"){
                    $this->{$this->get->option}();
                }elseif ($this->get->option == "cancelar") {
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
    $call = new c_adquirir_producto();
    $call->run();
?>
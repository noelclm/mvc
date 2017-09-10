<?php

/**
 * Copyright 2017 Noel Clemente
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class loginController extends controller{
    
    /**
     * Objeto usuario que contiene el modelo
     * @var object
     */
    private $user;
    
    /**
     * Constructor
     */
    function __construct ($params){
        
        parent::__construct($params);
        $this->user = new userModel();

    } 
    
    /**
     * Pagina principal de login
     * 
     */
    public function sign_in () {
        
        if($_SESSION['login']){
            header('Location: home');
        }
        
        $this->view("login",['message' => "", 'message_color' => ""]);
        
    }
    
    /**
     * Comprueba si el usuario y contraseña introducidos son correctos
     *
     * @return boolean Devuelve true si se ha registrado el logueo
     */
    public function logIn (){
        
        $save = isset($this->params['save']) ? $this->params['save'] : false;  
        $user = isset($this->params['user']) ? escapeCode($this->params['user']) : "";
        $password = isset($this->params['password']) ? escapeCode($this->params['password']) : "";
        
        if($user != ""){
            $result = $this->user->searchUser("user = '".$user."'");

            if($result && password_verify($password, $result['password'])){

                // Si no deja varias sesiones a la vez por usuario borra las anteriores
                if(!MULTIPLE_SESSIONS){ 
                    $this->user->deleteSessions("`user_id` = ".$_SESSION['user_id']);
                }

                $this->createSession($result,$save);
                
                setFunctionality();
                
                header('Location: home');

            }else{
                unset($_POST);
                $this->view("login",['message' => "Usuario o contraseña incorrecta", 'message_color' => "#f00"]);
            }
        }else{
            header('Location: signin');
        }
            
    } 
    
    /**
    * Borra los datos de logueo
    * 
    */
    public function logout (){

        logout();
        $this->view("login",['message' => "Hasta pronto!", 'message_color' => ""]);

    }
    
    /**
     * Guarda la session en la variable $_SESSION, $_COOKIES y en la base de datos
     * 
     * @param array $result
     * @param boolean $save
     */
    private function createSession ($result, $save) {
        
        setSession($result);
        $this->user->insertSessions(["user_id","key","last_active","ip"], [$_SESSION['user_id'],$_SESSION['key'],$_SESSION['last_active'],$_SESSION['ip']]);
        
        if($save){ 
            setCookies(); 
        } 
        
    }

} 

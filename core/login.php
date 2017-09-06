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

class Login {

    private $db;
    /**
     * Constructor
     */
    function __construct (){
        
        $this->db = new dataAccess();
        session_start();
        session_regenerate_id(true);

    } 

    /**
     * Hace todas las comprobaciones al cargar la pagina
     * Devuelve:
     *  1000 = Esta logueado
     *  1001 = Ha hecho login  
     *  1002 = Usuario o contraseña incorrecta 
     *  1003 = Ha hecho logout 
     *  1004 = Sesion expirada
     *
     * @return int Codigo mensaje 
     */
    public function check (){

        $code = 1000;
        if(!isset($_SESSION['login'])){ $_SESSION['login'] = false; } 

        $this->deleteOldSession(); // Borra las sesiones que han caducado

        // Si ha pulsado en logout
        if(((isset($_GET['logout']) && $_GET['logout'] == true) || (isset($_POST['logout']) && $_POST['logout'] == true)) && (isset($_SESSION['login']) && $_SESSION['login'] == true)){
            $code = $this->logout();
        } // Si se acaba de loguear 
        elseif(isset($_POST['login']) && $_POST['login'] == true && isset($_POST['user']) && isset($_POST['password'])){
            // Si no esta marcado para que se guarde la sesion
            if(!isset($_POST['save'])){ $_POST['save'] = false; }
            $code =$this->login(escapeCode($_POST['user']),escapeCode($_POST['password']),$_POST['save']);
        } else {
            // Si no esta logeado comprueba las cookies
            if (!$_SESSION['login']) { $this->checkCookie(); } 
            // Si esta logueado mira si ha expirado la sesion
            if($_SESSION['login']){
                if(!$this->chekSesion()){ $code = 1004; }
                else { $this->setCookies(); }// Renueva las cookies
            }
        }

        $this->db->close();
        unset($this->db);
        
        return $code;

    } 

    /**
     * Borra de la base de datos las sesiones inactivas
     *
     */
    private function deleteOldSession (){
        
        if(IDLE_TIME > 0 && isset($_COOKIE["save"])){
            $this->db->execute("DELETE FROM session WHERE last_active + ". IDLE_TIME ." < " . time() . ";");
        }

    } 

    /**
     * Comprueba las cookies con los datos de la base de datos
     *
     */
    private function checkCookie (){
        
        if(isset($_COOKIE["user_id"]) && isset($_COOKIE["key"]) && isset($_COOKIE["login"]) && isset($_COOKIE["ip"]) && isset($_COOKIE["user"]) && isset($_COOKIE["name"])){
            
            $execute = $this->db->execute("SELECT * FROM `session` WHERE `ip` = '".$_COOKIE['ip']."' AND `user_id` = ".$_COOKIE['user_id']." AND `key` = '".$_COOKIE['key']."';");
            $result = [];
            if ($execute) {
                while(($row = $this->db->nextRow()) !== false){
                    $result[] = $row;
                }
            }
            
            if(count($result)){
                $_SESSION['name'] = $_COOKIE['name'];
                $_SESSION['user'] = $_COOKIE['user'];
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['key'] = $_COOKIE['key'];
                $_SESSION['ip'] = $_COOKIE['ip'];
                $_SESSION['last_activity'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['login'] = $_COOKIE['login'];
            }

        }

    }

    /**
     * Comprueba los datos de la variable $_SESSION con los datos de la base de datos y guarda el ultimo acceso
     *
     * @return boolean True si el login es valido
     */
    private function chekSesion (){

        $execute = $this->db->execute("SELECT * FROM `session` WHERE `key` = '".$_SESSION['key']."';");

        if ($execute) {
            while(($row = $this->db->nextRow()) !== false){
                $result[] = $row;
            }
        }

        if(count($result)){

            $result = $result[0];

            if($result['user_id'] == $_SESSION['user_id'] && $result['ip'] == $_SESSION['ip']){
                $this->db->execute("UPDATE session SET `last_active` = ".$_SERVER['REQUEST_TIME']." WHERE `key` = '".$_SESSION['key']."';");
                return true;
            }
            
        } 
        
        logout();
        return false;

    } 

    /**
     * Comprueba si el usuario y contraseña introducidos son correctos
     *
     * @param string $name Nombre del usuario
     * @param string $psw Contraseña del usuario
     * @param boolean $save Si esta marcada guardar la sesion
     * @return boolean Devuelve true si se ha registrado el logueo
     */
    private function logIn ($name, $psw, $save){

        $execute = $this->db->execute("SELECT * FROM user WHERE user = '".$name."' ;");
        
        if ($execute) {while(($row = $this->db->nextRow()) !== false){$result[] = $row;}}
                
        if(count($result) == 1){

            if(password_verify($psw, $result[0]['password'])){

                $_SESSION['name'] = $result[0]['name'];
                $_SESSION['user'] = $result[0]['user'];
                $_SESSION['user_id'] = $result[0]['user_id'];
                $_SESSION['key'] = session_id();
                $_SESSION['ip'] = $this->clientIp();
                $_SESSION['last_active'] = $_SERVER['REQUEST_TIME'];
                $_SESSION['login'] = true;
                
                if($save){ $this->setCookies(); } 
                // Si no deja varias sesiones a la vez por usuario borra las anteriores
                if(!MULTIPLE_SESSIONS){ $this->db->execute("DELETE FROM `session` WHERE `user_id` = '".$_SESSION['user_id']."';");}
 
                $this->db->execute("INSERT INTO `session` (`user_id`,`key`,`last_active`,`ip`) VALUES (".$_SESSION['user_id'].",'".$_SESSION['key']."',".$_SESSION['last_active'].",'".$_SESSION['ip']."');");

                return 1001;
                
            } 

        } 
        
        unset($_POST);
        return 1002;
        
    } 

    /**
     * Borra los datos de logueo
     * 
     */
    private function logout (){
        
        unset($_POST);
        unset($_GET);
            
        if(isset($_SESSION['key'])){ $this->db->execute("DELETE FROM `sesion` WHERE `key` = '".$_SESSION['key']."';"); }

        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['login'] = false;
        
        setcookie("name", $_SESSION['name'], -3600);
        setcookie("user", $_SESSION['user'], -3600);
        setcookie("user_id", $_SESSION['user_id'] , -3600);
        setcookie("key", $_SESSION['key'], -3600);
        setcookie("ip", $_SESSION['ip'], -3600);
        setcookie("login", $_SESSION['login'], -3600);

        return 1003;
        
    }

    /**
     * Mira la ip desde donde se accede a la web
     *
     * @return string IP del cliente
     */
    private function clientIp (){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])){ return $_SERVER['HTTP_CLIENT_IP']; }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ return $_SERVER['HTTP_X_FORWARDED_FOR']; }
        return $_SERVER['REMOTE_ADDR'];

    } 
    
    /**
     * Establece las cookies
     *
     */
    private function setCookies (){
        
        $_SESSION['last_active'] = $_SERVER['REQUEST_TIME'];
        
        setcookie("name", $_SESSION['name'], time()+(IDLE_TIME));
        setcookie("user", $_SESSION['user'], time()+(IDLE_TIME));
        setcookie("user_id", $_SESSION['user_id'] , time()+(IDLE_TIME));
        setcookie("key", $_SESSION['key'], time()+(IDLE_TIME));
        setcookie("ip", $_SESSION['ip'], time()+(IDLE_TIME));
        setcookie("login", $_SESSION['login'], time()+(IDLE_TIME));
        
    } 

} 

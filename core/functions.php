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

/**
 * Funcion inicial para buscar el controlador referente a la página y comprobar la sesión
 * 
 */
function ini(){
    
    session_start();
    session_regenerate_id(true);

    deleteOldSession();

    if(!isset($_SESSION['login'])){ 
        $_SESSION['login'] = false; 
        $_SESSION['funcionality'] = [];
    } 

    check();

    $route = DEFAULT_ROUTE;
    $params = array_merge($_GET,$_POST);
    $uri = $_SERVER['REQUEST_URI'];

    if($uri != "/"){
        if (strpos("?", $uri)) { $route = iconv_substr($uri, 0, strpos($uri, "?")); } 
        else { $route = $uri; }
    }

    $data = getControler($route);

    require_once "controllers/".$data['controller'].".php";
    $controller = new $data['controller']($params);
    $function = $data['function'];
    $controller->$function();

}



/**
 * Obtiene los datos del controlador del fichero routes.php
 * 
 * @param string $route
 * @return array
 */
function getControler($route){
    
    try{
        
        $row = false;
        $db = new dataAccess();
        $execute = $db->execute("SELECT * FROM routes WHERE page = '".$route."'");

        if($execute){ $row = $db->nextRow(); }

        $db->close();
        unset($db);

        if(!$row) {
            header('Location: 404');
        } else {
            
            if($row['funcionality'] == null || $row['funcionality'] == "" || in_array($row['funcionality'], $_SESSION['funcionality'])){
                $result = ['controller' => $row['controller'], 'function' => $row['function']];
            }else{
                header('Location: 403');
            }
           
        }

        return $result;
        
    } catch (Exception $e){
        header('Location: 503');
    }
    
}

/**
* Borra de la base de datos las sesiones inactivas
*
*/
function deleteOldSession (){

   if(IDLE_TIME >= 0 && isset($_COOKIE["save"])){
        $db = new dataAccess();
        $db->execute("DELETE FROM session WHERE last_active + ".IDLE_TIME." < ".time().";");
        $db->close();
        unset($db);
   }

} 

/**
 * Cambia el codigo para evitar insercion de codigo
 *
 * @param string Cadena a comprobar
 * @return string Devuelve la cadena transformando el codigo
 */
function escapeCode($string){
    
    return addslashes(htmlspecialchars($string));
    
}

/**
* Mira la ip desde donde se accede a la web
*
* @return string IP del cliente
*/
function clientIp (){

    if (!empty($_SERVER['HTTP_CLIENT_IP'])){ return $_SERVER['HTTP_CLIENT_IP']; }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ return $_SERVER['HTTP_X_FORWARDED_FOR']; }
    return $_SERVER['REMOTE_ADDR'];

} 

/**
* Establece las variables de sesión
* 
* @param array $result
*/
function setSession ($result){

    $key = isset($result['key']) ? $result['key'] : session_id(); 

    $_SESSION['name'] = $result['name'];
    $_SESSION['user'] = $result['user'];
    $_SESSION['user_id'] = $result['user_id'];
    $_SESSION['key'] = $key;
    $_SESSION['ip'] = clientIp();
    $_SESSION['last_active'] = $_SERVER['REQUEST_TIME'];
    $_SESSION['login'] = true;

    setFunctionality();

}

/**
* Establece las cookies
*
*/
function setCookies (){

    $_SESSION['last_active'] = $_SERVER['REQUEST_TIME'];

    if($_SESSION['login']){ $login = 1; }
    else{ $login = 0; }

    if(IDLE_TIME >= 0){
        setcookie("name", $_SESSION['name'], time()+(IDLE_TIME));
        setcookie("user", $_SESSION['user'], time()+(IDLE_TIME));
        setcookie("user_id", $_SESSION['user_id'] , time()+(IDLE_TIME));
        setcookie("key", $_SESSION['key'], time()+(IDLE_TIME));
        setcookie("ip", clientIp(), time()+(IDLE_TIME));
        setcookie("login", $login, time()+(IDLE_TIME));
    }else{
        setcookie("name", $_SESSION['name'], time()+(31536000));
        setcookie("user", $_SESSION['user'], time()+(31536000));
        setcookie("user_id", $_SESSION['user_id'], time()+(31536000));
        setcookie("key", $_SESSION['key'], time()+(31536000));
        setcookie("ip", clientIp(), time()+(31536000));
        setcookie("login", $login, time()+(31536000));
    }

} 

/**
* Hace todas las comprobaciones sobre la session
*
* @return int Codigo mensaje 
*/
function check(){

    // Si no esta logeado comprueba las cookies
    if(!$_SESSION['login']){ 
        checkCookie(); 
    } 

    // Si esta logueado mira si ha expirado la sesion y renueva las cookies
    if($_SESSION['login']){

        chekSession();
        if(isset($_COOKIE['login'])){ 
            setCookies(); 
        }

    }

} 

/**
* Comprueba las cookies con los datos de la base de datos
*
*/
function checkCookie (){
    
    if( isset($_COOKIE["user_id"]) && isset($_COOKIE["key"]) && 
        isset($_COOKIE["login"]) && isset($_COOKIE["ip"]) && 
        isset($_COOKIE["user"]) && isset($_COOKIE["name"]) ){


        $db = new dataAccess();

        $sql = "SELECT * "
                . "FROM session s "
                . "INNER JOIN user u ON S.user_id = u.user_id "
                . "WHERE u.user_id = ".$_COOKIE['user_id']." AND s.key = '".$_COOKIE['key']."';";
        
        $execute = $db->execute($sql);

        if($execute){ 

            $result = $db->nextRow(); 
            if($result){ 
                setSession($result); 
            }

        }

        $db->close();
        unset($db);

    }

}

/**
* Comprueba los datos de la variable $_SESSION con los datos de la base de datos y guarda el ultimo acceso
*
*/
function chekSession (){

    $db = new dataAccess();
    $execute = $db->execute("SELECT * FROM `session` WHERE `user_id` = ".$_SESSION['user_id']." AND `key` = '".$_SESSION['key']."';");

    if($execute){ 
        $result = $db->nextRow(); 
    }

    if($result){

        $db->execute("UPDATE session SET `last_active` = ".clientIp().", `last_active` = ".$_SERVER['REQUEST_TIME']." WHERE `key` = '".$_SESSION['key']."';");
        setFunctionality();
        
    }else{

        logout();
        header('Location: signin');

    }

    $db->close();
    unset($db);

} 

/**
* Borra los datos de logueo
* 
*/
function logout (){

    unset($_POST);
    unset($_GET);

    if(isset($_SESSION['key'])){ 
        
        $sql = "DELETE FROM `session` WHERE `user_id` = ".$_SESSION['user_id']." AND `key` = '".$_SESSION['key']."'";
        $db = new dataAccess();
        $db->execute($sql);
        $db->close();
        unset($db);
        
    }

    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['login'] = false;

    setcookie("name", false, -3600);
    setcookie("user", false, -3600);
    setcookie("user_id", false, -3600);
    setcookie("key", false, -3600);
    setcookie("ip", false, -3600);
    setcookie("login", false, -3600);

}

/**
 * Carga las funcionalidades del usuario
 */  
function setFunctionality(){

    $db = new dataAccess();
    $sql = "SELECT `funcionality`.`name` FROM `funcionality` "
            . "INNER JOIN `funcionality_vs_role` ON `funcionality_vs_role`.`funcionality_id` = `funcionality`.`funcionality_id` "
            . "INNER JOIN `user_vs_role` ON `user_vs_role`.`role_id` = `funcionality_vs_role`.`role_id` "
            . "WHERE `user_vs_role`.`user_id` = ".$_SESSION['user_id'].";";
    
    $execute = $db->execute($sql);
    
    $funcionality = [];
    
    if ($execute) {
        while(($row = $db->nextRow()) !== false){
            $funcionality[] = $row['name'];
        }
    }
    
    $_SESSION['funcionality'] = $funcionality;
    
    $db->close();
    unset($db);
    
}
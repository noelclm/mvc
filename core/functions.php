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
 * Carga el controlador y ejecuta la función
 * 
 * @param array $route
 * @param array $params
 */
function executeAccion($route,$params){
    
    $data = getControler($route);
    
    $login = new Login();
    $code = $login->check();
    
    require_once "controllers/".$data['controller'].".php";
    $controller = new $data['controller'](array_merge($params,['check_login' => $code]));
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
        
        $data = [];
        $bd = new dataAccess();
        $execute = $bd->execute("SELECT * FROM routes WHERE page = '".$route."'");

        if ($execute) {
            while(($row = $bd->nextRow()) !== false){ 
                $data[] = $row; 
            }
        }

        $bd->close();
        unset($bd);

        if(sizeof($data) == 0) {
            $result = ['controller' => "errorController", 'function' => "pageNotFound"];
        } else {
            $result = ['controller' => $data[0]['controller'], 'function' => $data[0]['function']];
        }

        return $result;
        
    } catch (Exception $e){
        return ['controller' => "errorController", 'function' => "unavailable", 'exception' => $e];
    }
    
}

/**
 * Cambia el codigo para evitar insercion de codigo
 *
 * @param string Cadena a comprobar
 * @return string Devuelve la cadena transformando el codigo
 */
function escapeCode ($string){
    
    return addslashes(htmlspecialchars($string));
    
}
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

class controller{
    
    /**
     * Parametros pasados por GET y POST
     * @var array 
     */
    private $params;

    /**
     * Constructor del controlador
     * 
     * @param array $params
     */
    public function __construct($params) {
        
        $this->params = $params;
        
        //Incluir todos los modelos
        foreach(glob("model/*.php") as $file){
            require_once $file;
        }
        
    }
    
    /**
     * Carga la vista del controlador
     * 
     * @param string $vista
     * @param array $datos
     */
    public function view($vista, $datos = []){
        
        foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor; 
        }
        require_once 'views/'.$vista.'View.php';
        
    }
    
    /**
     * Devuelve el parametro que ha recibido por get y post
     * 
     * @param string $param
     * @return mixed
     */
    public function get($param){
        
        return $this->params[$param];
        
    }
    
    /**
     * Redirige a la pagina de login si no esta registrado
     * 
     * @param boolena $login
     */
    public function isLogin($login){
        
        if($_SESSION['login'] != $login){
            header('Location: login');
        }
        
    }

}
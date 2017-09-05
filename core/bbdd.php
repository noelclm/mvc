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

class dataAccess {

    /**
     * Objeto de la conexión
     * @var object
     * @access protected
     */
    protected $bd;
    /**
     * Objeto devuelto tras realizar la ejecución de una query
     * @var object
     * @access protected
     */
    protected $query;
    
    /**
     * Constructor
     *
     * Establece una conexión con la base de datos mediante mysqli
     *
     * @param boolean $persistent Si la conexion esta marcada como persistente o no
     * @param string $server Dirección del servidor
     * @param string $user Usuario del servidor
     * @param string $password Contraseña del servidor
     * @param string $bd Base de datos del servidor
     * @param string $encoding Tipo de codificación del servidor
     * @global object $bd
     * @global object $query
     */
    function __construct ($persistent = PERSISTENT, $server = SERVER_CONNECTION, $user = USER_CONNECTION, $password = PSW_CONNECTION, $bd = BBDD_CONNECTION, $encoding = ENCODING){

        // Si esta marcado para que la conexion sea persistente se pone
        if($persistent){ $p = "p:"; }
        else{ $p = ""; }
        
        $this->bd = new mysqli($server, $user, $password, $bd);
        if ( $this->bd->connect_errno ){
            throw new Exception('Sin conexion a la Base de Datos');
        }
        $this->bd->set_charset($encoding);

    } 

    /**
     * Ejecuta una consulta
     *
     * @param string $sql Query a lanzar
     * @global object $bd
     * @global object $query
     * @return boolean True si se ha podido lanzar, false en caso contrario
     */
    function execute ($sql){

        if(($this->query = $this->bd->query($sql)) === false){
            return false;
        }else{
            return true;
        }

    }

    /**
     * Devuelve el siguiente resultado de la consulta
     *
     * @global object $query
     * @return mixe Array con los valores de la siguiente fila si quedan filas, false en caso contrario
     */
    function nextRow (){  
        
        $row = $this->query->fetch_assoc();
        if($row){
            return $row;
        }else{
            return false;
        }

    }
    
    /**
     * Devuelve el ultimo id insertado
     *
     * @global object $bd
     * @return int ID del ultimo insert que se realizó
     */
    function lastId (){
        
        return mysqli_insert_id($this->bd);
        
    }
    
    /**
     * Cierra la conexión con la base de datos
     *
     * @global object $bd
     */
    function close (){
        
        mysqli_close($this->bd);
        
    }

}

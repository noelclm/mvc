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

class model{
    
    public function __construct() {
        
    }
    
    /**
     * Consulta en la base de datos y devuelve un array con los resultados
     *
     * @param string $sql Select que se quiere lanzar
     * @return array Array con las lineas devueltas
     */
    public function selectSQL ($sql){

        $result = [];
        $bd = new dataAccess();
        $execute = $bd->execute($sql);

        if ($execute) {
            while(($row = $bd->nextRow()) !== false){
                $result[] = $row;
            }
        }

        $bd->close();
        unset($bd);

        return $result;

    }

    /**
     * Modifica una entrada en la base de datos
     *
     * @param string $sql Update que se quiere lanzar
     * @return boolean True si se ha realizado, false en caso contrario
     */
    public function updateSQL ($sql){

        $bd = new dataAccess();
        $result = $bd->execute($sql);

        $bd->close();
        unset($bd);

        return $result;

    }

    /**
     * Inserta una nueva entrada en la base de datos
     *
     * @param string $sql Insert que se quiere lanzar
     * @return mixe ID del dato insertado si se ha realizado, false en caso contrario
     */
    public function insertSQL ($sql){

        $bd = new dataAccess();

        if($bd->execute($sql)){
            $result = $bd->lastId();
        }else{
            $result = false;
        }

        $bd->close();
        unset($bd);

        return $result;

    }

    /**
     * Elimina una entrada en la base de dato
     *
     * @param string $sql Delete que se quiere lanzar
     * @return boolean True si se ha realizado, false en caso contrario
     */
    public function deleteSQL ($sql){

        $bd = new dataAccess();
        $result = $bd->execute($sql);

        $bd->close();
        unset($bd);

        return $result;

    }
    
}
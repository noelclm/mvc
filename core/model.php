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
    protected function selectSQL ($sql){

        $result = [];
        $db = new dataAccess();
        $execute = $db->execute($sql);

        if ($execute) {
            while(($row = $db->nextRow()) !== false){
                $result[] = $row;
            }
        }

        $db->close();
        unset($db);

        return $result;

    }

    /**
     * Modifica una entrada en la base de datos
     *
     * @param string $sql Update que se quiere lanzar
     * @return boolean True si se ha realizado, false en caso contrario
     */
    protected function updateSQL ($sql){

        $db = new dataAccess();
        $result = $db->execute($sql);

        $db->close();
        unset($db);

        return $result;

    }

    /**
     * Inserta una nueva entrada en la base de datos
     *
     * @param string $sql Insert que se quiere lanzar
     * @return mixe ID del dato insertado si se ha realizado, false en caso contrario
     */
    protected function insertSQL ($sql){

        $db = new dataAccess();

        if($db->execute($sql)){
            $result = $db->lastId();
        }else{
            $result = false;
        }

        $db->close();
        unset($db);

        return $result;

    }

    /**
     * Elimina una entrada en la base de dato
     *
     * @param string $sql Delete que se quiere lanzar
     * @return boolean True si se ha realizado, false en caso contrario
     */
    protected function deleteSQL ($sql){

        $db = new dataAccess();
        $result = $db->execute($sql);

        $db->close();
        unset($db);

        return $result;

    }
    
}
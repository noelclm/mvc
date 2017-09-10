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


class userModel extends model{
    
    /**
     * Devuelve los datos de un usuario, si no encuenta devuelve false
     * 
     * @param string $where
     * @return mixed
     */
    public function searchUser($where) {
        
        $sql = "SELECT * FROM user WHERE ".$where;
        $result = $this->selectSQL($sql);
        
        if(count($result) == 0){
            return false;
        }else{
            return $result[0];
        }
        
    }
    
    /**
     * Inserta una sesión, devuelve el id de la insercion
     * 
     * @param array $arrayColumns
     * @param array $arrayValues
     * @return mixed
     */
    public function insertSessions($arrayColumns,$arrayValues) {
        
        $escapedArrayColumns = [];
        $escapedArrayValues = [];
        
        foreach ($arrayColumns as $value) {
            $escapedArrayColumns[] = "`".$value."`";
        }
        
        foreach ($arrayValues as $value) {
            if(is_numeric($value)){
                $escapedArrayValues[] = $value;   
            }else{
                $escapedArrayValues[] = "'".$value."'";
            }
            
        }
        
        $columns = implode(",",$escapedArrayColumns);
        $values = implode(",",$escapedArrayValues);
        $sql = "INSERT INTO `session` (".$columns.") VALUES (".$values.")";
        return $this->insertSQL($sql);
        
    }
    
    /**
     * Borra las sesiones
     * 
     * @param string $where
     * @return boolean
     */
    public function deleteSessions($where) {
        
        $sql = "DELETE FROM `session` WHERE ".$where;
        return $this->deleteSQL($sql);
        
    }
    
    
    
}


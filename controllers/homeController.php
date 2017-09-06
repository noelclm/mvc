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

class homeController extends controller {

    /**
     * Página login
     */
    public function login(){
        
        if($_SESSION['login'] == true){
            header('Location: home');
        }
        
        $this->view("login");
        
    }
    
    /**
     * Página principal
     */
    public function home(){
        
        $this->isLogin(true); // Si no esta registrado te saca de a la pantalla de login
        $this->view("home");
        
    }

}
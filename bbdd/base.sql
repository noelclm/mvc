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

CREATE DATABASE IF NOT EXISTS mvc CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'mvc'@'localhost' IDENTIFIED VIA mysql_native_password USING 'password';
GRANT USAGE ON *.* TO 'mvc'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
GRANT ALL PRIVILEGES ON `mvc`.* TO 'mvc'@'localhost' WITH GRANT OPTION;

# -------------------------------------------
# Estructura de `session`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `session` (
    `session_id`        INT(11) NOT NULL AUTO_INCREMENT,
    `user_id`           INT(11) NOT NULL,
    `key`               VARCHAR(32) NOT NULL,
    `last_active`       INT(11) NOT NULL,
    `ip`                VARCHAR(15) NOT NULL,
    PRIMARY KEY  (`session_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

# -------------------------------------------
# Estructura de `user`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `user` (
    `user_id`           INT(11) NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(75) NOT NULL,
    `user`              VARCHAR(75) NOT NULL,
    `password`          VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    `role`              INT(11) NOT NULL,
    PRIMARY KEY  (`user_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

# -------------------------------------------
# Estructura de `routes`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `routes` (
    `routes_id`          INT(11) NOT NULL AUTO_INCREMENT,
    `page`               VARCHAR(75) NOT NULL,
    `controller`         VARCHAR(75) NOT NULL,
    `function`           VARCHAR(75) NOT NULL,
    `funcionality`       VARCHAR(75),
    PRIMARY KEY  (`routes_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

INSERT INTO routes (`page`,`controller`,`function`,`funcionality`) VALUES
('/login','homeController','login',null),
('/home','homeController','home',null);

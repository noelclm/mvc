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
# Estructura de `user`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `user` (
    `user_id`           INT(11) NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(75) NOT NULL,
    `user`              VARCHAR(75) NOT NULL,
    `password`          VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    PRIMARY KEY  (`user_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

INSERT INTO `user` (`name`, `user`, `password`, `email`) VALUES
('User', 'user', '', 'user@dominio.com');


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

ALTER TABLE `session` ADD CONSTRAINT `tsession_tuser` 
FOREIGN KEY (`user_id`) REFERENCES `mvc`.`user` (`user_id`)
ON DELETE NO ACTION ON UPDATE NO ACTION;

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
('/503','errorController','unavailable',null),
('/404','errorController','pageNotFound',null),
('/403','errorController','forbidden',null),
('/logout','loginController','logout',null),
('/login','loginController','login',null),
('/home','homeController','home','home');

# -------------------------------------------
# Estructura de `funcionality`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `funcionality` (
    `funcionality_id`    INT(11) NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(75) NOT NULL,
    `description`        VARCHAR(255) NOT NULL,
    PRIMARY KEY  (`funcionality_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

INSERT INTO `funcionality` (`name`,`description`) VALUES
('home','Acceso a la página principal');

# -------------------------------------------
# Estructura de `funcionality`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `role` (
    `role_id`            INT(11) NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(75) NOT NULL,
    `description`        VARCHAR(255) NOT NULL,
    PRIMARY KEY  (`role_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

INSERT INTO `role` (`name`,`description`) VALUES
('admin','Control total sobre la aplicación');

# -------------------------------------------
# Estructura de `funcionality_vs_role`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `funcionality_vs_role` (
    `funcionality_vs_role_id`   INT(11) NOT NULL AUTO_INCREMENT,
    `funcionality_id`           INT(11) NOT NULL,
    `role_id`                   INT(11) NOT NULL,
    PRIMARY KEY  (`funcionality_vs_role_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

ALTER TABLE `funcionality_vs_role` ADD CONSTRAINT `tfuncionality_vs_role_tfuncionality` 
FOREIGN KEY (`role_id`) REFERENCES `mvc`.`funcionality` (`funcionality_id`)
ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `funcionality_vs_role` ADD CONSTRAINT `tfuncionality_vs_role_trole` 
FOREIGN KEY (`role_id`) REFERENCES `mvc`.`role` (`role_id`)
ON DELETE NO ACTION ON UPDATE NO ACTION;

INSERT INTO `funcionality_vs_role` (`funcionality_id`,`role_id`) VALUES
((SELECT funcionality_id FROM funcionality WHERE `name` = 'home'),(SELECT role_id FROM `role` WHERE `name` = 'admin'));

# -------------------------------------------
# Estructura de `user_vs_role`
# -------------------------------------------

CREATE TABLE IF NOT EXISTS `user_vs_role` (
    `user_vs_role_id`   INT(11) NOT NULL AUTO_INCREMENT,
    `user_id`           INT(11) NOT NULL,
    `role_id`           INT(11) NOT NULL,
    PRIMARY KEY  (`user_vs_role_id`)
)ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;

ALTER TABLE `user_vs_role` ADD CONSTRAINT `tuser_vs_role_tuser` 
FOREIGN KEY (`role_id`) REFERENCES `mvc`.`user` (`funcionality_id`)
ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user_vs_role` ADD CONSTRAINT `tuser_vs_role_trole` 
FOREIGN KEY (`role_id`) REFERENCES `mvc`.`role` (`role_id`)
ON DELETE NO ACTION ON UPDATE NO ACTION;

INSERT INTO `user_vs_role` (`user_id`,`role_id`) VALUES
((SELECT user_id FROM `user` WHERE `user` = 'user'),(SELECT role_id FROM `role` WHERE `name` = 'admin'));

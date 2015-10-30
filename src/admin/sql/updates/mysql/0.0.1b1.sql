-- MySQL Workbench Synchronization
-- Generated: 2015-10-29 17:01
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: btomczak

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';

ALTER TABLE `#__oscampus_pathways`
ADD COLUMN `access` INT(11) NULL DEFAULT NULL AFTER `published`;

UPDATE `#__oscampus_pathways`
SET access = 1
WHERE access IS NULL;

ALTER TABLE `#__oscampus_lessons`
ADD INDEX `idx_ordering` (`ordering` ASC);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

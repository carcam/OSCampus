-- MySQL Script generated by MySQL Workbench
-- 06/12/15 16:55:48
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema OSCampus
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `#__oscampus_pathways`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_pathways` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_pathways` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NULL COMMENT 'User FK of this pathway owner (optional)',
  `title` VARCHAR(255) NOT NULL COMMENT 'Pathway name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe pathway name',
  `description` TEXT NOT NULL COMMENT 'Full pathway description',
  `image` VARCHAR(255) NOT NULL COMMENT 'Thumbnail image for pathway',
  `published` INT NOT NULL,
  `ordering` INT NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_users_id` (`users_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_teachers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_teachers` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_teachers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `image` VARCHAR(255) NOT NULL COMMENT 'Head shot image for teacher',
  `bio` TEXT NOT NULL COMMENT 'Teacher biography',
  `links` TEXT NOT NULL COMMENT 'External links (twitter, facebook, etc)',
  `created` DATETIME NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_users_id` (`users_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_courses` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_courses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `teachers_id` INT NULL DEFAULT NULL COMMENT 'Teachers FK',
  `difficulty` CHAR(12) NOT NULL COMMENT 'Difficulty/Level higher = harder',
  `length` INT NOT NULL COMMENT 'Time to take course in minutes',
  `title` VARCHAR(255) NOT NULL COMMENT 'Course name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe course name',
  `image` VARCHAR(255) NOT NULL COMMENT 'Thumnail image for course',
  `introtext` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `access` INT NOT NULL,
  `published` INT NOT NULL DEFAULT 1,
  `released` DATE NULL DEFAULT NULL COMMENT 'First date to make public',
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_teachers_id` (`teachers_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_modules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_modules` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_modules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `title` VARCHAR(255) NOT NULL COMMENT 'Module Name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe module name',
  `published` INT NOT NULL,
  `ordering` INT NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_courses_id` (`courses_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_lessons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_lessons` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_lessons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `modules_id` INT NOT NULL COMMENT 'Module FK',
  `title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `header` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `footer` TEXT NOT NULL,
  `access` INT NOT NULL,
  `published` INT NOT NULL DEFAULT 1,
  `ordering` INT NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_modules_id` (`modules_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_certificates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_certificates` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_certificates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `date_earned` DATETIME NOT NULL COMMENT 'Date certificate created',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_users_courses` (`users_id` ASC, `courses_id` ASC),
  INDEX `idx_courses_id` (`courses_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_courses_pathways`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_courses_pathways` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_courses_pathways` (
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `pathways_id` INT NOT NULL COMMENT 'Pathway FK',
  `ordering` INT NOT NULL COMMENT 'Ordering for this pathway',
  PRIMARY KEY (`courses_id`, `pathways_id`),
  INDEX `idx_pathways_id` (`pathways_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_users_lessons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_users_lessons` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_users_lessons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `lessons_id` INT NOT NULL COMMENT 'Lesson FK',
  `completed` DATETIME NULL,
  `score` FLOAT(3,2) NOT NULL,
  `visits` INT NOT NULL,
  `data` TEXT NOT NULL,
  `first_visit` DATETIME NULL,
  `last_visit` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_lessons_id` (`lessons_id` ASC),
  INDEX `idx_users_id` (`users_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_tags` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL COMMENT 'Tag name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe tag name',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_courses_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_courses_tags` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_courses_tags` (
  `courses_id` INT NOT NULL,
  `tags_id` INT NOT NULL,
  PRIMARY KEY (`courses_id`, `tags_id`),
  INDEX `idx_tags_id` (`tags_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_files` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_files` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `published` INT NOT NULL,
  `created` DATETIME NULL,
  `created_by` INT NULL,
  `created_by_alias` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_files_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_files_courses` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_files_courses` (
  `files_id` INT NOT NULL,
  `courses_id` INT NOT NULL,
  `ordering` INT NOT NULL,
  PRIMARY KEY (`files_id`, `courses_id`),
  INDEX `idx_files_courses` (`courses_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_files_lessons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_files_lessons` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_files_lessons` (
  `files_id` INT NOT NULL,
  `lessons_id` INT NOT NULL,
  `ordering` INT NOT NULL,
  PRIMARY KEY (`files_id`, `lessons_id`),
  INDEX `idx_lessons_files` (`lessons_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__oscampus_wistia_downloads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__oscampus_wistia_downloads` ;

CREATE TABLE IF NOT EXISTS `#__oscampus_wistia_downloads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL,
  `ip` CHAR(15) NOT NULL,
  `downloaded` DATETIME NOT NULL,
  `media_hashed_id` VARCHAR(255) NOT NULL,
  `media_project_name` VARCHAR(255) NOT NULL,
  `media_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_users_id` (`users_id` ASC),
  INDEX `idx_media_hashed_id` (`media_hashed_id` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- MySQL Script generated by MySQL Workbench
-- 04/22/15 17:13:11
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema OSCampus
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `ext53_oscampus_pathways`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_pathways` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_pathways` (
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
  `checked_out` INT NULL DEFAULT NULL,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_instructors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_instructors` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_instructors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `image` VARCHAR(255) NOT NULL COMMENT 'Head shot image for instructor',
  `bio` TEXT NOT NULL COMMENT 'Instructor biography',
  `parameters` TEXT NOT NULL COMMENT 'Misc information about instructor',
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NULL DEFAULT NULL,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_users_id` (`users_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_courses` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_courses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `instructors_id` INT NULL DEFAULT NULL COMMENT 'Instructors FK',
  `difficulty` CHAR(12) NOT NULL COMMENT 'Difficulty/Level higher = harder',
  `length` INT NOT NULL COMMENT 'Time to take course in minutes',
  `title` VARCHAR(255) NOT NULL COMMENT 'Course name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe course name',
  `image` VARCHAR(255) NOT NULL COMMENT 'Thumnail image for course',
  `introtext` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `published` INT NOT NULL DEFAULT 1,
  `publish_up` DATETIME NULL DEFAULT NULL,
  `publish_down` DATETIME NULL DEFAULT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NULL DEFAULT NULL,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_instructors_id` (`instructors_id` ASC),
  CONSTRAINT `courses_instructors`
    FOREIGN KEY (`instructors_id`)
    REFERENCES `ext53_oscampus_instructors` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_modules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_modules` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_modules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `title` VARCHAR(255) NOT NULL COMMENT 'Module Name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe module name',
  `description` TEXT NOT NULL COMMENT 'Module description',
  `published` INT NOT NULL,
  `ordering` INT NOT NULL,
  `access` INT NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_by_alias` VARCHAR(255) NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT NULL DEFAULT NULL,
  `checked_out` INT NULL DEFAULT NULL,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_courses_id` (`courses_id` ASC),
  CONSTRAINT `modules_courses`
    FOREIGN KEY (`courses_id`)
    REFERENCES `ext53_oscampus_courses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_lessons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_lessons` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_lessons` (
  `id` INT NOT NULL,
  `modules_id` INT NOT NULL COMMENT 'Module FK',
  PRIMARY KEY (`id`),
  INDEX `idx_modules_id` (`modules_id` ASC),
  CONSTRAINT `lessons_modules`
    FOREIGN KEY (`modules_id`)
    REFERENCES `ext53_oscampus_modules` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_certificates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_certificates` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_certificates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `date_earned` DATETIME NOT NULL COMMENT 'Date certificate created',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_users_courses` (`users_id` ASC, `courses_id` ASC),
  INDEX `idx_courses_id` (`courses_id` ASC),
  CONSTRAINT `certificates_courses`
    FOREIGN KEY (`courses_id`)
    REFERENCES `ext53_oscampus_courses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_courses_pathways`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_courses_pathways` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_courses_pathways` (
  `courses_id` INT NOT NULL COMMENT 'Course FK',
  `pathways_id` INT NOT NULL COMMENT 'Pathway FK',
  `ordering` INT NOT NULL COMMENT 'Ordering for this pathway',
  PRIMARY KEY (`courses_id`, `pathways_id`),
  INDEX `idx_pathways_id` (`pathways_id` ASC),
  CONSTRAINT `pathways_courses`
    FOREIGN KEY (`courses_id`)
    REFERENCES `ext53_oscampus_courses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `courses_pathways`
    FOREIGN KEY (`pathways_id`)
    REFERENCES `ext53_oscampus_pathways` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_users_lessons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_users_lessons` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_users_lessons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` INT NOT NULL COMMENT 'User FK',
  `lessons_id` INT NOT NULL COMMENT 'Lesson FK',
  PRIMARY KEY (`id`),
  INDEX `idx_lessons_id` (`lessons_id` ASC),
  CONSTRAINT `users_lessons`
    FOREIGN KEY (`lessons_id`)
    REFERENCES `ext53_oscampus_lessons` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_tags` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL COMMENT 'Tag name',
  `alias` VARCHAR(255) NOT NULL COMMENT 'URL safe tag name',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ext53_oscampus_courses_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ext53_oscampus_courses_tags` ;

CREATE TABLE IF NOT EXISTS `ext53_oscampus_courses_tags` (
  `courses_id` INT NOT NULL,
  `tags_id` INT NOT NULL,
  PRIMARY KEY (`courses_id`, `tags_id`),
  INDEX `idx_tags_id` (`tags_id` ASC),
  CONSTRAINT `courses_tags`
    FOREIGN KEY (`courses_id`)
    REFERENCES `ext53_oscampus_courses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `tags_courses`
    FOREIGN KEY (`tags_id`)
    REFERENCES `ext53_oscampus_tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

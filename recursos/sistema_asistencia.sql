-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema plataforma_uisrael
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema plataforma_uisrael
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `plataforma_uisrael` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci ;
USE `plataforma_uisrael` ;

-- -----------------------------------------------------
-- Table `usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario` (
  `codUsuario` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL,
  `clave` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL,
  `rol` ENUM('estudiante','docente','administrador') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'docente',
  `numeroIntentos` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT '0',
  `token` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `ultimoIngreso` DATETIME NULL DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codUsuario`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `persona`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `persona` (
  `codPersona` INT(11) NOT NULL AUTO_INCREMENT,
  `nombres` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `primerApellido` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `segundoApellido` VARCHAR(45) NULL DEFAULT NULL,
  `tipoDocumento` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `numeroDocumento` VARCHAR(12) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `fechaNacimiento` DATE NULL DEFAULT NULL,
  `correo` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `celular` CHAR(11) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `imagen` VARCHAR(45) NULL DEFAULT 'default',
  PRIMARY KEY (`codPersona`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `area_conocimiento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `area_conocimiento` (
  `codAreaConocimiento` INT(11) NOT NULL AUTO_INCREMENT,
  `areaConocimiento` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codAreaConocimiento`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `carrera_profesional`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carrera_profesional` (
  `codCarreraProfesional` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `carreraProfesional` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codAreaConocimiento` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codCarreraProfesional`),
  INDEX `fk_carrera_profesional_area_conocimiento1_idx` (`codAreaConocimiento` ASC),
  CONSTRAINT `fk_carrera_profesional_area_conocimiento1`
    FOREIGN KEY (`codAreaConocimiento`)
    REFERENCES `area_conocimiento` (`codAreaConocimiento`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `estudiante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `estudiante` (
  `codEstudiante` INT(11) NOT NULL AUTO_INCREMENT,
  `anioIngreso` CHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codUsuario` INT(11) NULL,
  `codPersona` INT(11) NOT NULL,
  `codCarreraProfesional` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codEstudiante`),
  INDEX `fk_estudiante_usuario1_idx` (`codUsuario` ASC),
  INDEX `fk_estudiante_persona1_idx` (`codPersona` ASC),
  INDEX `fk_estudiante_carrera_profesional1_idx` (`codCarreraProfesional` ASC),
  CONSTRAINT `fk_estudiante_usuario1`
    FOREIGN KEY (`codUsuario`)
    REFERENCES `usuario` (`codUsuario`)
    ON DELETE SET NULL
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_estudiante_persona1`
    FOREIGN KEY (`codPersona`)
    REFERENCES `persona` (`codPersona`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_estudiante_carrera_profesional1`
    FOREIGN KEY (`codCarreraProfesional`)
    REFERENCES `carrera_profesional` (`codCarreraProfesional`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `aula`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aula` (
  `codAula` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` CHAR(3) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `piso` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `capacidad` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codAula`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `seccion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seccion` (
  `codSeccion` INT(11) NOT NULL AUTO_INCREMENT,
  `seccion` ENUM('Diurno', 'Vespertina', 'Nocturno') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Diurno',
  PRIMARY KEY (`codSeccion`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `docente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `docente` (
  `codDocente` INT(11) NOT NULL AUTO_INCREMENT,
  `modalidad` ENUM('Nombrado', 'Contratado', 'TC') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Nombrado',
  `gradoAcademico` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL,
  `profesion` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codUsuario` INT(11) NULL,
  `codPersona` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codDocente`),
  INDEX `fk_docente_usuario_idx` (`codUsuario` ASC),
  INDEX `fk_docente_persona1_idx` (`codPersona` ASC),
  CONSTRAINT `fk_docente_usuario`
    FOREIGN KEY (`codUsuario`)
    REFERENCES `usuario` (`codUsuario`)
    ON DELETE SET NULL
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_docente_persona1`
    FOREIGN KEY (`codPersona`)
    REFERENCES `persona` (`codPersona`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `modalidad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `modalidad` (
  `codModalidad` INT(11) NOT NULL AUTO_INCREMENT,
  `modalidad` ENUM('Presencial', 'Semipresencial', 'Distancia') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Presencial',
  PRIMARY KEY (`codModalidad`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `ciclo_academico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclo_academico` (
  `codCicloAcademico` INT(11) NOT NULL AUTO_INCREMENT,
  `anio` CHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `semestre` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codCicloAcademico`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `plan_estudio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plan_estudio` (
  `codPlanEstudio` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(250) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL,
  `anioPlanEstudio` CHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `numeroCiclos` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `numeroCursosObligatorios` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `numeroCursosLectivos` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codCarreraProfesional` INT(11) NULL,
  `estado` TINYINT(1) NOT NULL,
  PRIMARY KEY (`codPlanEstudio`),
  INDEX `fk_plan_estudio_carrera_profesional1_idx` (`codCarreraProfesional` ASC),
  CONSTRAINT `fk_plan_estudio_carrera_profesional1`
    FOREIGN KEY (`codCarreraProfesional`)
    REFERENCES `carrera_profesional` (`codCarreraProfesional`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `curso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `curso` (
  `codCurso` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(45) NULL,
  `curso` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL,
  `nivel` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL,
  `abreviatura` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `creditos` CHAR(3) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codPlanEstudio` INT(11) NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codCurso`),
  INDEX `fk_curso_plan_estudio1_idx` (`codPlanEstudio` ASC),
  CONSTRAINT `fk_curso_plan_estudio1`
    FOREIGN KEY (`codPlanEstudio`)
    REFERENCES `plan_estudio` (`codPlanEstudio`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `curso_aperturado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `curso_aperturado` (
  `fechaApertura` DATE NULL DEFAULT NULL,
  `codCicloAcademico` INT(11) NOT NULL,
  `codCurso` INT(11) NOT NULL,
  PRIMARY KEY (`codCicloAcademico`, `codCurso`),
  INDEX `fk_curso_aperturado_ciclo_academico1_idx` (`codCicloAcademico` ASC),
  INDEX `fk_curso_aperturado_curso1_idx` (`codCurso` ASC),
  CONSTRAINT `fk_curso_aperturado_ciclo_academico1`
    FOREIGN KEY (`codCicloAcademico`)
    REFERENCES `ciclo_academico` (`codCicloAcademico`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_curso_aperturado_curso1`
    FOREIGN KEY (`codCurso`)
    REFERENCES `curso` (`codCurso`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `carga_academica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carga_academica` (
  `fechaInicioClases` DATE NULL,
  `codCicloAcademico` INT(11) NOT NULL,
  `codCurso` INT(11) NOT NULL,
  `esComun` CHAR(2) NOT NULL,
  `codAreaConocimiento` INT(11) NULL,
  `codCarreraProfesional` INT(11) NULL,
  `paralelo` VARCHAR(45) NOT NULL,
  `codModalidad` INT(11) NOT NULL,
  `codAula` INT(11) NOT NULL,
  `codSeccion` INT(11) NOT NULL,
  `codDocente` INT(11) NULL,
  PRIMARY KEY (`codCicloAcademico`, `codCurso`, `paralelo`, `codModalidad`),
  INDEX `fk_carga_academica_modalidad1_idx` (`codModalidad` ASC),
  INDEX `fk_carga_academica_aula1_idx` (`codAula` ASC),
  INDEX `fk_carga_academica_seccion1_idx` (`codSeccion` ASC),
  INDEX `fk_carga_academica_docente1_idx` (`codDocente` ASC),
  INDEX `fk_carga_academica_curso_aperturado2_idx` (`codCicloAcademico` ASC, `codCurso` ASC),
  INDEX `fk_carga_academica_carrera_profesional1_idx` (`codCarreraProfesional` ASC),
  INDEX `fk_carga_academica_area_conocimiento1_idx` (`codAreaConocimiento` ASC),
  CONSTRAINT `fk_carga_academica_modalidad1`
    FOREIGN KEY (`codModalidad`)
    REFERENCES `modalidad` (`codModalidad`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_carga_academica_aula1`
    FOREIGN KEY (`codAula`)
    REFERENCES `aula` (`codAula`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_carga_academica_seccion1`
    FOREIGN KEY (`codSeccion`)
    REFERENCES `seccion` (`codSeccion`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_carga_academica_docente1`
    FOREIGN KEY (`codDocente`)
    REFERENCES `docente` (`codDocente`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_carga_academica_curso_aperturado2`
    FOREIGN KEY (`codCicloAcademico` , `codCurso`)
    REFERENCES `curso_aperturado` (`codCicloAcademico` , `codCurso`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carga_academica_carrera_profesional1`
    FOREIGN KEY (`codCarreraProfesional`)
    REFERENCES `carrera_profesional` (`codCarreraProfesional`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carga_academica_area_conocimiento1`
    FOREIGN KEY (`codAreaConocimiento`)
    REFERENCES `area_conocimiento` (`codAreaConocimiento`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `sesion_clase`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sesion_clase` (
  `codSesionClase` INT(11) NOT NULL AUTO_INCREMENT,
  `fecha` DATE NULL DEFAULT NULL,
  `diaSemana` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `horaInicio` TIME NULL DEFAULT NULL,
  `horaFin` TIME NULL DEFAULT NULL,
  `asistenciaRealizada` ENUM('Si','No') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'No',
  `totalEstudiantes` INT(3) NULL DEFAULT NULL,
  `estudiantesAsistieron` INT(3) NULL DEFAULT NULL,
  `avanceSilabo` ENUM('Si','No') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'No',
  `totalTemas` INT(3) NULL DEFAULT NULL,
  `temasTerminados` INT(3) NULL,
  `sesionTerminada` ENUM('Si','No') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'No',
  `observacion` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL,
  `codCicloAcademico` INT(11) NOT NULL,
  `codCurso` INT(11) NOT NULL,
  `codModalidad` INT(11) NOT NULL,
  `paralelo` VARCHAR(45) NOT NULL,
  `codAula` INT(11) NOT NULL,
  `codSeccion` INT(11) NOT NULL,
  `codDocente` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codSesionClase`, `codCicloAcademico`, `codCurso`, `codModalidad`, `paralelo`, `codAula`, `codSeccion`, `codDocente`),
  INDEX `fk_sesion_clase_aula1_idx` (`codAula` ASC),
  INDEX `fk_sesion_clase_seccion1_idx` (`codSeccion` ASC),
  INDEX `fk_sesion_clase_docente1_idx` (`codDocente` ASC),
  INDEX `fk_sesion_clase_carga_academica1_idx` (`codCicloAcademico` ASC, `codCurso` ASC, `paralelo` ASC, `codModalidad` ASC),
  CONSTRAINT `fk_sesion_clase_aula1`
    FOREIGN KEY (`codAula`)
    REFERENCES `aula` (`codAula`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_sesion_clase_seccion1`
    FOREIGN KEY (`codSeccion`)
    REFERENCES `seccion` (`codSeccion`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_sesion_clase_docente1`
    FOREIGN KEY (`codDocente`)
    REFERENCES `docente` (`codDocente`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_sesion_clase_carga_academica1`
    FOREIGN KEY (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    REFERENCES `carga_academica` (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `asistencia_estudiante`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `asistencia_estudiante` (
  `codAsistenciaEstudiante` INT(11) NOT NULL AUTO_INCREMENT,
  `estadoAsistenciaEstudiante` ENUM('Puntual','Tarde','Falta') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Falta',
  `codEstudiante` INT(11) NOT NULL,
  `codSesionClase` INT(11) NOT NULL,
  PRIMARY KEY (`codAsistenciaEstudiante`, `codEstudiante`, `codSesionClase`),
  INDEX `fk_asistencia_estudiante_estudiante1_idx` (`codEstudiante` ASC),
  INDEX `fk_asistencia_estudiante_sesion_clase1_idx` (`codSesionClase` ASC),
  CONSTRAINT `fk_asistencia_estudiante_estudiante1`
    FOREIGN KEY (`codEstudiante`)
    REFERENCES `estudiante` (`codEstudiante`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_asistencia_estudiante_sesion_clase1`
    FOREIGN KEY (`codSesionClase`)
    REFERENCES `sesion_clase` (`codSesionClase`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `semana`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `semana` (
  `codSemana` INT(11) NOT NULL AUTO_INCREMENT,
  `semana` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`codSemana`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `tematica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tematica` (
  `codTematica` INT(11) NOT NULL AUTO_INCREMENT,
  `tematica` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`codTematica`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `silabo_detalle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `silabo_detalle` (
  `codSilaboDetalle` INT(11) NOT NULL AUTO_INCREMENT,
  `codCurso` INT(11) NOT NULL,
  `codSemana` INT(11) NOT NULL,
  `codTematica` INT(11) NOT NULL,
  PRIMARY KEY (`codSilaboDetalle`),
  INDEX `fk_silabo_detalle_semana1_idx` (`codSemana` ASC),
  INDEX `fk_silabo_detalle_tematica1_idx` (`codTematica` ASC),
  INDEX `fk_silabo_detalle_curso1_idx` (`codCurso` ASC),
  CONSTRAINT `fk_silabo_detalle_semana1`
    FOREIGN KEY (`codSemana`)
    REFERENCES `semana` (`codSemana`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_silabo_detalle_tematica1`
    FOREIGN KEY (`codTematica`)
    REFERENCES `tematica` (`codTematica`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_silabo_detalle_curso1`
    FOREIGN KEY (`codCurso`)
    REFERENCES `curso` (`codCurso`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `avance_silabo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `avance_silabo` (
  `codAvanceSilabo` INT(11) NOT NULL AUTO_INCREMENT,
  `avance` ENUM('Iniciado','Terminado') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Iniciado',
  `observaciones` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  `codSilaboDetalle` INT(11) NOT NULL,
  `codSesionClase` INT(11) NOT NULL,
  PRIMARY KEY (`codAvanceSilabo`),
  INDEX `fk_avance_silabo_silabo_detalle1_idx` (`codSilaboDetalle` ASC),
  INDEX `fk_avance_silabo_sesion_clase1_idx` (`codSesionClase` ASC),
  CONSTRAINT `fk_avance_silabo_silabo_detalle1`
    FOREIGN KEY (`codSilaboDetalle`)
    REFERENCES `silabo_detalle` (`codSilaboDetalle`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_avance_silabo_sesion_clase1`
    FOREIGN KEY (`codSesionClase`)
    REFERENCES `sesion_clase` (`codSesionClase`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `dia_semana`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dia_semana` (
  `codDiaSemana` INT(11) NOT NULL AUTO_INCREMENT,
  `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'Lunes',
  PRIMARY KEY (`codDiaSemana`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `horario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `horario` (
  `codHorario` INT(11) NOT NULL AUTO_INCREMENT,
  `horaInicio` TIME NULL DEFAULT NULL,
  `horaFin` TIME NULL DEFAULT NULL,
  `codDiaSemana` INT(11) NOT NULL,
  `codCicloAcademico` INT(11) NOT NULL,
  `codCurso` INT(11) NOT NULL,
  `paralelo` VARCHAR(45) NOT NULL,
  `codModalidad` INT(11) NOT NULL,
  PRIMARY KEY (`codHorario`, `codDiaSemana`, `codCicloAcademico`, `codCurso`, `paralelo`, `codModalidad`),
  INDEX `fk_horario_dia_semana1_idx` (`codDiaSemana` ASC),
  INDEX `fk_horario_carga_academica1_idx` (`codCicloAcademico` ASC, `codCurso` ASC, `paralelo` ASC, `codModalidad` ASC),
  CONSTRAINT `fk_horario_dia_semana1`
    FOREIGN KEY (`codDiaSemana`)
    REFERENCES `dia_semana` (`codDiaSemana`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_horario_carga_academica1`
    FOREIGN KEY (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    REFERENCES `carga_academica` (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `matricula`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matricula` (
  `fechaMatricula` DATE NULL,
  `codEstudiante` INT(11) NOT NULL,
  `codCicloAcademico` INT(11) NOT NULL,
  `codCurso` INT(11) NOT NULL,
  `paralelo` VARCHAR(45) NOT NULL,
  `codModalidad` INT(11) NOT NULL,
  PRIMARY KEY (`codEstudiante`, `codCicloAcademico`, `codCurso`, `paralelo`, `codModalidad`),
  INDEX `fk_matricula_carga_academica1_idx` (`codCicloAcademico` ASC, `codCurso` ASC, `paralelo` ASC, `codModalidad` ASC),
  CONSTRAINT `fk_matricula_estudiante1`
    FOREIGN KEY (`codEstudiante`)
    REFERENCES `estudiante` (`codEstudiante`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_matricula_carga_academica1`
    FOREIGN KEY (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    REFERENCES `carga_academica` (`codCicloAcademico` , `codCurso` , `paralelo` , `codModalidad`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `session` (
  `id` CHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT '',
  `name` CHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT '',
  `modified` INT(11) NULL DEFAULT NULL,
  `lifetime` INT(11) NULL DEFAULT NULL,
  `data` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `name`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


-- -----------------------------------------------------
-- Table `administrador`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `administrador` (
  `codAdministrador` INT(11) NOT NULL AUTO_INCREMENT,
  `codUsuario` INT(11) NULL,
  `codPersona` INT(11) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codAdministrador`),
  INDEX `fk_administrador_usuario1_idx` (`codUsuario` ASC),
  INDEX `fk_administrador_persona1_idx` (`codPersona` ASC),
  CONSTRAINT `fk_administrador_usuario1`
    FOREIGN KEY (`codUsuario`)
    REFERENCES `usuario` (`codUsuario`)
    ON DELETE SET NULL
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_administrador_persona1`
    FOREIGN KEY (`codPersona`)
    REFERENCES `persona` (`codPersona`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_spanish_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `plataforma_uisrael`;
INSERT INTO `usuario` (`codUsuario`, `usuario`, `clave`, `rol`, `numeroIntentos`, `token`, `ultimoIngreso`, `estado`) VALUES (1, 'admin', 'admin', 'administrador', NULL, NULL, NULL, 1);
INSERT INTO `usuario` (`codUsuario`, `usuario`, `clave`, `rol`, `numeroIntentos`, `token`, `ultimoIngreso`, `estado`) VALUES (2, 'docente', 'docente', 'docente', NULL, NULL, NULL, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `persona`
-- -----------------------------------------------------
START TRANSACTION;
USE `plataforma_uisrael`;
INSERT INTO `persona` (`codPersona`, `nombres`, `primerApellido`, `segundoApellido`, `tipoDocumento`, `numeroDocumento`, `fechaNacimiento`, `correo`, `celular`, `imagen`) VALUES (1, 'Felix', 'Contreras', 'Martinez', 'DNI', '47623721', '21/05/1991', 'zirtrex@live.com', '966102508', 'default');
INSERT INTO `persona` (`codPersona`, `nombres`, `primerApellido`, `segundoApellido`, `tipoDocumento`, `numeroDocumento`, `fechaNacimiento`, `correo`, `celular`, `imagen`) VALUES (2, 'Julio', 'Gutierrez', 'Chavez', 'DNI', '12345678', '15/05/1975', 'jguticha@gmail.com', '123456789', 'default');

COMMIT;


-- -----------------------------------------------------
-- Data for table `docente`
-- -----------------------------------------------------
START TRANSACTION;
USE `plataforma_uisrael`;
INSERT INTO `docente` (`codDocente`, `modalidad`, `gradoAcademico`, `profesion`, `codUsuario`, `codPersona`, `estado`) VALUES (1, 'Contratado', 'Doctor', 'Ingeniero', 2, 2, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `administrador`
-- -----------------------------------------------------
START TRANSACTION;
USE `plataforma_uisrael`;
INSERT INTO `administrador` (`codAdministrador`, `codUsuario`, `codPersona`, `estado`) VALUES (1, 1, 1, 1);

COMMIT;


/********************************************
 *******************VISTAS*******************
 ********************************************/

CREATE VIEW vw_estudiante
AS
  SELECT e.codEstudiante, e.anioIngreso, 
		p.codPersona, p.nombres, p.primerApellido, p.segundoApellido, p.tipoDocumento, p.numeroDocumento, p.fechaNacimiento, p.correo, p.celular, p.imagen,
        u.usuario, u.rol, numeroIntentos, token, ultimoIngreso,
        cp.codCarreraProfesional, cp.carreraProfesional
  FROM estudiante e
  INNER JOIN persona p ON p.codPersona = e.codPersona
  LEFT JOIN usuario u ON u.codUsuario = e.codUsuario
  LEFT JOIN carrera_profesional cp ON cp.codCarreraProfesional = e.codCarreraProfesional;
  

CREATE VIEW vw_docente
AS
  SELECT d.codDocente, d.modalidad, d.gradoAcademico, d.profesion,
	p.codPersona, p.nombres, p.primerApellido, p.segundoApellido, p.tipoDocumento, p.numeroDocumento, p.fechaNacimiento, p.correo, p.celular, p.imagen,
	u.codUsuario, u.usuario, u.rol, numeroIntentos, token, ultimoIngreso
  FROM docente d
  INNER JOIN persona p ON p.codPersona = d.codPersona
  LEFT JOIN usuario u ON u.codUsuario = d.codUsuario;
  

CREATE VIEW vw_carrera_profesional
AS
  SELECT cp.codCarreraProfesional, cp.codigo, cp.carreraProfesional,
		arco.codAreaConocimiento, arco.areaConocimiento
  FROM carrera_profesional cp
  INNER JOIN area_conocimiento arco ON arco.codAreaConocimiento = cp.codAreaConocimiento;
 
 
CREATE VIEW vw_administrador
AS
  SELECT a.codAdministrador,
		p.codPersona, p.nombres, p.primerApellido, p.segundoApellido, p.tipoDocumento, p.numeroDocumento, p.fechaNacimiento, p.correo, p.celular, p.imagen,
        u.codUsuario, u.usuario, u.rol , numeroIntentos, token, ultimoIngreso
  FROM administrador a
  INNER JOIN persona p ON p.codPersona = a.codPersona
  LEFT JOIN usuario u ON u.codUsuario = a.codUsuario;

  
CREATE VIEW vw_plan_estudio
AS
  SELECT pe.codPlanEstudio, pe.titulo, pe.anioPlanEstudio, pe.numeroCiclos,
		cp.codCarreraProfesional, cp.carreraProfesional
  FROM plan_estudio pe
  LEFT JOIN carrera_profesional cp ON cp.codCarreraProfesional = pe.codCarreraProfesional;

  
CREATE VIEW vw_curso
AS
  SELECT c.codCurso, c.codigo, c.curso, c.nivel, c.abreviatura, c.creditos,
		pe.codPlanEstudio, pe.titulo, pe.anioPlanEstudio
  FROM curso c
  LEFT JOIN plan_estudio pe ON pe.codPlanEstudio = c.codPlanEstudio;

  
CREATE VIEW vw_curso_aperturado
AS
  SELECT cuap.fechaApertura,
        ciac.codCicloAcademico, ciac.anio, ciac.semestre,
		c.codCurso, c.codigo, c.curso, c.nivel, c.abreviatura, c.creditos, c.codPlanEstudio, c.titulo, c.anioPlanEstudio	
  FROM curso_aperturado cuap
  LEFT JOIN ciclo_academico ciac ON ciac.codCicloAcademico = cuap.codCicloAcademico
  INNER JOIN vw_curso c ON c.codCurso = cuap.codCurso;
 

CREATE VIEW vw_carga_academica
AS
  SELECT caac.fechaInicioClases, caac.paralelo, caac.esComun,	
	ciac.codCicloAcademico, ciac.anio, ciac.semestre,
    ac.codAreaConocimiento, ac.areaConocimiento,
    cp.codCarreraProfesional, cp.carreraProfesional,
	c.codCurso, c.codigo, c.curso, c.creditos,
	m.codModalidad, m.modalidad,	
	a.codAula, a.numero,
	s.codSeccion, s.seccion,
	d.codDocente, d.nombres, d.primerApellido, d.segundoApellido
  FROM carga_academica caac
  LEFT JOIN area_conocimiento ac ON ac.codAreaConocimiento = caac.codAreaConocimiento
  LEFT JOIN carrera_profesional cp ON cp.codCarreraProfesional = caac.codCarreraProfesional  
  LEFT JOIN ciclo_academico ciac ON ciac.codCicloAcademico = caac.codCicloAcademico
  INNER JOIN vw_curso c ON c.codCurso = caac.codCurso
  LEFT JOIN modalidad m ON m.codModalidad = caac.codModalidad 
  LEFT JOIN aula a ON a.codAula = caac.codAula
  LEFT JOIN seccion s ON s.codSeccion = caac.codSeccion
  LEFT JOIN vw_docente d ON d.codDocente = caac.codDocente;
  

CREATE VIEW vw_matricula
AS
  SELECT ma.fechaMatricula, ma.paralelo,
	e.codEstudiante, e.nombres, e.primerApellido, e.segundoApellido, e.correo,
	ciac.codCicloAcademico, ciac.anio, ciac.semestre, 
	c.codCurso, c.curso, c.abreviatura,
	m.codModalidad, m.modalidad
  FROM matricula ma
  INNER JOIN vw_estudiante e ON e.codEstudiante = ma.codEstudiante
  LEFT JOIN ciclo_academico ciac ON ciac.codCicloAcademico = ma.codCicloAcademico
  INNER JOIN curso c ON c.codCurso = ma.codCurso
  LEFT JOIN modalidad m ON m.codModalidad = ma.codModalidad;

 
CREATE VIEW vw_silabo_detalle
AS
  SELECT sd.codSilaboDetalle, c.codCurso, se.semana, t.tematica
  FROM silabo_detalle sd
  INNER JOIN curso c ON c.codCurso = sd.codCurso
  INNER JOIN semana se ON se.codSemana = sd.codSemana
  INNER JOIN tematica t ON t.codTematica = sd.codTematica
  ORDER BY c.codCurso, se.codSemana;
  

CREATE VIEW vw_sesion_clase
AS
  SELECT secl.codSesionClase, secl.fecha, secl.diaSemana, secl.horaInicio, secl.horaFin, secl.asistenciaRealizada, secl.totalEstudiantes, secl.estudiantesAsistieron, secl.avanceSilabo, secl.totalTemas, secl.temasTerminados, secl.sesionTerminada, secl.observacion,
	caac.codCicloAcademico, caac.anio, caac.semestre,
    caac.esComun,
    caac.codAreaConocimiento, caac.areaConocimiento,
    caac.codCarreraProfesional, caac.carreraProfesional, 
	caac.codCurso, caac.codigo, caac.curso,
	caac.codModalidad, caac.modalidad,
    secl.paralelo,
	caac.codAula, caac.numero,
    caac.codSeccion, caac.seccion,	
	caac.codDocente, caac.nombres, caac.primerApellido, caac.segundoApellido
  FROM sesion_clase secl
  LEFT JOIN vw_carga_academica caac ON caac.codCicloAcademico = secl.codCicloAcademico AND caac.codCurso = secl.codCurso AND caac.codModalidad = secl.codModalidad AND caac.paralelo = secl.paralelo
  ORDER BY secl.codSesionClase;
 
 
CREATE VIEW vw_avance_silabo
AS
  SELECT avsi.codAvanceSilabo, avsi.avance, avsi.observaciones,
	sd.codSilaboDetalle, sd. semana, sd.tematica,
	secl.codSesionClase, secl.codCicloAcademico, secl.codCurso, secl.codModalidad, secl.paralelo, secl.codAula, secl.codSeccion, secl.codDocente
  FROM avance_silabo avsi
  INNER JOIN vw_silabo_detalle sd ON sd.codSilaboDetalle = avsi.codSilaboDetalle 
  LEFT JOIN vw_sesion_clase secl ON secl.codSesionClase = avsi.codSesionClase;
  
/*  
SELECT *, COUNT(codSesionClase) AS asistencias FROM vw_sesion_clase
WHERE codCarreraProfesional LIKE CONCAT("%", "3", "%")
GROUP BY codCarreraProfesional, codCicloAcademico, codCurso, codDocente, codAula, codTurno


SELECT MAX(temasTerminados) AS totalTemasTerminados FROM vw_sesion_clase
GROUP BY codCarreraProfesional, codCicloAcademico, codCurso, codDocente, codAula, codTurno
ORDER BY totalTemasTerminados DESC
LIMIT 2*/
  

CREATE VIEW vw_horario_sesion_clase
AS  
  SELECT sc.codCicloAcademico, sc.codCurso, sc.codModalidad, sc.paralelo, sc.codAula, sc.codSeccion, sc.codDocente,
		sc.codSesionClase, sc.fecha, WEEK(sc.fecha) AS semanaAnio, sc.diaSemana, sc.horaInicio, sc.horaFin,
        h.codDiaSemana, h.horaInicio AS horaInicioHorario, h.horaFin AS horaFinHorario, TIMESTAMPDIFF(MINUTE, h.horaInicio, sc.horaInicio) AS minutosTarde	
  FROM sesion_clase sc
  LEFT JOIN horario h ON h.codCicloAcademico = sc.codCicloAcademico AND h.codCurso = sc.codCurso AND h.codModalidad = sc.codModalidad AND h.paralelo = sc.paralelo
  ORDER BY sc.codSesionClase;
  


CREATE VIEW vw_asistencia_estudiante
AS
  SELECT ae.codAsistenciaEstudiante, ae.estadoAsistenciaEstudiante,
	e.codEstudiante, CONCAT(e.primerApellido, ' ', e.segundoApellido, ', ', e.nombres) AS estudiante,
    secl.esComun,
    secl.codAreaConocimiento, secl.areaConocimiento,
    secl.codCarreraProfesional, secl.carreraProfesional, 
	secl.codCicloAcademico, secl.anio, secl.semestre,
	secl.codCurso, secl.codigo, secl.curso, 
	secl.codModalidad, secl.modalidad,
    secl.paralelo,
	secl.codAula, secl.numero, 
    secl.codSeccion, secl.seccion,
	secl.codDocente, CONCAT(secl.nombres, ' ', secl.primerApellido, ' ', secl.segundoApellido) AS docente
  FROM asistencia_estudiante ae
  LEFT JOIN vw_estudiante e ON e.codEstudiante = ae.codEstudiante
  LEFT JOIN vw_sesion_clase secl ON secl.codSesionClase = ae.codSesionClase
  ORDER BY e.primerApellido;
  
/*SELECT * FROM vw_asistencia_estudiante WHERE codDocente = '1';
  
SELECT *, COUNT(estado) AS numeroClases FROM vw_asistencia_estudiante
WHERE codCarreraProfesional = "1" AND codCicloAcademico = "7" AND codCurso = "C001"
GROUP BY codEstudiante

  
SELECT COUNT(estado) AS totalEstado FROM vw_asistencia_estudiante_estado 
WHERE codEstudiante = "1" AND estado = "Puntual";

SELECT *, max(temasTerminados) AS maxTemasTerminados, max(temasTerminados) * 100 / totalTemas AS avance FROM plataforma_uisrael.sesion_clase
GROUP BY codCicloAcademico, paralelo, codAula, codSeccion, codCurso, codDocente, codModalidad
ORDER BY avance DESC;
*/

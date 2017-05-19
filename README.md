GP-Syllabus -> Seacadu <<Seguimiento Académico Universitario>>
==============================================================

Sistema basado en Zend Framework 2.4

Instrucciones
-------------
Para la correcta instalación del sistema es mejor empezar desde la base de datos en blanco.

Requisitos:
-----------
PHP 5.4 o Superior
MySQL 5 o Superior
Servidor Apache 2


Observaciones
-------------
Revisar la carpeta recursos, en ella se encuentran los modelos de csv y el archivo excel con el modelo de ingreso de datos



Cambios en el tiempo
--------------------

2.4.1:
----
- Se soluciono el error de asignación de temas a los cursos. Ahora el formulario cuenta con un campo para agregar el Ciclo Académico.
- Se agrego el Autocomplete para la asignación de cursos.
- Se ha agregado la opciones de configuraciones, donde se podrán agregar las configuraciones necesarias por el momento solo tenemos 3: ciclo_academico, codCicloAcademico, plegable.
El registro "codCicloAcademico" permite asignar el ciclo en el que se está trabajando actualmente, esto a su vez permite que a los docentes solo se muestre los cursos del ciclo actual.

Notas: Recuerde cambiar el "codCicloAcademico" con el valor que corresponda, actualmente tiene el valor 1, que es por defecto.


2.4:
---
Esta versión hace un cambio importante en la base de datos debido a que los temas ya no se relacionan a un curso, al contrario lo hacen con la carga académica.
Es por ello que cada carga académica al igual como hacemos con los estudiantes, asi mismo debemos agregar los temas a tratar.
Otra mejora es que en el apartado de temas se ha agregado un autocomplete para que sea más sencillo agregar los temas a los nuevos cursos.


Webmaster: Rafael Contreras (Grupo Parada) rcontreras@grupoparada.com Telegram: +51966102508

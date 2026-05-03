Título:
Retroalimentación Evaluación 2 - Arquitectura y MVC

Contenido:
Equipo E9 - Evaluación 2

Calificación: 14/20

Desglose:
- Análisis del sistema actual: 4/6
- Diseño propuesto MVC: 3.5/4
- Implementación MVC: 4/6
- Uso de Git: 1.4/2
- README: 1.2/2

Fortalezas:
El grupo identifica correctamente un problema concreto dentro del proyecto: el módulo de login estaba implementado con Livewire/Volt, mezclando lógica de autenticación con componentes de vista, por lo que decidieron refactorizarlo hacia una estructura más cercana al patrón MVC.

Se observa una implementación real del módulo de login mediante app/Http/Controllers/Auth/LoginController.php, una vista separada en resources/views/auth/login.blade.php y la actualización de routes/auth.php para usar el controlador. Esto cumple con el objetivo mínimo de aplicar MVC en al menos un módulo.

También se evidencia participación de ambos integrantes en Git, con actividad de kevin77552-png y JaceBaker21. Hay commits relacionados con README, controlador de login, vista de login y estilos.

Aspectos a mejorar:
El alcance de la implementación fue limitado. La refactorización MVC se aplicó principalmente al módulo de login, mientras que gran parte del proyecto sigue organizada mediante Livewire/Volt. Aunque esto es válido para cumplir el mínimo, pudieron aplicar el patrón a un módulo adicional del sistema, por ejemplo inventario, solicitudes o reportes.

El README debe limpiarse y organizarse mejor. Actualmente mantiene mucho contenido genérico de Laravel, repite secciones de Evaluación P3 y presenta rastros de conflicto de merge, como líneas con ======= y >>>>>>>. Eso afecta la presentación formal de la entrega.

El diagnóstico inicial pudo profundizar más en otros problemas estructurales del sistema y no limitarse solo al login. También sería recomendable explicar mejor el flujo MVC implementado: ruta → controlador → validación/autenticación → vista/respuesta.

En Git se observa participación de ambos integrantes, pero algunos mensajes pueden mejorar y se observan force push, lo cual debe evitarse en entregas académicas salvo que esté justificado.

Recomendación:
Limpiar el README, eliminar conflictos de merge y contenido genérico, documentar mejor el flujo MVC y aplicar la separación MVC a otro módulo funcional del sistema para fortalecer la evidencia de comprensión.

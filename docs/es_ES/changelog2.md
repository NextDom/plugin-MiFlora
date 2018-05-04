# Changelog

### Versión 3.0.0 - mayo de 2018 - en desarrollo
* Gestión de antenas remotas
   - Posibilidad de crear antenas
   - Posibilidad de que cada objeto especifique por qué antena se leerá
   -  Atención:
        - el complemento ahora tiene dependencias para iniciarse, solo son útiles para administrar la migración a la versión 3.0.0 para los usuarios actuales si no usan Market para la actualización. Para todos los demás casos, las dependencias no son útiles.
        - puede ser necesario desactivarlo y luego activarlo para actualizar los nuevos campos, los datos se conservan durante este proceso.
* Pestaña de salud que permite ver de manera resumida el estado de MiFlora.
* Agregar valores predeterminados para la alerta de batería baja.
* Administre la flor Parrot en una secuencia de comandos de Python dedicada para una futura integración en el complemento.
* Se agregó la funcionalidad de actualización y la transición de un mínimo de 5 a 15 minutos..
    - Esta característica se puede usar desde un escenario o haciendo clic en el widget en modo de escritorio.
    - Tenga cuidado de poner una frecuencia de al menos 15 minutos para sus objetos existentes.

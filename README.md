# ALTA
```bash
php index.php cliente-catalogo recalvi 502 0
```
# VACIADO DE LA CACHE DE PUBLICIDADES
```bash
php index.php eliminar-publicidades recalvi 5215 0
```

# VACIADO DE LA CACHE DE GENÉRICOS
```bash
php index.php eliminar-genericos-padre recalvi 5215 0
```

# ACTUALIZACIÓN DE LOS CONTADORES DE LAS MATRÍCULAS
1. **-c** Centro que se quiere actualizar
2. **-s** Sumatorio de matrículas
    1. **0:** Indica que el contador no se suma, sino que es el total actualizado
    2. **1:** Indica que a las matrículas que el usuario tiene actualmente en el contador, se le suman otras.

```bash
php index.php matriculas -c recalvi -s 0
```

# GENERACIÓN DE UN DOCUMENTO RPV CON UN CÓDIGO DE BARRAS Y UN TEXTO
-r Ruta donde se dejarán los archivos resultantes (imagen y rpv)
-s String a codificar
```bash
php index.php tarjeta-personal -r ./ruta -s "string a codificar"
```

# GENERAR DOCUMENTOS DE LAS RUEDAS DE RECALVI
Con esta funcionalidad podemos generar los ficheros de las ruedas del proveedor Soledad de Recalvi 

## Generar el listado de ruedas que Recalvi va a vender: **neumaticos-recalvi**
Este comando genera la lista de ruedas y la pasa por el proceso de COBOL que introduce los neumáticos en Microgest
```bash
php index.php neumaticos-soledad
```
## Generar el listado de precios que Recalvi va a vender: **precios-recalvi**
Este comando genera la lista de precio de las ruedas y la pasa por el proceso de COBOL que introduce los neumáticos en Microgest
Tiene las siguientes opciones:
1. Configuración del incremento del porcentaje que se va a poner al precio de las ruedas -p

```bash
# Generamos el fichero con los precios de las ruedas con un 5% de incremento en el precio de venta
NOTA: Este comando actualiza la base de datos de stock en el webservice restful
php index.php precios-soledad -p 5
```

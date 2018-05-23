# ALTA
```bash
php /mnt/imagenes/php/catalogov2/index.php cliente-catalogo recalvi 502 0
```
# VACIADO DE LA CACHE DE PUBLICIDADES
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-publicidades recalvi 5215 0
```

# VACIADO DE LA CACHE DE GENÉRICOS
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-genericos-padre recalvi 5215 0
```

# ACTUALIZACIÓN DE LOS CONTADORES DE LAS MATRÍCULAS
1. **-c** Centro que se quiere actualizar
2. **-s** Sumatorio de matrículas
    1. **0:** Indica que el contador no se suma, sino que es el total actualizado
    2. **1:** Indica que a las matrículas que el usuario tiene actualmente en el contador, se le suman otras.

```bash
php /mnt/imagenes/php/catalogov2/index.php matriculas -c recalvi -s 0
```

# GENERACIÓN DE UN DOCUMENTO RPV CON UN CÓDIGO DE BARRAS Y UN TEXTO
-r Ruta donde se dejarán los archivos resultantes (imagen y rpv)
-s String a codificar
```bash
php /mnt/imagenes/php/catalogov2/index.php tarjeta-personal -r ./ruta -s "string a codificar"
```

# GENERAR DOCUMENTOS DE LAS RUEDAS DE RECALVI
Con esta funcionalidad podemos generar los ficheros de las ruedas del proveedor Soledad de Recalvi 

## Generar el listado de ruedas que Recalvi va a vender: **neumaticos-recalvi**
Este comando genera la lista de ruedas y la pasa por el proceso de COBOL que introduce los neumáticos en Microgest
```bash
php /mnt/imagenes/php/catalogov2/index.php neumaticos-soledad
```
## Generar el listado de precios que Recalvi va a vender: **precios-recalvi**
Este comando genera la lista de precio de las ruedas y la pasa por el proceso de COBOL que introduce los neumáticos en Microgest
Tiene las siguientes opciones:
1. Configuración del incremento del porcentaje que se va a poner al precio de las ruedas -p
# NOTA: Este comando actualiza la base de datos de stock en el webservice restful

```bash
# Generamos el fichero con los precios de las ruedas con un 0% de incremento en el precio de venta

php /mnt/imagenes/php/catalogov2/index.php precios-soledad -p 0
```

# GENERAR UN LOGIN PARA EL CATÁLOGO ONLINE
1. **-c** Centro del cliente
2. **-co** Correo del cliente
3. **-p** Contraseña
4. **-e** Empresa, para este funcionalidad usamos la cadena "internos"
5. **-r** Ruta del fichero resultante con el login completo

```bash
php /mnt/imagenes/php/catalogov2/index.php login-catalogo -c recalvi -co antoniogonzalez@m2m_recalvi.com -p Soledad2017 -e internos -r /home/gr/temporales-catalogov2/ficherologinproduccion.txt
```
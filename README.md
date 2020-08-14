# ALTA
```bash
php /mnt/imagenes/php/probasv2/index.php clientes-catalogo -c recalvi -n 2325 -s 1 -e 3 -cli 100
```

# BAJA
```bash
php /mnt/imagenes/php/probasv2/index.php clientes-catalogo-baja -c recalvi -n 2325 -s 1 -e 3 -cli 100 -co chari1@recalvi.es
```

# VACIADO DE LA CACHE DE PUBLICIDADES
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-publicidades -c recalvi
```

### comando obsoleto
Este era el comando usado anteriormente
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-publicidades recalvi 5215 0
```

# VACIADO DE LA CACHE DE GENÉRICOS
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-genericos -c recalvi
```

### comando obsoleto
Este era el comando usado anteriormente
```bash
php /mnt/imagenes/php/catalogov2/index.php eliminar-genericos-padre recalvi 5215 0
```

# ACTUALIZACIÓN DE LOS CONTADORES DE LAS MATRÍCULAS
1. **-c** Centro que se quiere actualizar
2. **-s** Sumatorio de matrículas
    1. **0:** Indica que el contador no se suma, sino que es el total actualizado
    2. **1:** Indica que a las matrículas que el usuario tiene actualmente en el contador, se le suman otras.
3. **-r** Ruta al CSV con los datos de la matrículas

```bash
php index.php matriculas-envio-consultas -c recalvi -s 0 -r /home/gr/matriclulas.csv
```


### comando obsoleto
1. **-c** Centro que se quiere actualizar
2. **-s** Sumatorio de matrículas
    1. **0:** Indica que el contador no se suma, sino que es el total actualizado
    2. **1:** Indica que a las matrículas que el usuario tiene actualmente en el contador, se le suman otras.

```bash
php /mnt/imagenes/php/catalogov2/index.php matriculas -c recalvi -s 0
```

# GENERACIÓN DE UN DOCUMENTO RPV CON UN CÓDIGO DE BARRAS Y UN TEXTO
### Obsoleta
-r Ruta donde se dejarán los archivos resultantes (imagen y rpv)
-s String a codificar
```bash
php /mnt/imagenes/php/catalogov2/index.php tarjeta-personal -r ./ruta -s "string a codificar"
```
### Nueva funcionalidad
-r ruta al CSV de los datos a imprimir
-d ruta al pdf destino
```bash
php index.php tarjeta-fichaje -r ./ruta/fichero.csv -d ./ruta/fichero.pdf
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
php /mnt/imagenes/php/catalogov2/index.php login-catalogo -c recalvi -co antoniogonzalez@m2m_recalvi.com -p Soledad2017 -e 3 -r /home/gr/temporales-catalogov2/ficherologinproduccion.txt
```

# GENERAR UNA TARJETA DE EXPEDICIONES
```bash
php /mnt/imagenes/php/probas2/index.php tarjeta-expediciones -o ruta_json_datos.json -d ruta_pdf_destino.pdf
```

# Generar la consulta para rellenar el informe de matrículas
```bash
# Opción sólo con centro (copia las del día d hoy)
php index.php migrar-matriculas -c 1
# Opción con centro y fecha
php index.php migrar-matriculas -c 1 -f 2020-02-01 
# Opción con centro y todas las matrículas de cualquier fecha
php index.php migrar-matriculas -c 1 -f todas
```

# GENERAR URL DE FACTURAS
Opciones:
1. **-r** Ruta al CSV con los datos de Microgest
2. **-d** Ruta del CSV con los datos generados
3. **-c** Nombre del centro
4. **-vh (opcional)** Ver hasta. Cantidad de días que será útil el enlace generado

El CSV origen tiene que tener la siguiente estructura: centro;nocliente;subdivision;cliente_de;empresa;factura_numero;factura_anho;factura_serie

```bash
php {ruta_microgest}/index.php facturas-a-pdf -r {ruta_csv_origen} -d {ruta_csv_destino} -c {centro} -vh {fecha_en_dias}
```

# GENERAR URL DE ALBARANES
Opciones:
1. **-r** Ruta al CSV con los datos de Microgest
2. **-d** Ruta del CSV con los datos generados
3. **-c** Nombre del centro
4. **-vh (opcional)** Ver hasta. Cantidad de días que será útil el enlace generado

El CSV origen tiene que tener la siguiente estructura: centro;nocliente;subdivision;cliente_de;empresa;numero

```bash
php {ruta_microgest}/index.php albaranes-a-pdf -r {ruta_csv_origen} -d {ruta_csv_destino} -c {centro} -vh {fecha_en_dias}
```
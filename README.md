# ALTA
php index.php cliente-catalogo recalvi 502 0

# VACIADO DE LA CACHE DE PUBLICIDADES
php index.php eliminar-publicidades recalvi 5215 0

# VACIADO DE LA CACHE DE GENÉRICOS
php index.php eliminar-genericos-padre recalvi 5215 0

# ACTUALIZACIÓN DE LOS CONTADORES DE LAS MATRÍCULAS
-c Centro que se quiere actualizar
-s 0: Indica que el contador no se suma, sino que es el total actualizado; 1: Indica que a las matrículas que el usuario tiene actualmente en el contador, se le suman otras.
php mantenimientos.php matriculas -c recalvi -s 0

<?php

if (!function_exists('help')) {
    function help(string $directory, $command) {
        if (is_string($command)) {
            $data = command_data($directory, $command);
            $descripcion = (isset($data->descripcion)) ? $data->descripcion : "";
            _echo("\t- $command: $descripcion");
            if ($data->indices) {
                _echo("\t\t- Parámetros");
                foreach ($data->indices as $key => $value) {
                    $tag = $value->tag;
                    $descripcion = isset($value->descripcion) ? $value->descripcion : "";
                    $obligatorio = $value->obligatorio ? "Si" : "No";
                    $tipo = str_replace("is_", "", $value->tipo);
                    _echo("\t\t\t$key:");
                    _echo("\t\t\t\tTag:\t\t $tag");
                    _echo("\t\t\t\tDescripción:\t $descripcion");
                    _echo("\t\t\t\tObligatorio:\t $obligatorio");
                    _echo("\t\t\t\tTipo:\t\t $tipo");
                }
                _echo("\t\t- Acciones");
                foreach ($data->acciones as $key => $value) {
                    $descripcion = isset($value->descripcion) ? $value->descripcion : "";
                    _echo("\t\t\t$key");
                    _echo("\t\t\t\tBiblioteca: \t$value->clase::$value->metodo");
                    _echo("\t\t\t\tDescripción: \t$descripcion");
                }
            }
        } else {
            $commands = commands_list($directory);
            foreach ($commands as $c) {
                $data = command_data($directory, $c);
                $descripcion = (isset($data->descripcion)) ? $data->descripcion : "";
                _echo("\t- $c: $descripcion");
            }
        }
    }
}

if (!function_exists('commands_list')) {
    function commands_list(string $directory) {
        $list = scandir($directory);
        $clean = array_diff($list, ['..', '.']);
        return array_map(function ($c) {
            return str_replace('.json', '', $c);
        }, $clean);
    }
}

if (!function_exists('command_data')) {
    function command_data(string $directory, string $command) {
        if (!command_exists($directory, $command)) {
            throw new \Exception("[$command] No existe el comando. No se continúa");
        }
        $fileContent = file_get_contents("$directory$command.json");
        return json_decode($fileContent);
    }
}

if (!function_exists('command_exists')) {
    function command_exists(string $directory, string $command) {
        return file_exists("$directory$command.json");
    }
}
if (!function_exists('needs_help')) {
    /**
     * Indica si el usuario está solicitando información sobre los comandos
     *
     * @param Array $argv
     * @return mixed 
     *          true si sólo indica -h
     *          false si no
     *          string con el nombre de un comando si este se indica
     */
    function needs_help(array $argv) {
        $itNeeds = false;
        foreach ($argv as $key => $value) {
            if ($value === '-h' && command_exists(RUTA_COMANDOS, $argv[1])) {
                $itNeeds = $argv[1];
                break;
            } else if ($value === '-h') {
                $itNeeds = true;
                break;
            }
        }
        return $itNeeds;
    }
}
if (!function_exists('check_parameters')) {
    function check_parameters($array, $parametrosMinimos) {
        $contador = count($array);
        $etiqueta = $array[1];
        unset($array[0]); // nombre del fichero
        unset($array[1]); // etiqueta con la accion
        // creamos un array con pares clave valor
        // para ello la cuenta total de parámetros tiene que ser par
        if (count($array) % 2 !== 0) {
            throw new \Exception("[$etiqueta] Todo parámetro debe disponer de un valor");
        }
        $comandos = [];
        for ($i = 2; $i < $contador; $i++) {
            $clave = $array[$i++];
            $valor = $array[$i];
            $comandos[$clave] = $valor;
        }
        // Comprobamos que no se hayan introducido parámetros de menos ...
        if ($parametrosMinimos->cantidad > count($comandos)) {
            throw new \Exception("[$etiqueta] Este comando debe estar compuesto por $parametrosMinimos->cantidad parametro(s)");
        }
        foreach ($parametrosMinimos->indices as $clave => $valor) {
            $tipo = $valor->tipo;
            if ($valor->obligatorio && (!isset($comandos[$clave]) || !$tipo($comandos[$clave]))) {
                throw new \Exception("[$etiqueta] $clave no es de tipo $tipo");
            }
            if (isset($valor->tag, $comandos[$clave])) {
                $comandos[$valor->tag] = $comandos[$clave];
                unset($comandos[$clave]);
            }
        }
        return $comandos;
    }
}

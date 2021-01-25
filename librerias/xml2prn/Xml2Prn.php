<?php

class Xml2Prn
{
    public function convertir($comandos)
    {

        $fdatos = $comandos['estructura'];
        if (!$fdatos) {
            throw new \Exception("[Xml2Prn] Falta fichero estructura datos.");
        }
        $fichero = $comandos['fichero-origen'];
        if (!$fichero) {
            throw new \Exception("[Xml2Prn] Falta fichero origen datos.");
        }
        $prn = $comandos['fichero-destino'];
        if (!$prn) {
            throw new \Exception("[Xml2Prn] Falta fichero destino prn.");
        }

        _echo_info("Datos: $fdatos");
        _echo_info("XML: $fichero");
        _echo_info("PRN: $prn");

        $this->xmlprn($fdatos, $fichero, $prn);
    }

    function xmlprn($fdatos, $fichero, $prn)
    {
        $mainnode = "";
        $nodes = array(
            "items" => array(),
            "size" => array(),
            "type" => array(),
            "dec" => array(),
        );
        $nodesdirectos =  array(
            "items" => array(),
            "size" => array(),
            "type" => array(),
            "dec" => array(),
        );

        $linea = 0;
        //Abrimos nuestro archivo
        $archivo = fopen($fdatos, "r");
        //Recogemos todos los nodes
        while (($datos = fgetcsv($archivo, ";")) == true) {
            $num = count($datos);
            $linea++;
            //Recorremos las columnas de esa linea
            for ($columna = 0; $columna < $num; $columna++) {
                $arr = explode(";", $datos[$columna]);
                if (!empty($arr[0])) {
                    $mainnode =  $arr[0];
                    array_push($nodes["items"], $arr[1]);
                    array_push($nodes["size"], $arr[2]);
                    array_push($nodes["type"], $arr[3]);
                    array_push($nodes["dec"], $this->create100($arr[4]));
                } else {
                    array_push($nodesdirectos["items"], $arr[1]);
                    array_push($nodesdirectos["size"], $arr[2]);
                    array_push($nodesdirectos["type"], $arr[3]);
                    array_push($nodesdirectos["dec"], $this->create100($arr[4]));
                }
            }
        }
        //Cerramos el archivo
        fclose($archivo);

        /*var_dump($nodes["size"]);
        var_dump($nodes["type"]);
        var_dump($nodes["dec"]);
        var_dump($nodesdirectos["items"]);*/

            /*foreach ($nodes as $v) {
            print_r($v.'</br>');
        }*/
        //echo $mainnode;

        //$f = fopen('convertido.csv', 'w');
        $fprn = fopen($prn, "w");
        $xml = simplexml_load_file($fichero);



        // Busca el nodo principal
        $objnode = $xml;
        $mnodes = explode("->", $mainnode);
        foreach ($mnodes as $ar) {
            if (property_exists($objnode, $ar)) {
                $objnode = $objnode->$ar;
            }
        }

        //Busca los nodos que van a leer directamente
        $ctrd = 0;
        $txtd = "";
        foreach ($nodesdirectos["items"] as $nd) {
            $sized = $nodesdirectos["size"][$ctrd];
            $typed = $nodesdirectos["type"][$ctrd];
            $decd = $nodesdirectos["dec"][$ctrd];
            $objnodedircto = $xml;
            $dnodes = explode("->", $nd);
            foreach ($dnodes as $ard) {
                if (property_exists($objnodedircto, $ard)) {
                    if (isset($objnodedircto->$ard) && !empty(trim($objnodedircto->$ard))) {
                        $vard = "";
                        if ($typed == "9") {
                            $num = floatval($objnodedircto->$ard);
                            if (is_float($num)) {
                                $num = $objnodedircto->$ard * $decd;
                                $num = intval($num);
                            }
                            $vard =  $this->completarCon($num, "0", $sized);
                        } else {
                            $vard = $this->llenarConEspaciosDerecha($objnodedircto->$ard, $sized);
                        }
                        $txtd .= $vard;
                    }
                    $objnodedircto = $objnodedircto->$ard;
                }
            }
            $ctrd++;
        }
        //echo $txtd;


        $cnt = count($objnode);
        //$cnt = count($xml->CstmrCdtTrfInitn->PmtInf->CdtTrfTxInf);
        for ($i = 0; $i < $cnt; $i++) {
            $put_arr = array();
            $txt = $txtd;
            $ctr = 0;
            foreach ($nodes["items"] as $v) {
                $size = $nodes["size"][$ctr];
                $type = $nodes["type"][$ctr];
                $dec = $nodes["dec"][$ctr];
                $obj = $objnode[$i];
                $arrnodes = explode("->", $v);
                foreach ($arrnodes as $ar) {
                    if (property_exists($obj, $ar)) {
                        if (isset($obj->$ar) && !empty(trim($obj->$ar))) {
                            array_push($put_arr, $obj->$ar);
                            $var = "";
                            if ($type == "9") {
                                $num = floatval($obj->$ar);
                                if (is_float($num)) {
                                    $num = $obj->$ar * $dec;
                                    $num = intval($num);
                                }
                                $var =  $this->completarCon($num, "0", $size);
                            } else {
                                $var =  $this->llenarConEspaciosDerecha($obj->$ar, $size);
                            }
                            $txt .= $var;
                        }
                        $obj = $obj->$ar;
                    }
                }
                $ctr++;
            }
            /*$a = "Amt";
            $b = "InstdAmt";
            $item1 =  $xml->CstmrCdtTrfInitn->PmtInf->CdtTrfTxInf[$i]->$a->$b;
            array_push($put_arr, $item1);
            $item2 =  $xml->CstmrCdtTrfInitn->PmtInf->CdtTrfTxInf[$i]->Cdtr->Nm;
            array_push($put_arr, $item2);*/

            //fputcsv($f, $put_arr, ',', '"');
            $txt = $txt . "\n";
            fwrite($fprn, $txt);
        }
        //fclose($f);
        fclose($fprn);
    }



    //Zend\Xml2Json\Xml2Json::fromXml(trim($cleaned), true);

    function create100($total)
    {
        $num = "1";
        $length = 0;
        while ($length != $total) {
            $num .= "0";
            $length++;
        }
        return intval($num);
    }

    /**
     * pone los caracteres a la izquierda (indicado para enteros, completar con
     * 0 a la izquierda)
     *
     * @param referencia referencia a tratar
     * @param caracter caracter a completar, siempre "0"
     * @param total total de todos los caracteres
     * @return referencia modificada
     */
    function completarCon($referencia, $caracter, $total)
    {
        if (!$referencia) {
            $referencia = "";
        } else {
            $referencia = trim($referencia);
        }
        $length = strlen($referencia);
        if ($length < $total) {
            while ($length != $total) {
                $referencia = $caracter . $referencia;
                $length++;
            }
        } else {
            $referencia = substr($referencia, 0, $total);
        }
        return $referencia;
    }

    /**
     * Pone espacios a la derecha, indicado para Cadenas.
     */
    function llenarConEspaciosDerecha($cadena, $cantTotal)
    {
        if (!$cadena) {
            $cadena = "";
        } /*else {
            $cadena = clean($cadena);
        }*/
        $ret = "";
        $length = strlen($cadena);
        $poner = $cantTotal - $length;
        $i = $poner;
        if ($i > 0) {
            while ($poner > 0) {
                $ret .= " ";
                $poner--;
            }
            $ret = $cadena . $ret;
        } else {
            if ($i == 0) {
                $ret = $cadena;
            } else {
                $ret = substr($cadena, 0, $cantTotal);
            }
        }
        return $ret;
    }

    function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars. 
        return preg_replace('/-+/', ' ', $string); // Replaces multiple hyphens with single one.
    }
}

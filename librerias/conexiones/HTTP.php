<?php

namespace conexiones;

class HTTP {

    public static function getExtensionMimeType($mimeType) {
        switch ($mimeType) {
            case 'application/pdf': return '.pdf';
            case 'text/plain': return '.txt';
            case 'image/jpeg': return '.jpg';
            case 'video/avi': return '.avi';
            case 'text/css': return '.css';
            case 'application/x-compressed': return '.gz';
            case 'application/x-gzip': return '.gzip';
            case 'multipart/x-gzip': return '.gzip';
            case 'text/html': return '.html';
            case 'application/x-ima': return '.ima';
            case 'application/x-httpd-imap': return '.imap';
            case 'application/javascript' : return '.js';
            case 'audio/mpeg': return '.mp2';
            case 'audio/mpeg3': return '.mp3';
            case 'video/mpeg': return'mpg';
            case 'application/zip': return '.zip';
            case 'image/x-icon': return '.ico';
            case 'image/gif': return '.gif';
            case 'image/bmp': return '.bmp';
            case 'image/png': return '.png';
            case 'image/x-quicktime': return '.qif';
            case 'image/x-quicktime': return '.qti';
            case 'image/x-quicktime': return '.qtif';
            case 'image/tiff': return '.tif';
            case 'tiff': return '.image/tiff';
        }
    }

    public static function getMimeTypeExtension($documento) {
        $temp = explode('.', $documento);
        $tipo = strtolower((isset($temp) && is_array($temp) && isset($temp[count($temp) - 1])) ? $temp[count($temp) - 1] : 'text');
        switch ($tipo) {
            case 'pdf': return 'application/pdf';
            case 'txt':
            case 'text':
            case 'sdml':
            case 'pl':
            case 'mar':
            case 'm': return 'text/plain';
            case 'jfif':
            case 'jfif-tbnl':
            case 'jpe':
            case 'jpeg':
            case 'jpg': return 'image/jpeg';
            case 'avi': return 'video/avi';
            case 'css': return 'text/css';
            case 'gz': return 'application/x-compressed';
            case 'gz': return 'application/x-gzip';
            case 'gzip': return 'application/x-gzip';
            case 'gzip': return 'multipart/x-gzip';
            case 'htm': return 'text/html';
            case 'html': return 'text/html';
            case 'htmls': return 'text/html';
            case 'htx': return 'text/html';
            case 'ima': return 'application/x-ima';
            case 'imap': return 'application/x-httpd-imap';
            case 'js': return 'application/x-javascript';
            case 'js': return 'application/javascript';
            case 'js': return 'application/ecmascript';
            case 'js': return 'text/javascript';
            case 'js': return 'text/ecmascript';
            case 'm1v':
            case 'm2a':
            case 'm2v':
            case 'mp2': return 'audio/mpeg';
            case 'mp3': return 'audio/mpeg3';
            case 'mpeg':
            case 'mpg': return 'video/mpeg';
            case 'mpg': return 'audio/mpeg';
            case 'mpga': return 'audio/mpeg';
            case 'ppt': return 'application/powerpoint';
            case 'xls': return 'application/excel';
            case 'xml': return 'application/xml';
            case 'zip': return 'application/x-compressed';
            case 'zip': return 'application/x-zip-compressed';
            case 'zip': return 'application/zip';
            case 'zip': return 'multipart/x-zip';
            case 'zoo': return 'application/octet-stream';
            case 'zsh': return 'text/x-script.zsh';
            case 'ico': return 'image/x-icon';
            case 'gif': return 'image/gif';
            case 'bm': return 'image/bmp';
            case 'bmp': return 'image/bmp';
            case 'png': return 'image/png';
            case 'qif': return 'image/x-quicktime';
            case 'qti': return 'image/x-quicktime';
            case 'qtif': return 'image/x-quicktime';
            case 'tif': return 'image/tiff';
            case 'tiff': return 'image/tiff';
            case 'tif': return 'image/x-tiff';
            case 'tiff': return 'image/x-tiff';
        }
        return 'text/plain';
    }

    /**
     * Determina si el documento que se le pasa es una imagen o no por nombre
     * @param type $nombre nombre del archivo con extensión, si es != null sobre escribe el contentType
     */
    public static function is_image($nombre) {
        $temp = explode('.', $nombre);
        $tipo = strtolower((isset($temp) && is_array($temp) && isset($temp[count($temp) - 1])) ? $temp[count($temp) - 1] : 'text');
        $contentType = HTTP::getMimeTypeExtension($tipo);
        //Aunque a priori todos los content type de las imágenes empiezan por image, no podemos asegurar 100% que siempre sea así por lo que evaluaremos caso por caso
        //Además de esta forma nos aseguramos que solo serán imágenes aquellas que agregremos a esta lista...
        switch ($contentType) {
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
            case 'image/png':
            case 'image/x-icon':
            case 'image/gif':
            case 'image/bmp':
            case 'image/png':
            case 'image/x-quicktime':
            case 'image/x-quicktime':
            case 'image/x-quicktime':
            case 'image/tiff':
                return true;
        }
        return false;
    }

}

/**/

/**/
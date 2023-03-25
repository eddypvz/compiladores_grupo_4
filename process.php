<?php

// Para subir archivos
if (!empty($_GET['operation']) && $_GET['operation'] === 'fileUpload') {

    // Obtengo el nombre del archivo subido
    $filename = $_FILES['file']['name'];

    // Ubicación temporal para guardar el archivo
    $location = "tmp/{$filename}";

    // muevo el archivo del temporal de php hacia mi temporal
    if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {

        // abro el archivo
        $handle = fopen($location, "r");

        // leo su contenido y cierro el archivo
        $contents = fread($handle, filesize($location));
        fclose($handle);

        // si el archivo existe, lo elimino
        if (file_exists($location)) unlink($location);

        // imprimo el contenido del archivo con cabeceras de texto plano
        header("Content-Type: text/plain");
        print $contents;
    }
    else {
        // si no lo pude mover, imprimo un error
        print 'Error al cargar archivo';
    }

    // detengo la ejecución del script al terminar esta operación
    die();
}

if (!empty($_GET['operation']) && $_GET['operation'] === 'loadDemo') {

    $location = "CEMBM.cembm";
    $handle = fopen($location, "r");

    // leo su contenido y cierro el archivo
    $contents = fread($handle, filesize($location));
    fclose($handle);

    // imprimo el contenido del archivo con cabeceras de texto plano
    header("Content-Type: text/plain");
    print $contents;

    die();
}

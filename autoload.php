<?php
    if(!session_id()) {
        session_start();
    }
    spl_autoload_register(function ($class) {
        // Remplacez "MonNamespace\\" par le namespace de vos classes si vous en utilisez un
        $name_space = "Memory\\";

        // Détermine le chemin vers le fichier de la classe à partir du namespace et du nom de la classe
        $base_dir = __dir__ . "/inc/classes/";
        $relative_class = substr($class, strlen($name_space));
        $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";

        // Charge le fichier de la classe s'il existe
        if(file_exists($file)) {
            require $file;
        }
    });

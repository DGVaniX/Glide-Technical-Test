<?php
    spl_autoload_register("autoLoader");

    function autoLoader($className){
        $classesPath = "classes/";
        $extension = ".class.php";
        $fullPath = $classesPath.$className.$extension;
        if(file_exists($fullPath)){
            include_once $fullPath;
        }
    }
?>
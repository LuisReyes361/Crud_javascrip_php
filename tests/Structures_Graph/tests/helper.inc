<?php
if ('C:\Users\LUIS MARIO\OneDrive\Escritorio\CRUD_BD_R_NR\pear' == '@'.'php_dir'.'@') {
    // This package hasn't been installed.
    // Adjust path to ensure includes find files in working directory.
    set_include_path(dirname(dirname(__FILE__))
        . PATH_SEPARATOR . dirname(__FILE__)
        . PATH_SEPARATOR . get_include_path());
}

require_once 'Structures/Graph.php';

<?php

session_start();

if(empty($_SESSION['is_admin']) || $_SESSION['is_admin'] == false):
    die("Erro: Acesso negado!");
endif;

?>
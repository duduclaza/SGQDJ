<?php
require_once __DIR__ . '/init.php';

$u = getUsuarioLogado();
$data = getMockData();
$homologacoes = $data['homologacoes'];

$title = "Monitorar Entregas - Homologações 2.0";
$viewFile = __DIR__ . '/views/monitoramento.php';
require_once __DIR__ . '/../views/layouts/main.php';
?>

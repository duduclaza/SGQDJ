<?php
// Layout simples para páginas públicas que já possuem sua própria estrutura HTML
if (isset($viewFile) && file_exists($viewFile)) {
    require_once $viewFile;
} else {
    echo "<h1>Erro: View não encontrada.</h1>";
}

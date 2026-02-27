<?php

/**
 * Limpa arquivos JSON antigos (mais de 30 dias).
 *
 * Uso:
 *   php scripts/cleanup_old_json_files.php [diretorio] [dias] [--dry-run]
 *
 * Exemplos:
 *   php scripts/cleanup_old_json_files.php
 *   php scripts/cleanup_old_json_files.php storage/chat 30
 *   php scripts/cleanup_old_json_files.php storage/formularios 30 --dry-run
 */

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Erro: não foi possível localizar a raiz do projeto.\n");
    exit(1);
}

$inputDir = $argv[1] ?? 'storage';
$days = isset($argv[2]) ? (int)$argv[2] : 30;
$dryRun = in_array('--dry-run', $argv, true);

if ($days <= 0) {
    fwrite(STDERR, "Erro: quantidade de dias inválida.\n");
    exit(1);
}

$targetDir = $inputDir;
if (!preg_match('/^[A-Za-z]:\\\\|^\//', $inputDir)) {
    $targetDir = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inputDir);
}

$targetDir = realpath($targetDir);
if ($targetDir === false || !is_dir($targetDir)) {
    fwrite(STDERR, "Erro: diretório não encontrado.\n");
    exit(1);
}

$cutoff = time() - ($days * 86400);

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($targetDir, FilesystemIterator::SKIP_DOTS)
);

$totalJson = 0;
$deleted = 0;
$kept = 0;
$errors = 0;

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    if (strtolower($fileInfo->getExtension()) !== 'json') {
        continue;
    }

    $totalJson++;
    $path = $fileInfo->getPathname();
    $mtime = $fileInfo->getMTime();

    if ($mtime > $cutoff) {
        $kept++;
        continue;
    }

    if ($dryRun) {
        echo "[DRY-RUN] Apagaria: {$path}\n";
        $deleted++;
        continue;
    }

    if (@unlink($path)) {
        echo "Apagado: {$path}\n";
        $deleted++;
    } else {
        echo "Erro ao apagar: {$path}\n";
        $errors++;
    }
}

echo "\nResumo:\n";
echo "Diretório: {$targetDir}\n";
echo "Dias limite: {$days}\n";
echo "Arquivos JSON encontrados: {$totalJson}\n";
echo "Arquivos removidos" . ($dryRun ? " (simulação)" : "") . ": {$deleted}\n";
echo "Arquivos mantidos: {$kept}\n";
echo "Erros: {$errors}\n";

exit($errors > 0 ? 2 : 0);

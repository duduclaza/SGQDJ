<?php
/**
 * CLI Script para gerar áudios do tour usando Eleven Labs
 * Execute no terminal: php scripts/gerar-audios-cli.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Carregar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$apiKey = $_ENV['ELEVENLABS_API_KEY'] ?? '';
$voiceId = $_ENV['ELEVENLABS_VOICE_ID'] ?? 'pFZP5JQG7iQjIQuC4Bku'; // Lily ou similar
$modelId = 'eleven_multilingual_v2'; // Modelo multilíngue v2 para melhor português

if (empty($apiKey)) {
    die("❌ ERRO: ELEVENLABS_API_KEY não encontrada no .env\n");
}

// Caminho de saída (adaptado para estrutura de pastas do user)
$outputDir = __DIR__ . '/../public/audio/tour-nc';

echo "=== Gerador de Áudios Eleven Labs (CLI) ===\n";
echo "Voz ID: $voiceId\n";
echo "Output: $outputDir\n\n";

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "📁 Diretório criado: $outputDir\n";
}

// Textos do tour (ID => Texto)
$steps = [
    1 => "Módulo de Não Conformidades. Aqui você gerencia todas as ocorrências de qualidade da empresa. Vamos conhecer as principais funcionalidades!",
    2 => "Nova Ocorrência. Este botão abre o formulário para registrar uma nova não conformidade. Vamos abrir para você ver os campos!",
    3 => "Formulário de Nova Não Conformidade. Aqui você preenche os dados da ocorrência: título, descrição detalhada, seleciona o responsável e pode anexar fotos como evidência.",
    4 => "Campos Importantes. Título é o resumo claro da ocorrência. Descrição são os detalhes completos. Responsável é quem vai resolver. E anexos são as fotos de evidência.",
    5 => "Fechando o Formulário. Vamos fechar o formulário e continuar conhecendo as abas de status.",
    6 => "Aba Pendentes. Não conformidades recém registradas que aguardam início do tratamento.",
    7 => "Aba Em Andamento. Não conformidades que estão sendo tratadas. O responsável registra ações corretivas.",
    8 => "Aba Solucionadas. Histórico de não conformidades resolvidas. Ótimo para consultas e auditorias!",
    9 => "Botão de Ajuda. Sempre que precisar, clique aqui para ver este tutorial novamente. Bom trabalho!",
];

$gerados = 0;
$erros = 0;
$pulados = 0;

foreach ($steps as $step => $texto) {
    $filename = "step-{$step}.mp3";
    $filepath = "{$outputDir}/{$filename}";
    
    echo "Processing Step {$step}...\n";
    
    // Verificar se já existe
    if (file_exists($filepath) && filesize($filepath) > 0) {
        echo "   ⏭️ Arquivo já existe. Pulando.\n";
        $pulados++;
        continue;
    }
    
    echo "   🎤 Gerando áudio via API...\n";
    
    $ch = curl_init("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}");
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Accept: audio/mpeg',
            'Content-Type: application/json',
            'xi-api-key: ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'text' => $texto,
            'model_id' => $modelId,
            'voice_settings' => [
                'stability' => 0.5,
                'similarity_boost' => 0.75,
                'style' => 0.0,
                'use_speaker_boost' => true,
            ],
        ]),
        CURLOPT_TIMEOUT => 30, // Timeout maior para geração
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $bytes = file_put_contents($filepath, $response);
        $sizeKB = round($bytes / 1024, 1);
        echo "   ✅ Sucesso! Salvo em $filename ($sizeKB KB)\n";
        $gerados++;
    } else {
        echo "   ❌ Erro na API (HTTP $httpCode)\n";
        if ($curlError) echo "   Curl Error: $curlError\n";
        echo "   Response: " . substr($response, 0, 100) . "...\n"; // Mostrar inicio do erro
        $erros++;
    }
    
    // Pequena pausa para evitar rate limit agressivo
    usleep(500000); // 0.5s
}

echo "\nCompleted!\n";
echo "Gerados: $gerados\n";
echo "Pulados: $pulados\n";
echo "Erros:   $erros\n";

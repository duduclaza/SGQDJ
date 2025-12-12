<?php
/**
 * Gera áudios do tour usando Eleven Labs
 * Acesse: /admin/gerar-audios-tour (apenas super_admin)
 */

// Verificar se está logado e é super_admin
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    http_response_code(403);
    die('❌ Acesso negado. Apenas super_admin pode executar este script.');
}

// Carregar .env
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$apiKey = $_ENV['ELEVENLABS_API_KEY'] ?? '';
$voiceId = $_ENV['ELEVENLABS_VOICE_ID'] ?? 'pFZP5JQG7iQjIQuC4Bku';
$modelId = 'eleven_multilingual_v2';

if (empty($apiKey)) {
    die('❌ ELEVENLABS_API_KEY não encontrada no .env');
}

$outputDir = __DIR__ . '/audio/tour-nc';

// Textos do tour
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

header('Content-Type: text/html; charset=utf-8');
echo "<html><head><title>Gerador de Áudios - Tour NC</title></head><body style='font-family:monospace;padding:20px;background:#1a1a2e;color:#eee;'>";
echo "<h2>🎤 Gerador de Áudios do Tour - Eleven Labs</h2>";

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "<p>📁 Diretório criado: $outputDir</p>";
}

$gerados = 0;
$erros = 0;

foreach ($steps as $step => $texto) {
    $filename = "step-{$step}.mp3";
    $filepath = "{$outputDir}/{$filename}";
    
    if (file_exists($filepath)) {
        echo "<p>⏭️ Step {$step}: <span style='color:#ffd700;'>Já existe, pulando...</span></p>";
        continue;
    }
    
    echo "<p>🎤 Step {$step}: Gerando áudio... ";
    flush();
    
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
                'style' => 0.3,
                'use_speaker_boost' => true,
            ],
        ]),
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        file_put_contents($filepath, $response);
        $size = round(strlen($response) / 1024, 1);
        echo "<span style='color:#00ff00;'>✅ Salvo ({$size} KB)</span></p>";
        $gerados++;
    } else {
        echo "<span style='color:#ff4444;'>❌ Erro (HTTP {$httpCode})</span></p>";
        $erros++;
    }
    
    usleep(500000);
}

echo "<hr>";
echo "<h3>📊 Resumo</h3>";
echo "<p>✅ Gerados: {$gerados}</p>";
echo "<p>❌ Erros: {$erros}</p>";
echo "<p>📁 Arquivos em: /audio/tour-nc/</p>";

if ($gerados > 0) {
    echo "<p style='color:#00ff00;font-size:1.2em;'>🎉 Áudios gerados com sucesso! O tour agora tem voz natural.</p>";
}

echo "</body></html>";

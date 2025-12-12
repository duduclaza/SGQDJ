<?php
/**
 * Script para gerar áudios do tour usando Eleven Labs
 * EXECUTAR APENAS UMA VEZ para gerar os arquivos MP3
 * 
 * Uso: php gerar-audios-tour.php
 */

// Carregar variáveis de ambiente
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$apiKey = $_ENV['ELEVENLABS_API_KEY'] ?? '';
// Rachel - voz feminina padrão do Eleven Labs (sempre disponível)
// Forçando Rachel pois a voz do .env pode não estar disponível
$voiceId = '21m00Tcm4TlvDq8ikWAM';
$modelId = 'eleven_multilingual_v2';

echo "API Key: " . substr($apiKey, 0, 15) . "...\n";
echo "Voice ID: $voiceId\n\n";

if (empty($apiKey)) {
    die("❌ ELEVENLABS_API_KEY não encontrada no .env\n");
}

$outputDir = __DIR__ . '/../public/audio/tour-nc';

// Textos do tour - MANTENHA CURTO para economizar créditos!
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

echo "=== Gerador de Áudios do Tour (Eleven Labs) ===\n\n";

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "📁 Diretório criado: $outputDir\n";
}

$totalGerados = 0;
$totalErros = 0;

foreach ($steps as $step => $texto) {
    $filename = "step-{$step}.mp3";
    $filepath = "{$outputDir}/{$filename}";
    
    // Pular se já existe
    if (file_exists($filepath)) {
        echo "⏭️  Step {$step}: Já existe, pulando...\n";
        continue;
    }
    
    echo "🎤 Step {$step}: Gerando áudio... ";
    
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
        echo "✅ Salvo ({$size} KB)\n";
        $totalGerados++;
    } else {
        echo "❌ Erro (HTTP {$httpCode})\n";
        echo "   Resposta: $response\n";
        $totalErros++;
    }
    
    // Pequena pausa entre requisições
    usleep(500000);
}

echo "\n=== Resumo ===\n";
echo "✅ Gerados: {$totalGerados}\n";
echo "❌ Erros: {$totalErros}\n";
echo "📁 Arquivos em: {$outputDir}\n";

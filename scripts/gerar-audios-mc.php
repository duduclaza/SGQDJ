<?php
/**
 * CLI Script para gerar áudios do tour Melhoria Contínua usando Eleven Labs
 * Execute no terminal: php scripts/gerar-audios-mc.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Carregar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$apiKey = $_ENV['ELEVENLABS_API_KEY'] ?? '';
$voiceId = $_ENV['ELEVENLABS_VOICE_ID'] ?? 'pFZP5JQG7iQjIQuC4Bku';
$modelId = 'eleven_multilingual_v2';

if (empty($apiKey)) {
    die("❌ ERRO: ELEVENLABS_API_KEY não encontrada no .env\n");
}

$outputDir = __DIR__ . '/../public/audio/tour-mc';

echo "=== Gerador de Áudios Eleven Labs - Tour Melhoria Contínua ===\n";
echo "Voz ID: $voiceId\n";
echo "Output: $outputDir\n\n";

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "📁 Diretório criado: $outputDir\n";
}

// Textos do tour Melhoria Contínua (ID => Texto)
$steps = [
    1 => "Módulo Melhoria Contínua 2.0. Bem-vindo ao módulo de Melhoria Contínua! Aqui você pode registrar ideias de melhoria, acompanhar seu status e contribuir para a evolução da empresa. Vamos conhecer as funcionalidades!",
    2 => "Nova Melhoria. Clique neste botão para registrar uma nova ideia de melhoria. Qualquer colaborador pode sugerir melhorias para processos, produtos ou ambiente de trabalho!",
    3 => "Formulário de Nova Melhoria. Este é o formulário para cadastrar sua ideia. Você vai preencher título, descrição, resultado esperado e o plano de ação seguindo a metodologia 5W2H.",
    4 => "Metodologia 5W2H. O formulário usa a metodologia 5W2H: O que, Como, Onde, Por que, Quando e Quanto custa. Isso ajuda a estruturar bem a sua ideia e facilita a análise pelos gestores.",
    5 => "Fechando o Formulário. Vamos fechar o formulário e conhecer os outros recursos do módulo.",
    6 => "Filtros Avançados. Use os filtros para encontrar melhorias específicas. Você pode filtrar por departamento, status, idealizador, pontuação e período.",
    7 => "Tabela de Melhorias. Aqui você visualiza todas as melhorias cadastradas. As colunas mostram: Data do registro, Departamento, Título, Descrição, Resultado Esperado, Status atual, Idealizador da ideia, quem Criou o registro, Responsáveis pela execução, Data Prevista e as Ações disponíveis.",
    8 => "Botão de Ajuda. Sempre que precisar, clique aqui para ver este tutorial novamente. Continue sugerindo melhorias e contribuindo para a evolução contínua!",
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
        CURLOPT_TIMEOUT => 30,
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
        echo "   Response: " . substr($response, 0, 100) . "...\n";
        $erros++;
    }
    
    // Pequena pausa para evitar rate limit
    usleep(500000); // 0.5s
}

echo "\n=== Completo! ===\n";
echo "Gerados: $gerados\n";
echo "Pulados: $pulados\n";
echo "Erros:   $erros\n";

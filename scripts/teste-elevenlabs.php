<?php
/**
 * Teste de conexão com Eleven Labs
 */

// Carregar .env manualmente
$envFile = __DIR__ . '/../.env';
$apiKey = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($key === 'ELEVENLABS_API_KEY') {
                $apiKey = $value;
            }
        }
    }
}

echo "=== Teste Eleven Labs API ===\n\n";

if (empty($apiKey)) {
    echo "❌ API Key não encontrada no .env\n";
    echo "Caminho testado: $envFile\n";
    exit(1);
}

echo "✅ API Key encontrada: " . substr($apiKey, 0, 15) . "...\n";
echo "   Tamanho: " . strlen($apiKey) . " caracteres\n\n";

// Testar API - listar vozes (endpoint simples)
echo "🔍 Testando conexão com API...\n";

$ch = curl_init("https://api.elevenlabs.io/v1/voices");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'xi-api-key: ' . $apiKey,
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $voiceCount = count($data['voices'] ?? []);
    echo "✅ Conexão OK! $voiceCount vozes disponíveis.\n\n";
    
    // Mostrar algumas vozes
    echo "Vozes disponíveis (primeiras 5):\n";
    $vozes = array_slice($data['voices'] ?? [], 0, 5);
    foreach ($vozes as $voz) {
        echo "   - {$voz['name']} (ID: {$voz['voice_id']})\n";
    }
} else {
    echo "❌ Erro na API!\n";
    echo "Resposta: $response\n";
}

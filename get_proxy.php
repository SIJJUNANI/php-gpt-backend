<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ✅ Get OpenAI API key from environment variable (set in Render dashboard)
$apiKey = getenv("OPENAI_API_KEY");

// ✅ Get user message from frontend
$input = json_decode(file_get_contents("php://input"), true);
$question = trim($input["message"] ?? '');

if (!$question) {
    echo json_encode(["error" => "No message provided"]);
    exit;
}

// ✅ Prepare request for OpenAI API
$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $question]
    ]
];

// ✅ Send HTTP request to OpenAI
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => "cURL error: " . curl_error($ch)]);
    exit;
}

curl_close($ch);

// ✅ Output OpenAI response to frontend
echo $response;

<?php
// --- FAKE KEY YOU PROVIDED ---
$apiKey = "AIzaSyBds22rq83Ag62Ro0v12A7k2OfaOzyl4OY";

if (!isset($_POST["message"])) {
    die("No message.");
}

$userMessage = $_POST["message"];

// Build request payload for Gemini generateContent
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $userMessage]
            ]
        ]
    ]
];

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . urlencode($apiKey);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    $reply = "cURL error: " . $error;
} else {
    curl_close($ch);
    $json = json_decode($response, true);

    // Try to extract text from the first candidate
    $reply = "No reply";

    if (isset($json["candidates"][0]["content"]["parts"][0]["text"])) {
        $reply = $json["candidates"][0]["content"]["parts"][0]["text"];
    } elseif (isset($json["error"]["message"])) {
        $reply = "API error: " . $json["error"]["message"];
    }
}

// Redirect back to the HTML with Gemini's reply in query string
$replyEncoded = urlencode($reply);
header("Location: index.html?reply=" . $replyEncoded);
exit;

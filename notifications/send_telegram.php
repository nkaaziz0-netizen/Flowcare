<?php

function sendTelegram($phone, $message)
{
    $botToken = //"8499160571:AAEORnv-uJn5_bxzBk9ZbBx0UZyCpK4I9Lo";
    $chatId = //"909312713";

  $url = "https://api.telegram.org/bot".$botToken."/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);

    try {

        file_get_contents($url, false, $context);

        return true;

    } catch (Exception $e) {

        die($e->getMessage());
        return false;
    }
}
?>
<?php
require 'functions.php';

$update = json_decode(file_get_contents("php://input"), true);
$message = $update["message"];
$text = $message["text"];
$cid = $message["chat"]["id"];
$uid = $message["from"]["id"];

if ($uid != $admin) exit();

if ($text == "/admin") {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "Admin paneliga xush kelibsiz!",
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => "📊 Statistika"], ['text' => "📢 Xabar yuborish"]],
                [['text' => "➕ Savol qo‘shish"]],
            ],
            'resize_keyboard' => true
        ])
    ]);
}
?>

<?php
require 'functions.php';

$update = json_decode(file_get_contents("php://input"), true);
$message = $update["message"] ?? null;
if (!$message) exit;

$text = trim($message["text"] ?? '');
$cid = $message["chat"]["id"];
$name = $message["from"]["first_name"] ?? '';
$uid = $message["from"]["id"];

$users = load('data/users.json');
if (!isset($users[$uid])) {
    $users[$uid] = ["step"=>0,"score"=>0,"current_day"=>null];
    save('data/users.json', $users);
}

if ($text == "/start") {
    bot('sendMessage', [
        'chat_id'=>$cid,
        'text'=>"🤖 Salom, $name! Zakovat botiga xush keldingiz.\n\n📌 “1-kun” deb yozing yoki “Reyting”ni tanlang.",
        'reply_markup'=>json_encode(['keyboard'=>[[['text'=>"1-kun"]], [['text'=>"Reyting"]]], 'resize_keyboard'=>true])
    ]);
    exit;
}

if ($text == "Reyting") {
    $list=[];
    foreach ($users as $id=>$u) $list[]=['id'=>$id,'score'=>$u['score']];
    usort($list, fn($a,$b)=>$b['score']-$a['score']);
    $msg="🏆 Top 10 Reyting:\n";
    foreach (array_slice($list,0,10) as $i=>$u) $msg .= ($i+1).". ".$u['score']." ball\n";
    bot('sendMessage',['chat_id'=>$cid,'text'=>$msg]);
    exit;
}

if ($text == "1-kun") {
    $day=1; $qfile="data/questions/$day.json";
    $data = load($qfile);
    if (!isset($data['questions'])) {
        bot('sendMessage',['chat_id'=>$cid,'text'=>"❌ Hali 1-kun savollari tayyor emas."]);
        exit;
    }
    $step=$users[$uid]['step'];
    if ($users[$uid]['current_day']!==$day) {
        $users[$uid]['current_day']=$day; $users[$uid]['step']=0; $users[$uid]['score']=0;
        save('data/users.json',$users);
        $step=0;
    }
    if (isset($data['questions'][$step])) {
        bot('sendMessage',['chat_id'=>$cid,'text'=>$data['questions'][$step]]);
    } else {
        bot('sendMessage',['chat_id'=>$cid,'text'=>"✅ 1-kun tugadi.\nSiz: {$users[$uid]['score']} / ".count($data['questions']).""]);
        $users[$uid]['current_day']=null;
        save('data/users.json',$users);
    }
    exit;
}

if ($users[$uid]['current_day']) {
    $day = $users[$uid]['current_day'];
    $data = load("data/questions/$day.json");
    $step = $users[$uid]['step'];
    $correct = $data['answers'][$step] ?? '';
    if (strtolower($text)==strtolower($correct)) {
        $users[$uid]['score']++;
    }
    $users[$uid]['step']++;
    save('data/users.json',$users);
    $next = $data['questions'][$users[$uid]['step']] ?? null;
    bot('sendMessage',['chat_id'=>$cid,'text'=>$next ? $next : "✅ 1-kun tugadi. Siz: {$users[$uid]['score']}"]);
    exit;
}
?>

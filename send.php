<?php
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false]); exit; }
$site    = trim($_POST['site'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$hp      = trim($_POST['company'] ?? '');
if ($hp !== '') { echo json_encode(['ok'=>true]); exit; } // honeypot — молча
if ($site === '' || $contact === '') { echo json_encode(['ok'=>false,'err'=>'empty']); exit; }
$site    = mb_substr($site, 0, 300);
$contact = mb_substr($contact, 0, 200);
$clean = function($s){ return str_replace(["\r","\n"], ' ', $s); };
$to      = 'hello@law-agent.ru';
$subject = 'Заявка с сайта law-agent.ru';
$body  = "Новая заявка на бесплатный аудит\n\n";
$body .= "Сайт: "    . $clean($site)    . "\n";
$body .= "Контакт: " . $clean($contact) . "\n";
$body .= "Время: "   . date('Y-m-d H:i:s') . "\n";
$body .= "IP: "      . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n";
$body .= "UA: "      . ($_SERVER['HTTP_USER_AGENT'] ?? '');
$headers  = "From: Заявки law-agent <hello@law-agent.ru>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$encSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$ok = @mail($to, $encSubject, $body, $headers);
echo json_encode(['ok' => (bool)$ok]);

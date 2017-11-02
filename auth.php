<?php

require __DIR__.'/vendor/autoload.php';

use Pusher\Pusher;

$options = array(
    'cluster' => PUSHER_CLUSTER,
    'encrypted' => true
);
$pusher = new Pusher(
    PUSHER_KEY,
    PUSHER_SECRET,
    PUSHER_ID,
    $options
);

$user_id = uniqid();
$presence_data = array('user_id' => $user_id);
$channel_name = $_POST['channel_name'];
$socket_id = $_POST['socket_id'];

$auth = $pusher->presence_auth($channel_name, $socket_id, $user_id, $presence_data);

header('Content-Type: application/json');
echo($auth);
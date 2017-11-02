<?php

require __DIR__.'/vendor/autoload.php';

use \DateTime;
use Pusher\Pusher;

if(!isset($_SESSION)) session_start();

if($_SESSION['csrf'] === $_POST['csrf'])
{
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

    switch($_POST['type'])
    {
        case "squirl":
            $data['squirlId'] = $_POST['squirlId'];
            $data['socketId'] = $_POST['socketId'];
            $pusher->trigger('presence-channel', 'squirl-event', $data);
        break;

        case "chat":
            $date = new DateTime();
            $data['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
            $data['message'] = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
            $data['time'] = $date->format("H:i:s");
            $data['socketId'] = $_POST['socketId'];
            $pusher->trigger('presence-channel', 'chat-event', $data);
        break;

        case "select":
            $data['acorns'] = $_POST['data'];
            $pusher->trigger('presence-channel', 'select-event', $data);
        break;
    }

    echo "true";
    exit();
}
echo "false";
exit();

?>
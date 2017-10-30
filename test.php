<?php

    require __DIR__ . '/vendor/autoload.php';

    use Pusher\Pusher;

    if(!isset($_SESSION)) session_start();

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
        case "chat":
            $data['message'] = $_POST['name'].": ".$_POST['message'];
            $pusher->trigger('my-channel', 'my-event', $data);
        break;

        case "select":
            $data['acorns'] = $_POST['data'];
            $pusher->trigger('my-channel', 'select-event', $data);
        break;
    }

    echo "true";

?>
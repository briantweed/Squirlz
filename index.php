<?php

    require __DIR__ . '/vendor/autoload.php';
    
    if(!isset($_SESSION)) session_start();

    unset($_SESSION['csrf']);
    $_SESSION['csrf'] = md5(date("H:i")."-".basename($_SERVER['PHP_SELF'])."-".date("U"));

?>


<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">

        <title>Squirlz</title>

        <meta name="subject" content="Squirlz">
        <meta name="abstract" content="Collect all the acorns, just don't chose the same acorn as a fellow squirl">
        <meta name="description" content="Multiplayer co-op game using webhooks where each player has to select a unique acorn or it's game over.">

        <meta name="url" content="http://brtweed.co.uk/squirlz">
        <meta name="rating" content="General">
        <meta name="referrer" content="no-referrer">
        <meta name="directory" content="submission">
        <meta name="coverage" content="Worldwide">
        <meta name="distribution" content="Global">
        <meta name="robots" content="index,follow,noodp">
        <meta name="googlebot" content="index,follow">
        <meta name="google" content="nositelinkssearchbox">
        <meta name="google" value="notranslate">
        <meta name="format-detection" content="telephone=no">
        <meta name="_token" content="<?php echo $_SESSION['csrf'] ?>">
        <meta name="_key" content="<?php echo PUSHER_KEY ?>">
        <meta name="_cluster" content="<?php echo PUSHER_CLUSTER ?>">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">

        <link rel="author" href="humans.txt">

        <link rel="prefetch" href="images/background.jpg">
        <link rel="prefetch" href="images/square.png">
        <link rel="prefetch" href="images/empty.png">
        <link rel="prefetch" href="css/styles.css?v=<?php echo VERSION ?>">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <link rel="stylesheet" href="styles.css?v=<?php echo VERSION ?>">

    </head>

    <body>

        <div id="wrapper" class="container-fluid">
            
            <div class="clearfix"><br><br></div>

            <div class="row">
                <div class="col-12">
                    <h1 class="text-center">Squirlz</h1>
                    <h2 class="text-center">Get some nutz</h2>
                </div>
            </div>  

            <div class="clearfix"><br><br></div>

            <div class="row">
                <div v-for="squirl in squirlz" :class="squirl.class">
                    <div class="card">
                        <img class="rounded img-fluid pb-2" :src="squirl.image" alt="">
                        <h3 class="text-primary text-center">{{squirl.name}}</h3>
                    </div>
                </div>
            </div>

            <div class="clearfix"><br><br></div>

            <div class="row">
                <div class="grid offset-1 col-5">
                    <div v-for="acorn in acorns" class="acorn">
                        <span v-if="acorn.active"><img :num="acorn.number" src="images/square.png" class="img-fluid" v-on:click="toggle(acorn)"></span>
                        <span v-else><img :num="acorn.number" src="images/empty.png" class="img-fluid" v-on:click="toggle(acorn)"></span>
                    </div>
                </div>
                <div class="col-5">
                    <div class="card">
                        <ul>
                            <li>Each player selects an available acorn from the grid</li>
                            <li>If every player selects a unique acorn then the nuts are removed and the game continues</li>
                            <li>If one or more players select the same acorn then its game over</li>
                            <li>If one or more players hasn't made a selection when the timer runs out then its game over</li>
                            <li>Players win when all the acorns have been removed</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="clearfix"><br><br></div>

            <div class="row">
                <div class="offset-1 col-3">
                    <div class="card">
                        <form id="messageForm">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" aria-describedby="name" placeholder="Enter name ...">
                            </div>
                            <div class="form-group">
                                <label for="message">Message:</label>
                                <textarea class="form-control" id="message" name="message" aria-describedby="message" rows="5" placeholder="Enter message ..."></textarea>
                            </div>
                            <button class="float-right btn btn-primary" onClick="addMessage()">add</button>
                        </form>
                    </div>
                </div>
                <div class="col-7">
                    <div class="card">
                        <ul id="playerList"></ul>
                            <li v-for="message in messages">
                                {{ message }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="clearfix"><br></div>



        </div>
        <!-- /.container-fluid -->

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue"></script>
        <script type="text/javascript" src="https://js.pusher.com/4.1/pusher.min.js"></script>
        <script type="text/javascript" src="script.js?v=<?php echo VERSION ?>"></script>

    </body>

</html>
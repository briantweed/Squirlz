<?php

    require __DIR__.'/vendor/autoload.php';
    
    if(!isset($_SESSION)) session_start();

    unset($_SESSION['csrf']);
    $_SESSION['csrf'] = md5(date("H:i")."-".basename($_SERVER['PHP_SELF'])."-".date("U"));

?>


<!DOCTYPE html>


<html lang="en">


    <head>

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

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

        <link rel="prefetch" href="css/styles.css?v=<?php echo VERSION ?>" rel="stylesheet">
        <link rel="prefetch" href="images/background.jpg">
        <link rel="prefetch" href="images/square.png">
        <link rel="prefetch" href="images/empty.png">

        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" rel="stylesheet">
        <link href="css/styles.css?v=<?php echo VERSION ?>" rel="stylesheet">

    </head>



    <body>


        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Squirlz</a>
        </nav>



        <div id="wrapper" class="container-fluid">
            

            
            <section id="title-section">
                
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center">Squirlz</h1>
                        <h2 class="text-center">Get some nutz</h2>
                    </div>
                </div> 

            </section>



            <section id="character-section">
                
                <h3 class="text-center">Select a Squirl</h3>
                <div id="characters" class="row">
                    <div v-cloak v-for="(squirl, index) in squirlz" :class="squirl.class">
                        <div class="card" :data-num="index" v-bind:class="{'selected': mysquirl == squirl.name, 'locked': squirl.locked == true}">
                            <a href="javascript:void(0)" v-on:click="selectSquirl(squirl.name)">
                                <img class="rounded img-fluid pb-2" :src="squirl.image" alt="image of the squirrel">
                                <h4 class="text-primary text-center">{{ squirl.name }}</h4>
                            </a>
                        </div>
                    </div>
                </div>
                <br>
                <h5 v-cloak v-show="mysquirl" class="text-center">You have selected <b>{{ mysquirl }}</b></h5>
                <br v-show="mysquirl==''">

            </section>

           

            <section id="chat-section">
                
                <h3 class="text-center">Have a chat</h3>
                <div class="row">
                    <div class="offset-1 col-3">
                        <div class="card">
                            <form id="messageForm">
                                <div class="form-group">
                                    <label for="character">Name:</label>
                                    <input type="text" class="form-control" id="name" name="name" readonly :value="mysquirl"/>
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
                        <div class="card" style="height: 336px;overflow-y: auto">
                            <ul id="playerList">
                                <li v-cloak v-for="message in messages" >
                                    <img class="rounded" style="height:40px;width:40px;margin-right:7px" :src="message.image" alt="image of the squirrel"> <b>{{ message.name }}</b>: {{ message.message }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <br>

            </section>



            <section id="game-section">
                
                <h3 class="text-center">Select an acorn</h3>
                <br><br>
                <div class="row">
                    <div class="grid offset-4 col-4">
                        <div v-cloak v-for="acorn in acorns" class="acorn">
                            <span v-if="acorn.active"><img :num="acorn.number" src="images/square.png" alt="a square containing an acorn" class="img-fluid" v-on:click="toggle(acorn)"></span>
                            <span v-else><img :num="acorn.number" src="images/empty.png" alt="an empty square" class="img-fluid" v-on:click="toggle(acorn)"></span>
                        </div>
                    </div>
                </div>
                
            </section>



            <div class="clearfix"><br><br><br></div> 



        </div>
        <!-- /.container-fluid -->



        <footer class="text-center">
            &copy; brtweed.co.uk
        </footer>



        <script src="https://code.jquery.com/jquery-3.2.1.min.js" type="text/javascript" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js" type="text/javascript" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" type="text/javascript" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" type="text/javascript" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue" type="text/javascript"></script>
        <script src="https://js.pusher.com/4.1/pusher.min.js" type="text/javascript"></script>
        <script src="js/script.js?v=<?php echo VERSION ?>" type="text/javascript"></script>


    </body>


</html>
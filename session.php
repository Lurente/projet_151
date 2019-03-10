<?php
session_start();
    /*
    Page name : index.php
    Description : main page of the website
    Author : Luca Prudente
    */

      require_once('includes/config.php');

      if (isset($_GET['refreshonlyChat']))
        {
          $allMsg = $db->query("SELECT message, pseudo FROM chat INNER JOIN compte ON chat.id_compte = compte.id_compte ORDER BY id_chat ASC");
              while ($msg = $allMsg->fetch()) {
                echo "<b>".$msg['pseudo'] . " : </b>";
                echo $msg['message'];
                echo "<br><br>";
              }
              if(isset($_POST['refreshChat'])){
                try{
                  $requete = "SELECT * FROM chat ORDER BY id_chat ASC";
                  $refreshChat = $db->query($requete);

                  $_SESSION['allMsg'] = $refreshChat;

                }
                catch(PDOException $e){
                  die('Une erreur est survenue ! ' . $e->getMessage());
                  echo "string";
                }
              }
            die();
        }

        if (isset($_GET['refreshonlyPoints']))
          {
            $allPoints = $db->query("SELECT points FROM points INNER JOIN compte ON points.id_compte = compte.id_compte ORDER BY points ASC");
                while ($points = $allPoints->fetch()) {
                  echo "<b>".$points['pseudo'] . " : </b>";
                  echo $points['points'];
                  echo "<br><br>";
                }
            $personalPoints = $_SESSION['points'];
            echo "<p>Vos Points : $personalPoints place : $rank</p>";
          }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Doodle War - SESSION</title>
      <meta name="description" content="Description de la page" />
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="assets/scss/main.css">
      <link href="https://fonts.googleapis.com/css?family=Cabin+Sketch" rel="stylesheet">
    </head>
    <body>
      <div class="page-session">
        <!--gameBackground-->
          <div class="gameBackground">
            <!--textAccueil-->
              <div class="textAccueil">
                  <?php
                  $usrname = isset($_SESSION['pseudo'])?$_SESSION['pseudo']:'';
                  echo "Bienvenue dans votre session $usrname";
                  ?>
              </div>
            <!--/textAccueil-->
            <!--chat-->
              <div class="chat">
                  <div class="displayOrNotChat">
                    <div class="row">
                      <h1>Chat</h1>
                      <div class="col-md-5" id="inputRadiusChange">
                        <button title="réduire le chat" type="button" id="hideChat" onclick="hideChat()">&Hacek;</button>
                        <button title="afficher le chat" style="display:none" type="button" id="showChat" onclick="showChat()">^</button >
                      </div>
                    </div>
                  </div>
                  <div  class="chatOutput">
                    <div class="row">
                      <div class="col-md-5" id="chatDisplay">
                        <div id="messages">

                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="chatInput">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="col-md-2">
                          <p>chat</p>
                        </div>
                        <div class="col-md-8">
                          <form id="sendChat" action="traitement.php" method="post">
                            <input type="text" id="chat" name="chat" value="">
                            <input type="number" id="sendChat" value="1" hidden>
                          </form>
                        </div>
                        <div class="col-md-2">
                          <input type="button" id="envoi" value="send">
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            <!--/chat-->
            <!--game-->
            <div class="game">
              <div class="container">
                <div class="gameDisplay">
                  <div class="row">
                    <div class="frame" id="images">
                    </div>
                    <div class="question">
                      Guess this picture's name
                    </div>
                  </div>
                </div>
                <div class="choicesDisplay">
                  <div class="row">
                    <div class="col-md-4 firstChoice">
                      <?php

                      ?>
                    </div>
                    <div class="col-md-4 secondChoice">
                      <?php

                      ?>
                    </div>
                    <div class="col-md-4 thirdChoice">
                      <?php

                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--/game-->
            <!--points-->
              <div class="points">
                  <div class="displayOrNotPoint">
                    <div class="row">
                      <h1>Points</h1>
                      <div class="col-md-5">
                        <button title="réduire l'affichage des points" type="button" id="hidePoint" onclick="hidePoint()">&Hacek;</button>
                        <button title="afficher les points" style="display:none" type="button" id="showPoint" onclick="showPoint()">^</button >
                      </div>
                    </div>
                  </div>
                  <div  class="pointOutput">
                    <div class="row">
                      <div class="col-md-5" id="pointDisplay">
                        <div id="points">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="personalPoint">
                    <div class="row">
                      <div class="col-md-5">
                        <div id="personalPoints">

                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              </div>
            <!--/points-->
          </div>
        <!--/gameBackground-->
      </div>
      <script type="text/javascript">

        $('#envoi').click(function(e){
          e.preventDefault();

            var formCheck = true;
            var message = $('#chat').val();
            var sendChat = $('#sendChat').val();

            if (document.getElementById('chat').value == '') {
      				formCheck = false;
      			}

            if (formCheck) {
              $.ajax({
                  url : "traitement.php", // on donne l'URL du fichier de traitement
                  type : "POST", // la requête est de type POST
                  data : "message=" + message + "&sendChat=true",
                           // et on envoie nos données
                  success : function(html) {
                        chatRefresh(); // data came back ok, so display it
                        $('#chat').val('');
                    }
              });
            }
        });

        setInterval(chatRefresh, 2000);

        function chatRefresh() {
          $.ajax({
            url: 'session.php?refreshonlyChat',
            type: 'POST',
            success : function(html) {
              $('#messages').html(html); // data came back ok, so display it
              }
          });
        }

      /*

      function loadImage() {
        $.ajax({
          url: 'session.php?loadImage',
          type: 'POST',
          success : function(html) {
            $('#images').html(html); // data came back ok, so display it
            }
        });
      }

      setInterval(pointsRefresh, 2000);

      function pointsRefresh() {
          $.ajax({
            url: 'session.php?refreshonlyPoints',
            type: 'POST',
            success : function(html) {
              $('#points').html(html); // data came back ok, so display it
              }
          });
        }*/

        function hideChat() {
          document.getElementById('chatDisplay').style.display ="none";
          document.getElementById('showChat').style.display ="block";
          document.getElementById('hideChat').style.display ="none";
        }

        function showChat() {
          document.getElementById('chatDisplay').style.display ="block";
          document.getElementById('showChat').style.display ="none";
          document.getElementById('hideChat').style.display ="block";
        }

        function hidePoint() {
          document.getElementById('pointDisplay').style.display ="none";
          document.getElementById('showPoint').style.display ="block";
          document.getElementById('hidePoint').style.display ="none";
        }

        function showPoint() {
          document.getElementById('pointDisplay').style.display ="block";
          document.getElementById('showPoint').style.display ="none";
          document.getElementById('hidePoint').style.display ="block";
        }

      </script>
    </body>
</html>

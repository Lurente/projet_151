<?php
  session_start();

  require_once('includes/config.php');

  $email = isset($_POST['email'])?$_POST['email']:'';
  $emailPseudo = isset($_POST['emailPseudo'])?$_POST['emailPseudo']:'';
  $pseudo = isset($_POST['pseudo'])?$_POST['pseudo']:'';
  $password = isset($_POST['password'])?$_POST['password']:'';
  $confirmPassword = isset($_POST['confirmPassword'])?$_POST['confirmPassword']:'';
  $encryptedpassword = md5($password);
  $level = 'stdrUsr';
  $id_envoyeur = isset($_SESSION['id_compte'])?$_SESSION['id_compte']:'';
  $message = isset($_POST['chat'])?$_POST['chat']:'';
  $id_destinataire = 3;

  echo "<pre>";
  print_r($_POST);
  echo "</pre>";

  if(isset($_POST['signIn'])){
    try
    {

      // préparation de la requete préparée (Prepared Statment)

      $emailcheck = $db->prepare("SELECT *  FROM compte WHERE email=:email");
      $emailcheck->bindParam(":email", $email);
      $emailcheck->execute();

      $usernamecheck = $db->prepare("SELECT * FROM compte WHERE pseudo=:pseudo");
      $usernamecheck->bindParam(":pseudo", $pseudo);
      $usernamecheck->execute();
      //$stmt = $db->query("SELECT password FROM compte WHERE password = '$encryptedpassword'");

      if ($emailcheck->rowCount() > 0){
        header('location:index.php?error=emailtaken');
      }
      elseif ($usernamecheck->rowCount() > 0){
        header('location:index.php?error=pseudotaken');
      }elseif ($password != $confirmPassword){
        header('location:index.php?error=passwordNonSimilaire');
      }else{
        $requete = $db->prepare("INSERT INTO compte (email, pseudo, password, level) VALUES (:email, :pseudo, :password, :level)" );

        $requete->bindParam(":email", $email);
        $requete->bindParam(":pseudo", $pseudo);
        $requete->bindParam(":password", $encryptedpassword);
        $requete->bindParam(":level", $level);

        $requete->execute();
          echo $pseudo;

        header('location:index.php');
      }

    }
    catch(PDOException $e){
      die('Une erreur est survenue ! ' . $e->getMessage());
    }
  }

  if(isset($_POST['login']))
  {
    try
    {
    	// préparation de la requete préparée (Prepared Statment)
    	$requete = "SELECT * FROM compte WHERE (pseudo=? OR email=?) AND password=?";
    	$stmt = $db->prepare($requete);
    	$stmt->bindParam(1, $emailPseudo);
      $stmt->bindParam(2, $emailPseudo);
    	$stmt->bindParam(3, $encryptedpassword);   // ATTENTION on bind en convertissant en MD5 ce qui est reçu

    	$stmt->execute();

    	if ($stmt->rowCount() > 0) {
    		// login effectué avec succès ! on a trouvé un utilisateur correspondant
    		// mise en session
    		$curUsr = $stmt->fetch(PDO::FETCH_OBJ);
        $_SESSION['level'] = $curUsr->level;
    		$_SESSION['id_compte'] = $curUsr->id_compte;
    		$_SESSION['email'] = $curUsr->email;
    		$_SESSION['pseudo'] = $curUsr->pseudo;
    		$_SESSION['password'] = $curUsr->password;

        header('location:session.php');
    	}
    	else
    	{
    		header('location:index.php?error=badPassword');
    	}
    }
    catch(PDOException $e)
    {
    	die('Une erreur est survenue ! ' . $e->getMessage());
    }
  }

  if(isset($_POST['sendChat'])){
    try
    {

      $sendChat = $db->prepare("INSERT INTO chat (id_envoyeur, message, id_destinataire) VALUES (:id_envoyeur, :message, :id_destinataire)");
      $sendChat->bindParam(":id_envoyeur", $id_envoyeur);
      $sendChat->bindParam(":message", $message);
      $sendChat->bindParam(":id_destinataire", $id_destinataire);
      $sendChat->execute();

      header('location:session.php');

    }
    catch(PDOException $e){
      die('Une erreur est survenue ! ' . $e->getMessage());
    }
  }

?>

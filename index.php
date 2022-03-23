<?php


session_start();

$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$WON = false;

                   //-- Toutes les partie du corps de pendu --//

$bodyParts = ["sanstete","tete","corp","maingauche","maindroite","jambes"];

                     //--Remplissage des mots dans un tableau--//

$words = [];
$gestion = fopen("mots.txt", "r");
if ($gestion)
{
   while (!feof($gestion))
   {
        $charge = fgets($gestion, 4096);
        $mot = trim($charge);
        $words[] = $mot;
    }
     
}
fclose($gestion);



function getCurrentPicture($part){
    return "./images/pendu_". $part. ".png";
}


function startGame(){
   
}

//-- Nettoyage des sessions--//
function restartGame(){
    session_destroy();
    session_start();

}

//-- Récupère les parties de pendu-- //

function getParts(){
    
    global $bodyParts;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $bodyParts;
}

//-- Ajouter des parties-- //

function addPart(){
    
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
}

//-- Récupération des partie actuelles du pendu--//
function getCurrentPart(){
    
    $parts = getParts();
    return $parts[0];
}

//--Récupération du mot actuel--// 

function getCurrentWord(){
    
    global $words;
    if(!isset($_SESSION["word"]) && empty($_SESSION["word"])){
        $key = array_rand($words);
        $_SESSION["word"] = $words[$key];
        var_dump($words[$key]);
    }
    return $_SESSION["word"];
}


//-- Récupération  des reponses de l'utilisateur--//

function getCurrentResponses(){
    
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addResponse($letter){
   
    $responses = getCurrentResponses();
    array_push($responses, $letter);
    $_SESSION["responses"] = $responses;
}

//--Vérification si la lettre est juste--//

function isLetterCorrect($letter){
    
    $word = getCurrentWord();
    $max = strlen($word) - 1;
    for($i=0; $i<= $max; $i++){
        if($letter == $word[$i]){
            return true;
        }
    }
    return false;
}

//--Vérification si le mot a découvrir est correct en fonction des action joueur--//

function isWordCorrect(){
    $guess = getCurrentWord();
    $responses = getCurrentResponses();
    $max = strlen($guess) - 1;
    for($i=0; $i<= $max; $i++){
        if(!in_array($guess[$i],  $responses)){
            return false;
            var_dump($guess[$i]);
        }
    }
   
    return true;
}

//--Préparation affichage pendu--//

function isBodyComplete(){
    $parts = getParts();
    // iveryfication de quel partie du corps est affiché
    if(count($parts) <= 1){
        return true;
    }
    return false;
}


//--Si le jeu est fini--//

function gameComplete(){
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] :false;
}


//-- Set jeu fini --//

function markGameAsComplete(){
    $_SESSION["gamecomplete"] = true;
}

//--Lancement nouvelle partie --//

function markGameAsNew(){
    $_SESSION["gamecomplete"] = false;
}



//--Restart de la game par pression du btn restart--//

if(isset($_GET['start'])){
    restartGame();
}


//--Détection quand on clique sur une lettre--//

if(isset($_GET['kp'])){
    $currentPressedKey = isset($_GET['kp']) ? $_GET['kp'] : null;
    // Si la lettre est juste
    if($currentPressedKey 
    && isLetterCorrect($currentPressedKey)
    && !isBodyComplete()
    && !gameComplete()){
        
        addResponse($currentPressedKey);
        if(isWordCorrect()){
            $WON = true; // Jeu gagné
            markGameAsComplete();
        }
    }else{
        // Sinon on commence a pendre
        if(!isBodyComplete()){
           addPart(); 
           if(isBodyComplete()){
               markGameAsComplete(); 
           }
        }else{
            markGameAsComplete(); 
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pendu officiel</title>
</head>
    <body style="background: deepskyblue">
        
        <div style="margin: 0 auto; background: #dddddd; width:900px; height:900px; padding:5px; border-radius:3px;">
            
            <div style="display:inline-block; width: 500px; background:#fff;">
                 <img style="width:80%; display:inline-block;" src="<?php echo getCurrentPicture(getCurrentPart());?>"/>
          
              
               <?Php if(gameComplete()):?>
                    <h1>Jeu terminé</h1>
                <?php endif;?>
                <?php if($WON  && gameComplete()):?>
                    <p style="color: darkgreen; font-size: 25px;">Vous avez gagné, Félicitation </p>
                <?php elseif(!$WON  && gameComplete()): ?>
                    <p style="color: darkred; font-size: 25px;">Vous avez Perdu, CHEH </p>
                <?php endif;?>
            </div>
            
            <div style="float:right; display:inline; vertical-align:top;">
                <h1>Pendu officiel</h1>
                <div style="display:inline-block;">
                    <form method="get">
                    <?php
                        $max = strlen($letters) - 1;
                        for($i=0; $i<= $max; $i++){
                            echo "<button type='submit' name='kp' value='". $letters[$i] . "'>".
                            $letters[$i] . "</button>";
                            if ($i % 7 == 0 && $i>0) {
                               echo '<br>';
                            }
                            
                        }
                    ?>
                    <br><br>
                    <!-- Restart game button -->
                    <button type="submit" name="start">Restart Game</button>
                    </form>
                </div>
            </div>
            
            <div style="margin-top:20px; padding:15px; background: lightseagreen; color: #fcf8e3">
                <!-- Display the current guesses -->
                <?php 
                 $guess = getCurrentWord();
                 $maxLetters = strlen($guess) - 1;
                for($j=0; $j<= $maxLetters; $j++): $l = getCurrentWord()[$j];?>
                    <?php if(in_array($l, getCurrentResponses())): ?>
                        <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;"><?php echo $l;?></span>
                    <?php  else: ?>
                        <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;">&nbsp;&nbsp;&nbsp;</span>
                    <?php endif;?>
                <?php endfor;?>
            </div>
            
        </div>
        
        
        
    </body>
    
    
</html>

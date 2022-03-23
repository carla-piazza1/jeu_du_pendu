<?php

if(empty($_GET["mot"])) {
$mot = $_GET["mot"];
$file = fopen("mots.txt", "a");
fwrite($file,$mot);
fclose($file);

}
else {
$mot = $_GET["mot"]. PHP_EOL;
$file = fopen("mots.txt", "a");
fwrite($file,$mot);
fclose($file);
}
?>
<form action="admin.php?mot=<?=$mot?>" method="get">
<textarea id="msg" name="mot"></textarea>
<button type="submit">Ajouter le mot</button>


</form>
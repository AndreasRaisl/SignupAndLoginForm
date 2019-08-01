

<?php

if(isset($_GET['q']))
{
	$password = $_GET['q'];
}

//if($password)

$sicherheitszahl = 0;
$debug_kleinbuchstabe = 0;
$debug_grossbuchstabe = 0;
$debug_zahl = 0;
$debug_zeichen = 0;
$sicherheitszahl = strlen($password);


if (preg_match("/[a-z]/", $password)) {
    $sicherheitszahl = $sicherheitszahl + 5;
    $debug_kleinbuchstabe++;
}
if (preg_match("/[A-Z]/", $password)) {
    $sicherheitszahl = $sicherheitszahl + 5;
    $debug_grossbuchstabe++;
}
if (preg_match("/[0-9]/", $password)) {
    $sicherheitszahl = $sicherheitszahl + 5; 
    $debug_zahl++;          
}

if (preg_match("/[,.;:_]/", $password)) {
    $sicherheitszahl = $sicherheitszahl + 5;
    $debug_zeichen++;    
}
 
if($sicherheitszahl < 18)
{	
	echo 'unsicher (' . $sicherheitszahl . ' Punkte)' . $debug_kleinbuchstabe . $debug_grossbuchstabe . $debug_zahl . $debug_zeichen;
}
elseif($sicherheitszahl <= 25)
	echo 'sicher (' . $sicherheitszahl . ' Punkte)' . $debug_kleinbuchstabe . $debug_grossbuchstabe . $debug_zahl . $debug_zeichen;
elseif($sicherheitszahl > 25)
	echo 'sehr sicher (' . $sicherheitszahl . ' Punkte)'  . $debug_kleinbuchstabe . $debug_grossbuchstabe . $debug_zahl . $debug_zeichen;

?>
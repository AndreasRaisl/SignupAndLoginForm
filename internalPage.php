<?php
session_start();

if(!isset($_SESSION['user'])) {
  die("Bitte zunächst <a href='index.php#LoginHeader'> einloggen </a> oder <a href='index.php#RegistrationHeader'> registrieren </a> <br>");
}
$user = $_SESSION['user'];

$context = $_GET['context'];
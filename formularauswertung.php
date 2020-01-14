<?php
session_start();
	$action = $_GET['action'];		
	if(empty($action)) {
		echo "Bitte zunächst <a href='index.php#LoginHeader'> einloggen </a> oder <a href='index.php#RegistrationHeader'> registrieren </a> <br>"; 
	}
?>

<head>
		<title> Welcome Page </title>
		<link rel="stylesheet" href="styles/bootstrap.min.css">
		<link rel="stylesheet" href="styles/processingStyles.css">
</head>

<body>

<?php
		

  // execute if a Signup
	if($action == "register")
	{
		$userName = $_POST['username'];
		$firstName = $_POST['vorname'];
		$lastName = $_POST['nachname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$passwordRepeat = $_POST['password-repeat'];
		$passwordMismatch = false;
		if($password != $passwordRepeat) $passwordMismatch = true;			
		$travelDestination = $_POST['traveldestination'];				
		$pet = $_POST['pet'];
		if (isset($_POST['isStupid'])) $isStupid = true;
		else $isStupid = false;  
		$comments = $_POST['comments'];			
		
		$dateOfRegistration = date("d.m.Y");
		$timeOfRegistration = date("H:i:s");
		$registrationExpires = strtotime("+30 days");
		$dateRegistrationExpires = date("d.m.Y", $registrationExpires);
		$timeRegistrationExpires = date("H:i", $registrationExpires);
		

			if ($firstName == "" or $lastName == "" or $email == "" or $password == "" or $passwordRepeat == "")
			{
				$linkWithQueryString = buildLinkWithQueryString($userName, $firstName, $lastName, $email);				
				echo "Wichtige Felder wurden nicht ausgefüllt. Gehen Sie bitte nochmal zurück zum
						 <a href='" . $linkWithQueryString . "'> Eingabeformular </a> <br>";										
			} 

			else if ($passwordMismatch)
			{
				$linkWithQueryString = buildLinkWithQueryString($userName, $firstName, $lastName, $email);				
				echo "Die beiden Passwörter stimmen nicht überein. Gehen Sie bitte nochmal zurück zum
						 <a href='" . $linkWithQueryString . "'> Eingabeformular </a> <br>";				
			}

			else 
			{
				$passwordEnc = sha1($password);
				$userInput = array('firstName'=>$firstName, 'lastName'=>$lastName, 'email'=>$email,
				'passwordEnc'=>$passwordEnc, 'userName'=>$userName, 
				'travelDestination'=>$travelDestination, 'pet'=>$pet, 'isStupid' =>$isStupid, 'comments' => $comments, 
				'dateOfRegistration'=>$dateOfRegistration, 'timeOfRegistration'=>$timeOfRegistration, 'dateRegistrationExpires'=>$dateRegistrationExpires,
				'timeRegistrationExpires'=>$timeRegistrationExpires);
				$userInputToSave = array('userName'=>$userName, 'firstName'=>$firstName, 'lastName'=>$lastName, 'email'=>$email,
				'passwordEnc'=>$passwordEnc);
				printUserInput($userInput);
				saveUserToFile($userInputToSave);
				if ( isset($_FILES['fileUpload']['name']) && $_FILES['fileUpload']['name'] <> "" )	processAndStoreUploadedFile();
			} 			
		}


    // execute if a Login 
    else if($action == "login")
    {	

			$nameFound = false;			
    	$mail = $_POST['email'];
			$password = $_POST['password'];
			$passwordEnc = sha1($password);
			$allUsers = file('Data/users.txt');
			//var_dump($allUsers);

			foreach($allUsers AS $user)
			{			
				$userAsArray = explode(';', $user);			
				if ($userAsArray[3] == $mail) {
					if($userAsArray[4] == $passwordEnc) {
						$nameFound = true;
						$_SESSION['user'] = $userAsArray[0];

						echo "Herzlich Willkommen im internen Bereich, " . $userAsArray[0] . "<br>";						
						echo date("d.m.Y - H:i:s", time()) . "<br>";
						
						echo "Hier gehts in den  <a href='internalPage.php?context=fromLogin'> internen Bereich </a>  <br>";	
						echo "<a href='logout.php'> Logout </a> <br>";							
						break;
					}
					else {
						echo "Das Passwort ist falsch <br>";
						echo "<a href='index.php#LoginHeader'> Zurück zum Login </a>";
						$nameFound = true;
						break;
					}
				}				
			}

			if ($nameFound == false) {
				echo "Die Emailadresse wurde nicht gefunden <br>";
				echo "<a href='index.php#LoginHeader'> Zurück zum Login </a>";
			}
		}      

    else 
    {
			echo "Fehler: Kein query String für action übergeben!";			
			echo "Bitte zunächst <a href='index.php#LoginHeader'> einloggen </a> oder <a href='index.php#RegistrationHeader'> registrieren </a> <br>";
		}

		
	//Outputs (echo) all data the user has entered 
function printUserInput($userInput)
{
	echo "Ihr Vorname lautet: " . $userInput['firstName'] . "<br>";
	echo "Ihr Nachname lautet: " . $userInput['lastName']  . "<br>";
	echo "Ihr Username lautet: " . $userInput['userName'] . "<br>";
	echo "Ihre Email lautet: " . $userInput['email']  . "<br>";
	echo "Ihr Passwort lautet: Hm, hoffentlich haben Sie es sich gemerkt <br>";
	echo "Ihr liebstes Reiseziel ist: " . $userInput['travelDestination']. "<br>";
	echo "Sie haben folgendes Haustier angegeben: " . $userInput['pet'] . "<br>";
	if(!empty ($userInput['isStupid'])) echo "Sie finden diese Fragen ganz schön doof!  <br>";
	else echo "Sie finden diese Fragen offensichtlich ganz normal <br> ";		
	echo "Sie haben sich registriert am " . $userInput['dateOfRegistration'] . " um " . $userInput['timeOfRegistration'] . "<br>";
	echo "Ihre Registrierung ist 30 Tage gültig, also bis zum " . $userInput['dateRegistrationExpires'] . 
	" um " . $userInput['timeRegistrationExpires'] . "<br>";
}

// takes in already entered user data as strings and returns a linktext (string) for a href parameter pointing back to the Sign In Form 
//with an attached query string of the already entered user data 
function buildLinkWithQueryString($userName, $firstName, $lastName, $email) {	
	$linkWithQueryString = 'index.php?userName=' . $userName . '&firstName=' . $firstName . '&lastName=' . $lastName . 
	'&email=' . $email;	
	return $linkWithQueryString;		
}

function saveUserToFile($userInputToSave)
{	
	$userInputToSave  = implode(';', $userInputToSave) . ";dummyString\n";
	//if (IsMailRegistered($userInputToSave)) {
	// 	echo "Diese Mailadresse ist bereits registriert. Kein neuer Datensatz der Nutzerdatei hinzugefügt. <br>
	// 	      Bitte melden Sie sich mit dem bestehenden Passwort an oder registrieren Sie eine neue Emailadresse <br>";
	// }
	
	file_put_contents('Data/users.txt', $userInputToSave, FILE_APPEND);
	echo "Der Nutzerdatei hinzugefügt <br> ";
}

// changes the filename to a robust version 
function standardizeFileName($dateiname)
{
	// habe ich kopiert aus ".....", alle weiteren Codezeilen einschliesslich der Kommentare von dort //übernommen
	// erwünschte Zeichen erhalten bzw. umschreiben
	// aus allen ä wird ae, ü -> ue, ß -> ss (je nach Sprache mehr Aufwand)
	// und sonst noch ein paar Dinge;
	// (ist schätzungsweise mein persönlicher Geschmach ;)
$dateiname = strtolower ( $dateiname );
$dateiname = str_replace ('"', "-", $dateiname );
$dateiname = str_replace ("'", "-", $dateiname );
$dateiname = str_replace ("*", "-", $dateiname );
$dateiname = str_replace ("ß", "ss", $dateiname );
$dateiname = str_replace ("ß", "ss", $dateiname );
$dateiname = str_replace ("ä", "ae", $dateiname );
$dateiname = str_replace ("ä", "ae", $dateiname );
$dateiname = str_replace ("ö", "oe", $dateiname );
$dateiname = str_replace ("ö", "oe", $dateiname );
$dateiname = str_replace ("ü", "ue", $dateiname );
$dateiname = str_replace ("ü", "ue", $dateiname );
$dateiname = str_replace ("Ä", "ae", $dateiname );
$dateiname = str_replace ("Ö", "oe", $dateiname );
$dateiname = str_replace ("Ü", "ue", $dateiname );
$dateiname = htmlentities ( $dateiname );
$dateiname = str_replace ("&", "und", $dateiname );
$dateiname = str_replace ("+", "und", $dateiname );
$dateiname = str_replace ("(", "-", $dateiname );
$dateiname = str_replace (")", "-", $dateiname );
$dateiname = str_replace (" ", "-", $dateiname );
$dateiname = str_replace ("\'", "-", $dateiname );
$dateiname = str_replace ("/", "-", $dateiname );
$dateiname = str_replace ("?", "-", $dateiname );
$dateiname = str_replace ("!", "-", $dateiname );
$dateiname = str_replace (":", "-", $dateiname );
$dateiname = str_replace (";", "-", $dateiname );
$dateiname = str_replace (",", "-", $dateiname );
$dateiname = str_replace ("--", "-", $dateiname );

// Heilfunktion
$dateiname = filter_var($dateiname, FILTER_SANITIZE_URL);
return ($dateiname);
}

// stores a file that the user might have uploaded
function processAndStoreUploadedFile()
{
	$allowedFileTypes = array("image/png", "image/jpeg", "image/gif", "text/plain", "application/pdf");
	$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
	
	
	if(in_array($_FILES['fileUpload']['type'], $allowedFileTypes))
    {
    	$dateiname = standardizeFileName($_FILES['fileUpload']['name']);
    	move_uploaded_file (
        $_FILES['fileUpload']['tmp_name'] ,
         'uploadedFiles/'. $dateiname);
    	echo "Hochladen war erfolgreich <br>";
    	echo "<a href='uploadedFiles/" . $_FILES['fileUpload']['name'] . "'> uploadedFiles/" . $_FILES['fileUpload']['name'] . " </a> <br> <br>";
	}
	else echo "Ungültiger Dateityp";	
}

include('footer.php');
?>

</body>



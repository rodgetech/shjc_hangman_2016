<?php  

/*
Author: Luis Rodriguez
Class: Advance Database Design
Date of Completion: 14/1/2016
Teacher: Mr. Johnstone
Institution: Sacred Heart Junior College
Purpose:
The purpose of this application is to simulate an online Hang-the-man game.
*/

session_start();

$images = array();

// populate array with hang images
for ($i=0; $i < 7; $i++) { 
	$images[$i] = "hangs/hang" . $i . ".gif";
}

// check if new game is selected
if(isset($_POST['new_game']))
{
	session_unset(); // unset all sessions
	//session_destroy(); 
}

// check if all sessions are unset so we only set them once and not overwrite any sensitive inforamtion
// that may break the functionality of the program
if(empty($_SESSION['alphabets']) && empty($_SESSION['guesses']) && empty($_SESSION['placeholder']) 
	&& empty($_SESSION['word']) && empty($_SESSION['image_index']))
{
	$_SESSION['alphabets'] = range('A', 'Z'); // generates an array with values from A to Z
	$_SESSION['guesses'] = 6; // total guesses before loosing
	$_SESSION['word'] = file_get_contents("http://randomword.setgetgo.com/get.php"); // get random word
	while (strlen($_SESSION['word']) < 5) { // make sure word has atleast 5 chars before proceeding
		$_SESSION['word'] = file_get_contents("http://randomword.setgetgo.com/get.php");
	}
	$_SESSION['placeholder'] = array();
	for ($i=0; $i < strlen($_SESSION['word']); $i++) { // used to notify the user if they made any correct selections
		$_SESSION['placeholder'][$i] = "_ ";
	}
	$_SESSION['image_index'] = 0; // used to change image
}

// checks if user selected a letter
if(isset($_POST['letter']))
{
	$word = $_SESSION['word']; // get random word
	$letter = strtolower($_POST['letter']); // get selected letter

	$offset = 0; 
	$letterCounter = 0;

	if(strpos($word, $letter) !== false) // check if letter appears within the random word
	{
		if(strpos($word, $letter) == 0){  // check if letter is occurs at the first position in the word
			$letterCounter++;
			$_SESSION['placeholder'][$offset] = $letter;
		}

		while($offset = strpos($word, $letter, ++$offset)){ // find all position of the letter if it occurs multiple times
			$letterCounter++;
			$_SESSION['placeholder'][$offset] = $letter;
		}
	}
	else // use choose the wrong letter
	{
		$_SESSION['image_index']++; // change image
		$_SESSION['guesses']--; // decrement total guesses
	}

	// remove letter button that user clicked
	$pos = array_search($_POST['letter'], $_SESSION['alphabets']); 
	unset($_SESSION['alphabets'][$pos]);	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Hangman | Advance Database Design</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div id="wrapper"><!-- Start Wrapper -->
	<div id="header"><!-- Start Header -->
		<h1>Hangman :)</h1>
	</div><!-- End Header -->
	<div id="messages"><!-- Start Messages -->
			<h2>
				<?php  
				// loose condition
				if($_SESSION['guesses'] == 0)
				{
					echo "Game Over :( <br>";
					echo "The word was " . $_SESSION['word'];
				}
				elseif(strcmp(implode('', $_SESSION['placeholder']), $_SESSION['word']) == 0) // win condition
				{
					echo "Congratulations, You Won!!";
				}
				?>
			</h2>
		</div><!-- End Messages -->
	<div id="content"><!-- Start Content -->
		<div id="hangs-image"><!-- Start Hangs-image -->
			<img src="<?php echo $images[$_SESSION['image_index']] ?>"> 
		</div><!-- End Hangs-image -->
		<div id="display"><!-- Start Display -->
			<h1>
				<?php  
					for($i = 0; $i < count($_SESSION['placeholder']); $i++) // used to show the user if they made a correct selection
					{
						echo $_SESSION['placeholder'][$i];
					}
				?>
			</h1>	
		</div><!-- End Display -->
		<div id="buttons"><!-- Start Buttons -->
			<?php 
			foreach ($_SESSION['alphabets'] as $letter) { // dynamically generate submit buttons represeting alphabets
			?>
				<form id="letters" method="post" action="index.php" style="display: inline;"><!-- Start Form -->
					<input type="submit" name="letter" value="<?php echo $letter; ?>" >	
				</form><!-- End Form -->
			<?php  
			}
			?>
				<form method="post" action="index.php"><!-- Start Form -->
					<input id="new" type="submit" value="New Game" name="new_game">
				</form><!-- Start Form -->
		</div><!-- End Buttons -->
	</div><!-- End Content -->
</div><!-- End Wrapper -->

</body>
</html>
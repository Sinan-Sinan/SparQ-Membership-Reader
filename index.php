<?php
	session_start();//Starts Session to collect transfer variable values beetween tabs

	$cardString = $_SESSION['cardString'];//Gets the Card Number in String Format from session
	if(isset($_REQUEST['cardNum'])){//Request for Card Number
		$cardString = htmlentities($_REQUEST['cardNum']);//Makes sures no injection 
	}
	else{
		$cardString = '';
	}

	$cardNum = (int)$cardString;//Parses String

	if($cardNum > 8){//Student Numbers are less than 8
		$cardString = substr($cardNum, 0, 8);
	}

	$cardNum = (int)$cardString;//I dont why this is here lol
	$_SESSION['cardString'] =  $cardString;//Session to store Card string

	$spreadsheet_url="URL";//Spreadsheet URL (Use google spreadsheets)

	if(!ini_set('default_socket_timeout', 10)) echo "<!-- unable to change socket timeout -->";//Gets the spreadsheet

	//Variables to store data
	$valid;
	$cardNumber;
	$membership;
	$training;

	//Gets the Data and stores them
	if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($data[2] == $cardString){
				$cardNumber = $data[2];
				$valid = $data[5];
				$membership = $data[10];
				$training = $data[11];
				break;
			}
		}
		fclose($handle);
	}
	else {
		print("ERROR");	
	}

	$colour;//Variable to display colours

	if(strcmp($valid,'VALID') == 0){//Makes sure that the Member is valid
		$colour = 'green';
	}elseif(strcmp($valid,'INVALID') == 0){//If Member didnt pay or didnt do training they are invalid
		$colour = 'red';
	}else{//If the number is not found
		$colour = 'white';
		$valid = 'NOT FOUND';
	}

	if(!(empty($cardNumber))){//Refreshes the Tab after 1 sec unless if member is not found
		header("refresh:1;url=index.php");
	}

	//Sends data to session (don't really need it)
	$_SESSION['colour'] = $colour;
	$_SESSION['valid'] =  $valid;
	$_SESSION['cardNumber'] = $cardNumber;
	$_SESSION['membership'] = $membership;
	$_SESSION['training'] = $training;

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>SparQ Entry Reader</title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<?php echo '<body style="background-color:' . $colour . '">';//Displays Colour?>
<body>
	
    <h1>Membership Valdiation</h1>
    <form action="" method="GET">
        <input type="number" id="box" name="cardNum" id="cardNum" autofocus><br>
        <input type="submit" id="button" value="Submit" onclick="">
    </form>
	<h3>Student Number: <?php echo $cardString; ?></h3>
	<h1 id="valid"><?php echo $valid; //Displays if valid or invalid?> </h1>

	<?php 
	if(strcmp($valid,'INVALID') == 0) {//If member is invalid this prints out the reason
		if(strcmp($membership,'no') == 0){
			echo '<h2>Membership not Purchased</h2>';
		}
		elseif(strcmp($training,'no') == 0){
			echo '<h2>No Basic Training</h2>';
		}
		else{
			echo '<h2>Reason Not Found</h2>';
		}
	}
	?>

</body>
</html>

<?php
	session_start();
	$cardString = $_SESSION['cardString'];
    if(isset($_REQUEST['cardNum'])){
		$cardString = htmlentities($_REQUEST['cardNum']);
    }
	else{
		$cardString = '';
	}
	$cardNum = (int)$cardString;

	if($cardNum > 8){
		$cardString = substr($cardNum, 0, 8);
	}
	$cardNum = (int)$cardString;
	$_SESSION['cardString'] =  $cardString;

	$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQP6KOKJhG_39-3UNXFkCMjsKSr3gMGU_Rr3hp1mqoz5BXTEAwXwmqzAEKMrz8Cn_6DN4zfE5q_d43C/pub?gid=0&single=true&output=csv";

	if(!ini_set('default_socket_timeout', 10)) echo "<!-- unable to change socket timeout -->";

	$valid;
	$cardNumber;
	$membership;
	$training;

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

	$colour;

	if(strcmp($valid,'VALID') == 0){
		$colour = 'green';
	}elseif(strcmp($valid,'INVALID') == 0){
		$colour = 'red';
	}else{
		$colour = 'white';
		$valid = 'NOT FOUND';
	}

	if(!(empty($cardNumber))){
		header("refresh:1;url=index.php");
	}

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
<?php echo '<body style="background-color:' . $colour . '">';?>
<body>
	
    <h1>Membership Valdiation</h1>
    <form action="" method="GET">
        <input type="number" id="box" name="cardNum" id="cardNum" autofocus><br>
        <input type="submit" id="button" value="Submit" onclick="">
    </form>
	<h3>Student Number: <?php echo $cardString; ?></h3>
	<h1 id="valid"><?php echo $valid; ?> </h1>

	<?php 
	if(strcmp($valid,'INVALID') == 0) {
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

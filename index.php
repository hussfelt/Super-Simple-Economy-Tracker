<?php
$username = "";
$password = "";

$secret_key = "secret_key";

// Connect to db
$dbh = new PDO('mysql:dbname=sset;host=localhost', $username, $password);
$message = "";

if (isset($_POST['amount']) && $_GET['key'] == $secret_key) {
	$message = 'Record added.';
	// Insert new record
	$stmt = $dbh->prepare("INSERT INTO sset(name, amount, added) values (:name, :amount, :added)");

	// Bind params
	$stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR, 255);
	$stmt->bindParam(':amount', $_POST['amount'], PDO::PARAM_STR, 255);
	$stmt->bindParam(':added', $_POST['added'], PDO::PARAM_STR, 255);
	$stmt->execute();
}

if (isset($_GET['create_tables'])) {
	// Create tables
	$stmt = $dbh->exec("CREATE TABLE IF NOT EXISTS sset(id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, name varchar(255), amount varchar(255), added DATE DEFAULT '0000-00-00');");	
	if (!$stmt) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($dbh->errorInfo());
	} else {
		echo "Created tables";
	}
	exit;
}

if (isset($_GET['dump'])) {
	// If we want to dump
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=file.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	$stmt = $dbh->prepare('SELECT * FROM sset');
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	    echo $row['name'] . ',' . $row['amount'] . ',' . $row['added'] . ";\n";
	}
	$stmt = null;
	exit;
}
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
    	<?php if ($message != "") {?>
    	<p class="chromeframe"><?php echo $message;?></p>
    	<?php }?>
        <h1>Track</h1>
        <form name="track" action="index.php?key=<?php echo (isset($_GET['key']) ? $_GET['key'] : '');?>" method="POST">
        	<select name="name" required>
        		<option value="">V&auml;lj typ</option>
        		<option value="Mat">Mat</option>
        		<option value="MatUte">Mat Ute</option>
        		<option value="Nöje">Nöje</option>
        		<option value="Övrigt">Övrigt</option>
        		<option value="Bil/Resa">Bil/Resa</option>
        		<option value="Kläder">Kläder</option>
        		<option value="Lägenhet">Lägenhet</option>
        	</select>
        	<input type="number" size="6" name="amount" min="0" max="30000" value="0" />
        	<input type="date" size="6" name="added" value="<?php echo date('Y-m-d');?>" />
        	<input type="submit" value="Skicka" />
        </form>
    </body>
</html>
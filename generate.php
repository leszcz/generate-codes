<?php

require_once("class_generateCode.php");

$generateCode = new generateCode();
set_time_limit(180); // set max_execution time required to > 100000 codes 

if( php_sapi_name() == "cli") {
    $generateCode->cli($argv);
} else {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Generator kodów</title>
</head>
<body>

<?php
	echo $generateCode->cgi($_POST);
?>
</body>
</html>
<?php
}
?>

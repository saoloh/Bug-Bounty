<?php
//var x=al
include('style.php');
$host = $_SERVER["HTTP_HOST"];
$xss = $_GET['xss'];
$finder = preg_match_all('#(confirm|alert|prompt|src)#i', $xss);
if ($finder) {
	echo "XSS Detected!";} else 
	{
		echo "<html>Kewool, I see safe string! ".$xss."</html>";
		echo "</br></br>You can inject your payload in the GET parameter 'xss' (Example: http://$host/<font color=red>1.php?xss=</font>Your_XSS_Payload)";

	}
?>

<?php
//<script src=>
include('style.php');
$host = $_SERVER["HTTP_HOST"];
$xss = $_GET['xss'];
$finder = preg_match_all('#(data|confirm|alert|prompt)#i', $xss);
if ($finder) {
	echo "XSS Detected!";} else 
	{
		echo "<html>Kewool, I see safe string --> ".$xss."</html>";
		echo "</br></br>You can inject your payload in the GET parameter 'xss' (Example: $host/<font color=red>1.php?xss=</font>Your_XSS_Payload)";

	}
?>

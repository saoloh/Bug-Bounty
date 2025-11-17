<?php
include('style.php');
$host = $_SERVER["HTTP_HOST"];
$illegal = "#$%^&*+-[];,/{}|:<>?~";
$xss = $_GET['xss'];
$x = strpbrk($xss, $illegal);
if($x){
	echo "XSS Detected!";
	} else {
		echo "I Like photos! Here is your photo."."</br></br>"."<img src='".$xss."'>";
		echo "</br></br>You can inject your payload in the GET parameter 'xss' (Example: http://$host/<font color=red>1.php?xss=</font>Your_XSS_Payload)";

	}
?>

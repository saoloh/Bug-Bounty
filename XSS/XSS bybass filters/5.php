<?php
include('style.php');
$host = $_SERVER["HTTP_HOST"];
$xss = $_GET['xss'];
$finder = preg_match_all('#(src|var|eval|onmouseover|onload|onclick|onmouseout|>|<|onerror)#i', $xss);
if ($finder) {
	echo "XSS Detected!";} else 
	{
		echo "<html>Kewool, I see safe string! lets print it here. (Hint: I love EventHandlers)</br>";//.$xss."</html>";
		echo "</br>$xss</br></br>";
		echo '<form action=""><input type="text" name="xss" value="'.$xss.'" width="600" height="600"></html>';
		echo "<input name=submit value=submit type=submit></input></form>";
		echo "</br>You can inject your payload in the GET parameter 'xss' (Example: http://$host/<font color=red>1.php?xss=</font>Your_XSS_Payload)";

	}
	//echo "I Like photos! Here is your photo."."</br></br>"."<img src='".$replacer."'>";
?>
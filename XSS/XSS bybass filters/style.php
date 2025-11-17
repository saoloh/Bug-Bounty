<html lang="en" class="no-js">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
<title>XSS Challanges</title>
</head>
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
}

.navbar {
  overflow: hidden;
  background-color: #333;
}

.navbar a {
  float: left;
  font-size: 16px;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.dropdown {
  float: left;
  overflow: hidden;
}

.dropdown .dropbtn {
  font-size: 16px;  
  border: none;
  outline: none;
  color: white;
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}

.navbar a:hover, .dropdown:hover .dropbtn {
  background-color: red;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  float: none;
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #ddd;
}

.dropdown:hover .dropdown-content {
  display: block;
}
</style>
</head>
<body>
<div class="navbar">
  <a href="index.php">Main</a>
  <a href="1.php?xss=Hello!">Easy (1)</a>
  <a href="2.php?xss=images.jpeg">Easy (2)</a>
  <a href="3.php?xss=Hello!">Easy (3)</a>
  <a href="4.php?xss=Hello!">Hard</a>
  <a href="5.php?xss=Hello!">Medium (1)</a>
  <a href="6.php?xss=Hello!">Medium (2)</a>
</div>
</br>
</body>
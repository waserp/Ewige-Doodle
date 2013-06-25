<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<?php

  $Cookiename = $_COOKIE["KlimperName"];
  $Cookiename = trim($Cookiename);
  if (!empty($Cookiename)) {
    header("Location: EwigaDuudel.php");
  }
  if($_POST['formSubmit'] == "Submit") {
    $Klimper = $_POST['Name'];
    if(!empty($_POST['Name'])) {
      setcookie("KlimperName", $Klimper , time()+3600000);
      header("Location: EwigaDuudel.php");
    }
  } 
  echo ("<html xmlns='http://www.w3.org/1999/xhtml'>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>\n
          <title>Login for Ewiger Doodle</title>\n</head>\n
            <body>\n<h1>LOGIN: Ewiger Doodle</h1>\n<form id='Loginform' method='post' action='' >\n");
  echo ("Name: <input type='text' name='Name' value = '' />");
  echo ("<p><input type='submit' name='formSubmit' value='Submit' /> </p></form><body/></html>");
?>


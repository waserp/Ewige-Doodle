<?php


class HTMLOut
{

  public function GetHeader($Title)
  {
    $Header = "<html xmlns='http://www.w3.org/1999/xhtml'>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>\n
          <title>" . $Title . "</title>\n<LINK REL=\"StyleSheet\" HREF=\"style.css\" TYPE=\"text/css\">\n</head>\n<body>";

    return $Header;
  }


  public function GetPageTitle($Title)
  {
    $PageTitle = "<h1>$Title</h1>\n";
    return $PageTitle;
  }

  public function GetLoginForm()
  {
    $LoginForm = "<form id='Loginform' method='post' action='' >\n
    Name: <input type='text' name='Name' value = '' />
    <p><input type='submit' name='formSubmit' value='Submit' /> </p></form>";
    return $LoginForm;
  }

  public function ClosingHtml()
  {
    $Closing = "</body></html>";
    return $Closing;
  }

}

?>

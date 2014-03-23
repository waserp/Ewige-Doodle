<?php

class Cookie
{
  private $Cookiename;

  public function Cookie()
  {
    $Cookiename = @$_COOKIE["UserName"];
    $this->Cookiename = @trim($Cookiename);
  }

  public function CheckCookieExists()
  {
    if (empty($this->Cookiename)) {
      return false;
    }
    return true;
  }

  function GetUserName()
  {
  	if ($this->CheckCookieExists()) {
  		return $this->Cookiename;
  	}
  	else {
  		return "";
  	}
  }

  public function SetCookie($CookieValue)
  {
    setcookie("UserName", $CookieValue , time() + 3600 * 24 * 365);
  }

  public function DeleteCookie()
  {

  }
}

?>
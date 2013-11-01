<?php

class Cookie
{
  private $Cookiename;

  public function Cookie()
  {
    $Cookiename = $_COOKIE["KlimperName"];
    $this->Cookiename = @trim($Cookiename);
  }

  public function CheckCookieExists()
  {
    if (empty($this->Cookiename)) {
      return false;
    }
    return true;
  }

  function GetClimperName()
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
    setcookie("KlimperName", $CookieValue , time() + 3600 * 24 * 365);
  }

  public function DeleteCookie()
  {

  }
}

?>
<?php

class Cookie
{
  private $Cookiename;

  public function Cookie()
  {
    $this->Cookiename = $_COOKIE["KlimperName"];
    $this->Cookiename = @trim($Cookiename);
  }

  public function CheckCookieExists()
  {
    if (!empty($this->Cookiename)) {
      return false;
    }
    return true;
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
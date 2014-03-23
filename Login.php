<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<?php

  include 'CHTMLOut.php';
  include 'CFormHandler.php';
  include 'CCookie.php';
  include 'CDataHandler.php';

  $HTML = new HTMLOut();
  $Cookie = new Cookie();

  if ($Cookie->CheckCookieExists()) {
    header("Location: EwigaDuudel.php");
  }

  if(@$_POST['formSubmit'] == "Submit") {
    if (!empty($_POST['Name'])) {
      $User = $_POST['Name'];
      $Cookie->SetCookie($User);
      if (!empty($_POST['Email'])) {
        if ($_POST['Email'] == $_POST['EmailConfirm']) {
        	$Email = $_POST['Email'];
        }
        else {
          $Email = "";
        }
      }
      header("Location: EwigaDuudel.php");
    }
  }

  $PageTitle = "LOGIN: Ewiger Doodle";
  echo $HTML->GetHeader($PageTitle);
  echo $HTML->GetPageTitle($PageTitle);
  $LoginForm = new FormHandler("post", "Loginform");
  $LoginForm->AddElement("text", "Name", "", "*Name");
//  $LoginForm->AddElement("text", "Email", "", "Email");
//  $LoginForm->AddElement("text", "EmailConfirm", "", "Email confirmation");
  $LoginForm->AddElement("submit", "formSubmit", "Submit");
  echo $LoginForm->StartForm();
  echo $LoginForm->GetForm();
  echo $LoginForm->CloseForm();
  echo $HTML->ClosingHtml();


  echo $HTML->ClosingHtml();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<?php
  include 'CHTMLOut.php';
  include 'CFormHandler.php';
  include 'CCookie.php';
  include 'CDoodleTable.php';
  include 'CDataHandler.php';

  $HTML = new HTMLOut();
  $Cookie = new Cookie();
  $DataHandler = new CDataHandler($Cookie->GetClimperName());

	$cWeekDays = "Mo,Di,Mi,Do,Fr,Sa,So";
  $cWeekDays = explode(",",$cWeekDays);

  function CleanEntry($Entry) {
   $val = trim($Entry);
   $val = trim($val , ",");
   $val = str_replace(",", ":", $val);
   $val = "," . strip_tags($val);
   $val = substr($val, 0, 100);
   return $val;
  }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $DataHandler->SetLastAccessAndIncrement();

  if (@$_POST['formLogout'] == "Logout") {
    $Cookie->SetCookie(""); // Clear Cookie
    header("Location: Login.php");
  }
  if (!$Cookie->CheckCookieExists()) {
    header("Location: Login.php");
  }
  $Editrequest = false;
  if (@$_POST['formEdit'] == "Edit") {
    $Editrequest = true;
  }

  if(@$_POST['formSubmit'] == "Submit") {
      $DataString = $Cookie->GetClimperName();
      for ($Day = 1; $Day <= $DataHandler->GetAmountOfDays(); $Day++) {
      	$DataString .= CleanEntry($_POST['ck'.$Day]);
      }
      $DataHandler->EditLine($DataString);
  }

  $Table = new DoodleTable();
  $Form = new FormHandler("post", "Klimperform");

  $ActualWeekDay = @date(N);

  // Generate Header with the days
  $Table->AddSingleCell("Klimper", CCell::COLOR_HEADER);
  for ($i = $ActualWeekDay; $i <= ($ActualWeekDay + ($DataHandler->GetAmountOfDays() -1)); $i++) {
    $Index = $i % 7;
    $Table->AddSingleCell($cWeekDays[$Index], CCell::COLOR_HEADER);
  }
  $Table->AddRow($Table->GetCellArray(), RowType::Header);


  $ClimperAlreadyInDatabases = false;
  $LineArray = array();
  while ($DataHandler->GetNextClimper($LineArray)) {
		$PrintLine = true;

    if ($LineArray[0] == $Cookie->GetClimperName()) {
      $ClimperAlreadyInDatabases = true;
      if (!$Editrequest) {
        $Form->AddFunctionElement("submit", "formEdit", "Edit");
      }
      else {
      	$PrintLine = false;
        $LineArrayToEdit = $LineArray;
        // Generate Input boxes in case Climper asked to edit his data
		    GenerateInputboxForEdit($Table, $Form, $Cookie->GetClimperName(), $LineArrayToEdit);
      }
    }

    if ($PrintLine) {
	    foreach ($LineArray as $Element) {
	      $Element = trim($Element);
	      if (!empty($Element)) {
//	        $color = "style=\"background-color:#80FF80\"";
	        $Color = CCell::COLOR_YES_I_CAN;
	      } else {
//	        $color = "style=\"background-color:#FF8080\"";
	        $Color = CCell::COLOR_NO_I_CANT;
	      }
				$Table->AddSingleCell($Element, $Color);
	    }
	    $Table->AddRow($Table->GetCellArray());
    }
  }

  if (!$ClimperAlreadyInDatabases) {
    // Generate Input boxes in case Climper was not found in the Database
  	GenerateInputboxForEdit($Table, $Form, $Cookie->GetClimperName(), "");
  }


  function GenerateInputboxForEdit($Table, $Form, $Cookiename, $LineArrayToEdit)
  {
		$Value = "";
  	$Table->AddSingleCell($Cookiename, CCell::COLOR_YES_I_CAN);
  	for ($Day = 1; $Day <= AMOUNT_OF_DAYS; $Day++) {
			if ($LineArrayToEdit != "") {
				$Value = $LineArrayToEdit[$Day];
			}
  		$Form->AddElement("text", "ck".$Day, $Value);
		}
		$Table->AddMultipleCell($Form->GetElementArrayString(), CCell::COLOR_CLIMPER);
		$Table->AddRow($Table->GetCellArray());
    $Form->AddFunctionElement("submit", "formSubmit", "Submit");
  }

  $Form->AddFunctionElement("submit", "formLogout", "Logout");

  //////////////////////////////////////////////////////////////////////////////////////////////
  // Output

  $PageTitle = "Ewiger Doodle";
  echo $HTML->GetHeader($PageTitle);
  echo $HTML->GetPageTitle($PageTitle);
  echo "<h2>You are logged on as <b>". $Cookie->GetClimperName() . "</b></h2>\n";

  echo ("<p>You can stay logged on if you want to.</p>\n");
  if (!$DataHandler->GetDataReadResult()) {
  	echo "<p class=\"warning\">Hmmm, datafile not ready! Come back later!</p>";
  }
  echo $Form->StartForm();
  echo $Table->GetTable();
  echo $Form->GetForm();
  echo $Form->GetFunctionElementString();
  $Refreshtext = "refresh";
  if ($Editrequest) { $Refreshtext = "cancle"; }
  echo "<a href=\"\">" . $Refreshtext . "</a>";
	echo $Form->CloseForm();
  echo ("<p class=\"footmsg_l\"><i>Server Date " . @date(r) . "</i></p>");
	echo $HTML->ClosingHtml();

?>
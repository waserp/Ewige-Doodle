<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<?php
  include 'CHTMLOut.php';
  include 'CFormHandler.php';
  include 'CCookie.php';
  include 'CDoodleTable.php';

  define("AMOUNT_OF_DAYS", "6");

  $HTML = new HTMLOut();
  $Cookie = new Cookie();

  $weekdays="So,Mo,Di,Mi,Do,Fr,Sa,So,Mo,Di,Mi,Do,Fr,Sa,So";
  $weekdays=explode(",",$weekdays);

  function AdvanceADay() {
    $remaining = 0;
    $dat = date("U");
    //echo ("Datum : " . date("W") . " " .  $dat . "\n");
    $data = file("mydata.csv");
    $fs = fopen("mydata.csv","w");
    foreach ($data as $line) {
      $LineArray = explode(",",$line);
      //echo($line);
      $flag = "0";
      for ($i = 2; $i <= 6; $i++) {
        $LineArray[$i]= trim($LineArray[$i]);
        if (!empty($LineArray[$i])) { $flag = "1"; }
      }
      if ($flag == "1") { // only keep entry if there are active dates
        $remaining = $remaining + 1;
        fwrite($fs,$LineArray[0]);
        for ($i = 2; $i <= 6; $i++) {
          //echo ($i . " ");
          fwrite($fs,"," . trim($LineArray[$i]));
        }
        fwrite($fs,",\n");
      }
    }
    fclose($fs);
  }// end function advanceADay

  function EditLine($NewLine) {
    $Written = false;
    $data = file("mydata.csv");
    $fs = fopen("mydata.csv","w");
    $NewLineArray= explode(",",$NewLine);
    foreach ($data as $line) {
      $LineArray = explode(",",$line);
      if ($NewLineArray[0] == $LineArray[0]) {
        fwrite($fs, trim($NewLine) . "\n");
        $Written = true;
      } else {
        fwrite($fs, trim($line) . "\n");
      }
    }
    if (!$Written) {
      fwrite($fs, trim($NewLine) . "\n");
    }
    fclose($fs);
  }

  function CleanEntry($Entry) {
   $val = trim($Entry);
   $val = trim($val , ",");
   $val = str_replace(",", ":",$val);
   $val = "," . strip_tags($val);
   $val = substr($val, 0, 30);
   return $val;
  }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  if (@$_POST['formAdvance'] == "Advance") {
    AdvanceADay();
  }
  $lastaccess = file("lastaccess.csv");
  $lastaccess = trim($lastaccess{0});
  $todaysday = @date(z);
  while($lastaccess != $todaysday) {
    AdvanceADay();
    $lastaccess = $lastaccess + 1;
  }
  $fs = fopen("lastaccess.csv","w"); fwrite($fs,$todaysday); fclose($fs);

  if (@$_POST['formLogout'] == "Logout") {
    setcookie ("KlimperName", "", time() - 3600);  // Clear Cookie
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
      $datestring = $Cookie->GetClimperName() . CleanEntry($_POST['ck1']) . CleanEntry($_POST['ck2']) . CleanEntry($_POST['ck3']) .
                    CleanEntry($_POST['ck4']) . CleanEntry($_POST['ck5']) . CleanEntry($_POST['ck6']);
      EditLine($datestring);
  }

  $Table = new DoodleTable();

  $Form = new FormHandler("post", "Klimperform");

  $weekday = @date(N);
  $data = file("mydata.csv"); //or die('Could not read file!');

  // Generate Header with the days
  $Table->AddSingleCell("Klimper", CCell::COLOR_HEADER);
  for ($i = $weekday; $i <= ($weekday + 5); $i++) {
		$Table->AddSingleCell($weekdays[$i], CCell::COLOR_HEADER);
  }
  $Table->AddRow($Table->GetCellArray(), RowType::Header);

  $ClimperAlreadyInDatabases = false;
  foreach ($data as $line) {
    $LineArray = explode(",",$line);
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
	        $color = "style=\"background-color:#80FF80\"";
	        $Color = CCell::COLOR_YES_I_CAN;
	      } else {
	        $color = "style=\"background-color:#FF8080\"";
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

  echo ("<i>Server Date " . @date(r) . "</i>");
  echo ("<p>You can stay logged on if you want to.</p>\n");

  echo $Form->StartForm();
  echo $Table->GetTable();
  echo $Form->GetForm();
  echo $Form->GetFunctionElementString();
  $Refreshtext = "refresh";
  if ($Editrequest) { $Refreshtext = "cancle"; }
  echo "<a href=\"\">" . $Refreshtext . "</a>";
	echo $Form->CloseForm();
  echo $HTML->ClosingHtml();

?>


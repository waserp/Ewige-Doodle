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
      $linearray = explode(",",$line);
      //echo($line);
      $flag = "0";
      for ($i = 2; $i <= 6; $i++) {
        $linearray[$i]= trim($linearray[$i]);
        if (!empty($linearray[$i])) { $flag = "1"; }
      }
      if ($flag == "1") { // only keep entry if there are active dates
        $remaining = $remaining + 1;
        fwrite($fs,$linearray[0]);
        for ($i = 2; $i <= 6; $i++) {
          //echo ($i . " ");
          fwrite($fs,"," . trim($linearray[$i]));
        }
        fwrite($fs,",\n");
      }
    }
    fclose($fs);
  }// end function advanceADay

  function EditLine($NewLine) {
    $written = "0";
    $data = file("mydata.csv");
    $fs = fopen("mydata.csv","w");
    $NewLineArray= explode(",",$NewLine);
    foreach ($data as $line) {
      $linearray = explode(",",$line);
      if ($NewLineArray[0] == $linearray[0]) {
        fwrite($fs, trim($NewLine) . "\n");
        $written = "1";
      } else {
        fwrite($fs, trim($line) . "\n");
      }
    }
    if ($written == "0") {
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
  $Cookiename = $_COOKIE["KlimperName"];
  if (empty($Cookiename)) {
    header("Location: Login.php");
  }
  $Editrequest="0";
  if (@$_POST['formEdit'] == "Edit") {
    $Editrequest="1";
  }

  $Cookiename = ucfirst(strtolower($Cookiename));

  if(@$_POST['formSubmit'] == "Submit") {
      $datestring = $Cookiename . CleanEntry($_POST['ck1']) . CleanEntry($_POST['ck2']) . CleanEntry($_POST['ck3']) .
                    CleanEntry($_POST['ck4']) . CleanEntry($_POST['ck5']) . CleanEntry($_POST['ck6']);
      EditLine($datestring);
  }

  echo $HTML->GetHeader();
  echo $HTML->GetPageTitle("Ewiger Doodle");
  echo "<h2>You are logged on as ". $Cookiename . "</h2>\n";

  echo (" Server Date " . @date(r));
  echo ("<p>You can stay logged on if you want to.</p>\n");

  $Table = new DoodleTable();

  $Form = new FormHandler("post", "Klimperform");

//   echo ("<html xmlns='http://www.w3.org/1999/xhtml'>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>\n
//           <title>Ewiger Doodle</title>\n</head>\n
//             <body>\n<h1>Ewiger Doodle</h1><h2>You are logged on as ". $Cookiename . "</h2>\n";
//     echo ("<form id='Klimperform' method='post' action='' >\n");
//   echo (" Server Date " . date(r));
//   echo ("<p>You can stay logged on if you want to.</p>\n");
  $weekday = @date(N);
  $data = file("mydata.csv"); //or die('Could not read file!');
  echo ("<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n");
  echo ('<colgroup><col width="200"><col width="180"><col width="180"><col width="180"><col width="180"><col width="180"><col width="180"></colgroup>');
  echo ("<tr style=\"background-color:#E0EFEF\"><td>Klimper</td>");
  $Table->AddSingleCell("Klimper");
  for ($i = $weekday; $i <= ($weekday + 5); $i++) {
    echo ("<td>" . $weekdays[$i] . "</td>");
		$Table->AddSingleCell($weekdays[$i]);
  }
  $Table->AddHeader($Table->GetCellArray());

  echo("</tr>\n");

  $Occurs="0";
  foreach ($data as $line) {
    echo ("<tr>");
    //echo $line."<br>";
    $linearray = explode(",",$line);
    $FirstRow = true;
    foreach ($linearray as $element) {
      //echo $element . " -- ";
      $element = trim($element);
      if (!empty($element)) {
        $color = "style=\"background-color:#80FF80\"";
      } else {
        $color = "style=\"background-color:#FF8080\"";
      }
      echo ("<td ". $color . ">". $element ."</td>");
// 			if ($FirstRow) {
// 				$Klimper = $element;
// 				$FirstRow = false;
// 			} else {
				$Table->AddSingleCell($element);
//			}
    }
    $Table->AddRow($Table->GetCellArray());
    if ($linearray[0] == $Cookiename) {
      //echo("<td><input type='submit' name='formEdit' value='Edit' /></td>");
      $Occurs="1";
      $Form->AddFunctionElement("submit", "formEdit", "Edit");
      $linearraytoedit=$linearray;
    }
    echo("</tr>\n");
  }
  if ($Occurs=="0") {
  	echo "Add text boxes to form and table";
		for ($Day = 1; $Day <= AMOUNT_OF_DAYS; $Day++) {
			$Form->AddElement("text", "ck".$Day, "");
		}
		$Table->AddRow($Cookiename, $Form->GetElementArray());

//   	echo ("<tr>
//          <td>" . $Cookiename ."</td>
//          <td><input type='text' name='ck1' /></td>\n
//          <td><input type='text' name='ck2' /></td>\n
//          <td><input type='text' name='ck3' /></td>\n
//          <td><input type='text' name='ck4' /></td>\n
//          <td><input type='text' name='ck5' /></td>\n
//          <td><input type='text' name='ck6' /></td>\n
//          </tr>");
  }
  if ($Editrequest=="1") {
  	echo "edit request";
  	for ($Day = 1; $Day <= AMOUNT_OF_DAYS; $Day++) {
			$Form->AddElement("text", "ck".$Day, $linearraytoedit[$Day]);
		}
		$Table->AddRow($Cookiename, $Form->GetElementArray());
//   	echo ("<tr>
//          <td>" . $Cookiename ."</td>
//          <td><input type='text' name='ck1' value = '" . $linearraytoedit[1] ."' /></td>\n
//          <td><input type='text' name='ck2' value = '" . $linearraytoedit[2] ."' /></td>\n
//          <td><input type='text' name='ck3' value = '" . $linearraytoedit[3] ."' /></td>\n
//          <td><input type='text' name='ck4' value = '" . $linearraytoedit[4] ."' /></td>\n
//          <td><input type='text' name='ck5' value = '" . $linearraytoedit[5] ."' /></td>\n
//          <td><input type='text' name='ck6' value = '" . $linearraytoedit[6] ."' /></td>\n
//          </tr>");
  }
  $Form->AddElement("submit", "formSubmit", "Submit");
  $Form->AddElement("submit", "formLogout", "Logout");
  echo("</table>");
//   echo("<p><input type='submit' name='formSubmit' value='Submit' /></p>\n
//         <p><input type='submit' name='formLogout' value='Logout' /></p>\n");
//    <p> <!--input type='submit' name='formAdvance' value='Advance' / --> </p>
//  echo("</form>\n</html>\n\n");

  echo "<h2>Table by Class</h2>";
  echo $Form->StartForm();
  echo $Table->GetTable();
  echo $Form->GetForm();
  echo $Form->GetFunctionElementString();
	echo $Form->CloseForm();
  echo $HTML->ClosingHtml();
?>


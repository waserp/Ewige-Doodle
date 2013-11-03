<?php

define("FILE_LAST_ACCESS", "lastaccess.csv");
define("FILE_DOODLE_DATA", "mydata.csv");
define("CLIMPER_DATA_ELEMENT_NAME", 0);
define("CLIMPER_DATA_ELEMENT_EMAIL", 1);
define("CLIMPER_DATA_ELEMENT_LAST", CLIMPER_DATA_ELEMENT_NAME);
define("AMOUNT_OF_DAYS", "7");

class CDataHandler
{
  private $Data = array(); // Array of climper data
  private $KlimperData = array(); // old LineArray, Containing all data for a particular climper
  private $fs;


	public function __construct()
	{
    $this->ReadDataFromFile();
	}

	public function __destruct()
	{
    if ($this->fs == NULL) {
    	echo "ERROR, Filepointer is NULL before destruction\n";
    }
    else {
      $this->WriteDataToFile();
      fclose($this->fs);
    }
	}

	function IncrementDay()
	{
		foreach ($this->Data as $Key => &$LineArray) {
			$AtLeastOneEntryFound = false;
			for ($i = (CLIMPER_DATA_ELEMENT_LAST + 2); $i < count($LineArray); $i++) {
				$LineArray[$i] = trim($LineArray[$i]);
				if (!empty($LineArray[$i])) {
					$AtLeastOneEntryFound = true;
				}
			}
			if ($AtLeastOneEntryFound) { // only keep entry if there are active dates
        $NewClimperArray = array();
				$NewClimperArray[CLIMPER_DATA_ELEMENT_NAME] = $LineArray[CLIMPER_DATA_ELEMENT_NAME];
				for ($i = (CLIMPER_DATA_ELEMENT_LAST + 2); $i < count($LineArray); $i++) {
					$NewClimperArray[CLIMPER_DATA_ELEMENT_LAST + $i - 1] = $LineArray[$i];
				}
        $LineArray = $NewClimperArray;
			}
      else {
        unset($this->Data[$Key]);
      }
		}
	}

	public function IsKlimperInDatabase($KlimperNameToVerify)
  {
		foreach ($this->Data as $ActualKlimperData)
		{
      $ActualKlimperName = $ActualKlimperData[0];
			if ($ActualKlimperName == $KlimperNameToVerify) {
				return true;
			}
		}
		return false;
	}

	public function AddNewEmptyKlimperDataset($NewKlimperName)
	{
		$this->EditLine($NewKlimperName . ",\n");
	}

	function GetLastAccess()
	{
		$LastAccess = file(FILE_LAST_ACCESS);
		$LastAccess = trim($LastAccess{0});
		return $LastAccess;
	}



	private function CleanEntry($Entry) {
		$val = trim($Entry);
		$val = trim($val , ",");
		$val = str_replace(",", ":", $val);
		$val = "," . strip_tags($val);
		$val = substr($val, 0, 30);
		return $val;
	}

	function EditLine($KlimperToEdit) { // KlimperToEdit: cs String
    $Written = false;
		$KlimperToEditArray = $this->KlimperDataStringToArray($KlimperToEdit);
		foreach ($this->Data as &$ActualKlimperArray) {
			if ($KlimperToEditArray[0] == $ActualKlimperArray[0]) {
				$ActualKlimperArray = $KlimperToEditArray;
				$Written = true;
			}
		}
		// Write new climper
		if (!$Written) {
      array_push($this->Data, $KlimperToEditArray);
		}
	}

  private function KlimperDataStringToArray($KlimperDataString)
  {
  	$Array = explode(",",$KlimperDataString);
  	return $Array;
  }

	private function ReadDataFromFile()
  {
      $DataStringArray = file(FILE_DOODLE_DATA);
		$this->fs = fopen(FILE_DOODLE_DATA, "w");
    if ($this->fs == NULL) {
      echo "ERROR, Filepointer is NULL after construction\n";
    }
    else {
	    $i = 0;
	    foreach ($DataStringArray as $ClimperData) {
	      $KlimperArray = explode(",", $ClimperData);
	      $this->Data[$i] = $KlimperArray;
	      $i++;
      }
    }
  }

	private function WriteDataToFile()
	{
    foreach ($this->Data as $LineArray) {
    	$LineString = "";
    	for ($i=0; $i<(count($LineArray)-1); $i++) // -1, skip last crlf
    	{
        $LineString .= $LineArray[$i].",";
    	}
    	$LineString .= "\n";
    	fwrite($this->fs, $LineString);
    }
	}

}

?>
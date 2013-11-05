<?php

define("FILE_LAST_ACCESS", "lastaccess.csv");
define("FILE_DOODLE_DATA", "mydata.csv");
define("KLIMPER_DATA_ELEMENT_NAME", 0);
define("KLIMPER_DATA_ELEMENT_EMAIL", 1);
define("KLIMPER_DATA_ELEMENT_LAST", KLIMPER_DATA_ELEMENT_NAME);
define("AMOUNT_OF_DAYS", "7");

class CDataHandler
{
  private $Data = array(); // Array of klimper data
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
			for ($i = (KLIMPER_DATA_ELEMENT_LAST + 2); $i < count($LineArray); $i++) {
				$LineArray[$i] = trim($LineArray[$i]);
				if (!empty($LineArray[$i])) {
					$AtLeastOneEntryFound = true;
				}
			}
			if ($AtLeastOneEntryFound) { // only keep entry if there are active dates
        $NewKlimperArray = array();
				$NewKlimperArray[KLIMPER_DATA_ELEMENT_NAME] = $LineArray[KLIMPER_DATA_ELEMENT_NAME];
				for ($i = (KLIMPER_DATA_ELEMENT_LAST + 2); $i < count($LineArray); $i++) {
					$NewKlimperArray[KLIMPER_DATA_ELEMENT_LAST + $i - 1] = $LineArray[$i];
				}
        $LineArray = $NewKlimperArray;
			}
      else {
        unset($this->Data[$Key]);
      }
		}
	}

	public function IsKlimperInDatabase($KlimperNameToVerify)
  {
		foreach ($this->Data as $ActualKlimperData) {
      $ActualKlimperName = $ActualKlimperData[KLIMPER_DATA_ELEMENT_NAME];
			if ($ActualKlimperName == $KlimperNameToVerify) {
				return true;
			}
		}
		return false;
	}

	public function AddNewEmptyKlimperDataset($NewKlimperName)
	{
    // add empty day string
		$EmptyDayString = "";
		for ($i = 0; $i<AMOUNT_OF_DAYS; $i++) {
      $EmptyDayString .= ",";
    }
		$this->EditLine($NewKlimperName . $EmptyDayString . "\n");
	}

	// function introduced for testing purpose
	public function GetKlimperString($Klimper)
	{
		foreach ($this->Data as $ActualKlimperData) {
			$ActualKlimperName = $ActualKlimperData[KLIMPER_DATA_ELEMENT_NAME];
			if ($ActualKlimperName == $Klimper) {
				return $this->ConvertKlimperArrayToString($ActualKlimperData);
			}
		}
		return "";
	}

	private function ConvertKlimperArrayToString($DataSet)
	{
		$OutpurtString = "";
		for ($i=0; $i < (count($DataSet) - 1); $i++) { // explaining the -1: do not fill last element which contains the delimiter '\n'
			$DataElement = $DataSet[$i];
			$DataElement .= ",";
			$OutpurtString .= $DataElement;
		}
		return $OutpurtString;
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
			if ($KlimperToEditArray[KLIMPER_DATA_ELEMENT_NAME] == $ActualKlimperArray[KLIMPER_DATA_ELEMENT_NAME]) {
				$ActualKlimperArray = $KlimperToEditArray;
				$Written = true;
			}
		}
		// Write new klimper
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
	    foreach ($DataStringArray as $KlimperDataString) {
	      $KlimperArray = explode(",", $KlimperDataString);
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
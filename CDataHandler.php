<?php

define("FILE_LAST_ACCESS", "lastaccess.csv");
define("FILE_DOODLE_DATA", "mydata.csv");
define("FILE_LOCK", "lock");
define("KLIMPER_DATA_ELEMENT_NAME", 0);
define("KLIMPER_DATA_ELEMENT_EMAIL", 1);
define("KLIMPER_DATA_ELEMENT_LAST", KLIMPER_DATA_ELEMENT_NAME);
define("AMOUNT_OF_DAYS", "7");

class CDataHandler
{
  private $Data = array(); // Array of klimper data
  private $fs;
  private $Log;
  private $InstanceName = "";
  private $DataReadResult;
	private $BaseDir;

	public function __construct($InstanceName)
	{
		$this->BaseDir = getcwd();
		$this->Log = fopen("logfile.log", "a+");
		$this->InstanceName = $InstanceName;
		$this->Trace("Open DataHandler");
		$this->DataReadResult = $this->LoadData();
	}

	public function __destruct()
	{
		if ($this->fs == NULL) {
      $this->Trace("ERROR: Filepointer is NULL before destruction, LoadData was not called maybe.");
    }
    else {
      $this->WriteDataToFile();
      $this->funlock();
      fclose($this->fs);
    }
		$this->Trace("Close DataHandler");
	}

	// for testing purpose this function must be public
  public function LoadData()
  {
  	if (!$this->ReadDataFromFile()) {
  		$this->Trace("File could not be read!");
  		return false;
  	}
  	$this->Trace("File Read successfully");
  	return true;
  }

  public function GetDataReadResult()
  {
		return $this->DataReadResult;
  }

  public function GetAmountOfDays()
  {
  	return AMOUNT_OF_DAYS;
  }

  // public for testing purpose
	public function IncrementDay()
	{
		$NewDataArray = array();
		$NewDataArrayIndex = 0;
		foreach ($this->Data as $Key => $LineArray) {
 			$AtLeastOneEntryFound = false;
 			// Magic Number 2 = 1st element is Name, 2nd Element is the old day
			for ($i = (KLIMPER_DATA_ELEMENT_LAST + 2); $i < count($LineArray); $i++) {
				$LineArray[$i] = trim($LineArray[$i]);
				if (!empty($LineArray[$i])) {
					$AtLeastOneEntryFound = true;
				}
			}
			if ($AtLeastOneEntryFound) { // only keep entry if there are active dates
        $NewKlimperArray = array();
				$NewKlimperArray[KLIMPER_DATA_ELEMENT_NAME] = $LineArray[KLIMPER_DATA_ELEMENT_NAME];
				for ($i = (KLIMPER_DATA_ELEMENT_LAST + 2); $i < (count($LineArray)-1); $i++) {
					$NewKlimperArray[KLIMPER_DATA_ELEMENT_LAST + $i - 1] = $LineArray[$i];
				}
				$NewKlimperArray[count($LineArray)-2] = "";
				$NewKlimperArray[count($LineArray)-1] = $LineArray[count($LineArray)-1]; // copy \n
        $NewDataArray[$NewDataArrayIndex++] = $NewKlimperArray;
			}
		}
		$this->Data = $NewDataArray;
	}

	private function GetNumberOfEntries($KlimperData)
	{
		$FoundEntries = 0;
		for ($i = (KLIMPER_DATA_ELEMENT_LAST + 1); $i < count($KlimperData); $i++) {
			$KlimperData[$i] = trim($KlimperData[$i]);
			if (!empty($KlimperData[$i])) {
				$FoundEntries++;
			}
		}
// 		printf("\nClimper " . $KlimperData[0]);
// 		printf("\nNumbOfEntries: " . $FoundEntries);
		return $FoundEntries;
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

	private function GetLastAccess()
	{
		$LastAccess = file(FILE_LAST_ACCESS);
		$LastAccess = trim($LastAccess{0});
		return $LastAccess;
	}

	public function SetLastAccessAndIncrement()
	{
		$TodaysDay = @date(z);
		$LastAccess = $this->GetLastAccess();
		while($LastAccess != $TodaysDay) {
			$this->IncrementDay();
			$LastAccess++;
			if ($LastAccess > 365) {
				$LastAccess = 0;
			}
		}
		$fs = fopen(FILE_LAST_ACCESS, "w");
		fwrite($fs,$TodaysDay);
		fclose($fs);
	}

	private function CleanEntry($Entry) {
		$val = trim($Entry);
		$val = trim($val , ",");
		$val = str_replace(",", ":", $val);
		$val = "," . strip_tags($val);
		$val = substr($val, 0, 30);
		return $val;
	}

	public function EditLine($KlimperToEdit) { // KlimperToEdit: cs String
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
		$this->fs = fopen(FILE_DOODLE_DATA, "w+");
    if ($this->fs == NULL) {
    	$this->Trace("ERROR, Filepointer is NULL after construction!");
      return false;
    }
    if ($this->GetLock($this->fs)) {
	    $i = 0;
	    foreach ($DataStringArray as $KlimperDataString) {
	      $KlimperArray = explode(",", $KlimperDataString);
	      $this->Data[$i] = $KlimperArray;
	      $i++;
      }
    }
    else {
      return false;
    }
    return true;
  }

  public function GetNextClimper(&$NexClimperArray)
  {
  	static $Index = 0;
  	if (count($this->Data) > $Index) {
  		$NexClimperArray = $this->Data[$Index++];
  		return true;
  	}
  	else {
  		return false;
  	}
  }

  private function GetLock($fp)
  {
    $Timeout = 1000000; // timeout in us
    $WaitFor = 100000; // us (do not change)
    $this->Trace("Try to get FILE Lock");
    while (!$this->flock($fp, LOCK_EX) && ($Timeout > 0)) { //  && ($Timeout > 0)
      usleep($WaitFor);
      $Timeout -= $WaitFor;
    }
    if ($Timeout <= 0) {
    	$this->Trace("could not get lock (timeout occured)");
      return false; // get lock failed!!!
    }
    $this->Trace("Lock created, Timeout: " . $Timeout);
    return true;
  }

  private function flock($Unused1, $Unused2)
  {
    $LockFileName = FILE_LOCK;
    if (!file_exists($LockFileName)) {
      $LockFile = fopen($LockFileName, "w");
      $LockKey = $this->InstanceName;
      $LockEntry = $LockKey;
      $this->Trace("LockKey: " . $LockKey);
      fwrite($LockFile, $LockEntry);
      $TempLockFileContent = file($LockFileName);
      $VerifyLockKey = $TempLockFileContent[0];
      if ($VerifyLockKey != $LockKey) {
      	$this->Trace("LockKey not confirmed");
      	$this->Trace("Expected: " . $LockKey);
      	$this->Trace("Actual: " . $VerifyLockKey);
      	unlink($LockFileName);
      	return false;
      }
      $this->Trace("Lock created");
      return true;
    }
    else {
    	$this->Trace("File still locked");
      return false;
    }
  }

  private function funlock()
  {
  	$this->Trace("Try to unlock file");
  	$LockFileName = $this->BaseDir . "/" . FILE_LOCK;
  	if (file_exists($LockFileName)) {
      if (unlink($LockFileName)) {
      	$this->Trace("Unlock file successful");
      } else {
      	$this->Trace("Unlock file not successful");
      }
  	}
  	else {
  		$this->Trace("Tryed to unlock a file but file \"$LockFileName\" doesn't exists");
  	}
  }

	private function WriteDataToFile()
	{
    foreach ($this->Data as $LineArray) {
    	if ($this->GetNumberOfEntries($LineArray) > 0) {
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

	public function Trace($Message)
	{
// 		$date = new DateTime();
// 		$Time = $date->format('Y.m.d H:i:s');
// 		fwrite($this->Log, $Time . " | " . $this->InstanceName . " | " . $Message . "\n");
// 		flush();
	}

}

?>
<?php

include '../CDataHandler.php';

class testDataHandler extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
     copy(FILE_LAST_ACCESS, FILE_LAST_ACCESS.".bak");
     copy(FILE_DOODLE_DATA, FILE_DOODLE_DATA.".bak");
  }
  public function __destruct()
  {
      copy(FILE_LAST_ACCESS.".bak", FILE_LAST_ACCESS);
      copy(FILE_DOODLE_DATA.".bak", FILE_DOODLE_DATA);
  }

  public function testEditLine()
  {
  	$DataHandler = new CDataHandler();
  	$EditString = "Fritz,MO,DI,,,,,,";
  	$DataHandler->EditLine($EditString);
  	unset($DataHandler);
  	$this->assertFileEquals("mydata_EditLine.csv", FILE_DOODLE_DATA);
  }

	public function testGetLastAccess()
  {
    $DataHandler = new CDataHandler();
  }

  public function testIsClimperInDatabasePositive()
  {
    $DataHandler = new CDataHandler();
    $this->assertTrue($DataHandler->IsKlimperInDatabase("Franz"));
  }

  public function testIsClimperInDatabaseNegative()
  {
    $DataHandler = new CDataHandler();
    $this->assertFalse($DataHandler->IsKlimperInDatabase("Kurt"));
  }

  public function testAddNewEmptyKlimperDataset()
  {
  	$DataHandler = new CDataHandler();
  	$DataHandler->AddNewEmptyKlimperDataset("Kurt");
    // set up new DataHandler to ensure that the new dataset is saved in file
  	unset($DataHandler);
   	$DataHandler = new CDataHandler();
  	$this->assertTrue($DataHandler->IsKlimperInDatabase("Kurt"));
  	$ExpectedString = "Kurt,,,,,,,";
  	$ActualString = $DataHandler->GetKlimperString("Kurt");
  	echo "Actual String: " . $ActualString . "\n";
  	$this->assertEquals($ExpectedString, $ActualString);
  }

  public function testIncrementDay()
  {
    $DataHandler = new CDataHandler();
    $DataHandler->IncrementDay();
    // set up new DataHandler to ensure that the new dataset is saved in file
  	unset($DataHandler);
   	$DataHandler = new CDataHandler();
    $this->assertFalse($DataHandler->IsKlimperInDatabase("Franz")); // After increment for Franz no entry is left
  }

}


?>
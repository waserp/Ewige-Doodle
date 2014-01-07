<?php

include '../CDataHandler.php';

class testDataHandler extends PHPUnit_Framework_TestCase
{
  public function __construct()
  {
  	$this->BackupFiles();
  }
  public function __destruct()
  {
  	$this->RestoreFiles();
  }

  public function testGetNextClimper()
  {
  	$DataHandler = new CDataHandler("testGetNextClimper");
  	$ClimperArray = array();
  	$this->assertTrue($DataHandler->GetNextClimper($ClimperArray));
  	$this->assertEquals(9, count($ClimperArray));
  	$this->assertEquals("Franz", $ClimperArray[0]);
  	$this->assertTrue($DataHandler->GetNextClimper($ClimperArray));
  	$this->assertEquals("Fritz", $ClimperArray[0]);
  	$this->assertTrue($DataHandler->GetNextClimper($ClimperArray));
  	$this->assertEquals("Peter", $ClimperArray[0]);
  	$this->assertFalse($DataHandler->GetNextClimper($ClimperArray));
  }

  public function testEditLine()
  {
  	$DataHandler = new CDataHandler("1stInstance_testEditLine");
  	$EditString = "Fritz,MO,DI,,,,,,";
  	$DataHandler->EditLine($EditString);
  	unset($DataHandler);
  	$this->assertFileEquals("mydata_EditLine.csv", FILE_DOODLE_DATA);
  }

// 	public function testGetLastAccess()
//   {
//     $DataHandler = new CDataHandler("1stInstance_testGetLastAccess");
//   }

  public function testIsClimperInDatabasePositive()
  {
    $DataHandler = new CDataHandler("1stInstance_testIsClimperInDatabasePositive");
    $this->assertTrue($DataHandler->IsKlimperInDatabase("Franz"));
  }

  public function testIsClimperInDatabaseNegative()
  {
    $DataHandler = new CDataHandler("1stInstance_testIsClimperInDatabaseNegative");
    $this->assertFalse($DataHandler->IsKlimperInDatabase("Kurt"));
  }

  public function testAddNewEmptyKlimperDataset()
  {
  	$DataHandler = new CDataHandler("1stInstance_testAddNewEmptyKlimperDataset");
  	$DataHandler->AddNewEmptyKlimperDataset("Kurt");
    // set up new DataHandler to ensure that the new dataset is saved in file
  	unset($DataHandler);
   	$DataHandler = new CDataHandler("2ndInstance_testAddNewEmptyKlimperDataset");
  	$this->assertTrue($DataHandler->IsKlimperInDatabase("Kurt"));
  	$ExpectedString = "Kurt,,,,,,,";
  	$ActualString = $DataHandler->GetKlimperString("Kurt");
  	$this->assertEquals($ExpectedString, $ActualString);
  }

  public function testIncrementDay()
  {
  	$this->RestoreFiles();
    $DataHandler = new CDataHandler("1stInstance_testIncrementDay");
    $DataHandler->IncrementDay();
    // close DataHandler to ensure that the new dataset is saved in file
  	unset($DataHandler);
    copy(FILE_DOODLE_DATA, FILE_DOODLE_DATA.".test");
   	$this->assertFileEquals("mydata_IncrementDay.csv", FILE_DOODLE_DATA);
   	$DataHandler = new CDataHandler("2ndInstance_testIncrementDay");
    $this->assertFalse($DataHandler->IsKlimperInDatabase("Franz")); // After increment for Franz no entry is left
  }

  public function testPreventMultipleAccessToDatafile()
  {
    $DataReadSuccessful = false;
    $DataHandler1 = new CDataHandler("1stInstance_testPreventMultipleAccessToDatafile");
    $DataReadSuccessful = $DataHandler1->GetDataReadResult();
    $this->assertTrue($DataReadSuccessful, "Test first instance was created successfully");

    $DataReadSuccessful = false;
    $DataHandler2 = new CDataHandler("2ndInstance_testPreventMultipleAccessToDatafile");
    $DataReadSuccessful = $DataHandler2->GetDataReadResult();
    $this->assertFalse($DataReadSuccessful, "Test second instance is not created since 1st instance is still alive");

    $DataHandler1->Trace("unset first instance now");
   	unset($DataHander1); // unlock resource
    $DataHandler1 = null; // force destruction, garbage collector is far to slow!
    $DataReadSuccessful = false;
    $DataReadSuccessful = $DataHandler2->LoadData();
    $this->assertTrue($DataReadSuccessful, "Test Resource now availlable again");
  }

  private function BackupFiles()
  {
     copy(FILE_LAST_ACCESS, FILE_LAST_ACCESS.".bak");
     copy(FILE_DOODLE_DATA, FILE_DOODLE_DATA.".bak");
  }
  private function RestoreFiles()
  {
     copy(FILE_LAST_ACCESS.".bak", FILE_LAST_ACCESS);
     copy(FILE_DOODLE_DATA.".bak", FILE_DOODLE_DATA);
  }

}


?>
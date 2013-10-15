<?php

include '../CDoodleTable.php';

class testDoodleTable extends PHPUnit_Framework_TestCase
{
  public function testAddHeader()
  {
		$Table = new DoodleTable();
		$Array = array("H1", "H2", "H3");
		$Table->AddHeader($Array);
		$Expected = $this->GetStdTable("  <tr>\n    <th style=\"width:200px\">Klimper</th>\n    <th style=\"width:160px\">H1</th>\n    <th style=\"width:160px\">H2</th>\n    <th style=\"width:160px\">H3</th>\n  </tr>\n");
		$this->assertEquals($Expected, $Table->GetTable());
  }

  public function testPassObjectArray()
  {
  	$ObjArray = array(new ObjectArray("Text1"), new ObjectArray("Text2"));
  	$Table = new DoodleTable();
    $Table->AddRow("Klimper1", $ObjArray);
    $Expect = $this->GetStdTable("  <tr>\n    <td style=\"width:200px\">Klimper1</td>\n    <td style=\"width:160px\">Text1</td>\n    <td style=\"width:160px\">Text2</td>\n  </tr>\n");
    $this->assertEquals($Expect, $Table->GetTable());
  }

  public function testAddSingleCell()
  {
  	$Table = new DoodleTable();
  	$ActualArray = array();

  	$Array = array("H1", "H2", "H3");
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i]);
  	}
  	$ActualArray = $Table->GetCellArray();
  	$this->assertEquals($Array, $ActualArray);
  }

  public function testGetCellArrayReset()
  {
  	$Table = new DoodleTable();
  	$ActualArray = array();

  	$Array = array("H1", "H2", "H3");
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i]);
  	}
  	$ActualArray = $Table->GetCellArray();
		$this->assertEquals($Array, $ActualArray);
		// test the second time, reset function must have emptyed the array
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i]);
  	}
  	$ActualArray = $Table->GetCellArray();
		$this->assertEquals($Array, $ActualArray);
  }

  private function GetStdTable($Body)
  {
  	$Output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
  	$Output .= $Body;
  	$Output .= "</table>\n";
  	return $Output;
  }
}

class ObjectArray {
	private $m_Text = "";
	public function ObjectArray($Text) {
		$this->m_Text = $Text;
	}
	public function ToString() {
		return $this->m_Text;
	}
}

?>
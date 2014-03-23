<?php

include '../CDoodleTable.php';

class testDoodleTable extends PHPUnit_Framework_TestCase
{
  public function testAddSingleCell()
  {
  	$Table = new DoodleTable();
  	$ActualArray = array();

  	$Array = array("H1", "H2", "H3");
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i], CCell::COLOR_USER);
  	}

  	$ActualArray = $Table->GetCellArray();
  	$this->assertEquals(sizeof($Array), sizeof($ActualArray));
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Expected = "    <td style=\" width:180px; background-color:#6666FF\">" . $Array[$i] . "</td>\n";
  		$Actual = $ActualArray[$i]->ToString();
  		$this->assertEquals($Expected, $Actual);
  	}
  }

  public function testGetCellArrayReset()
  {
  	$Table = new DoodleTable();
  	$ActualArray = array();

  	$Array = array("H1", "H2", "H3");
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i], CCell::COLOR_USER);
  	}
		$ActualArray = $Table->GetCellArray();
		$this->assertEquals(sizeof($Array), sizeof($ActualArray));
		for ($i=0; $i < sizeof($Array); $i++) {
			$Expected = "    <td style=\" width:180px; background-color:#6666FF\">" . $Array[$i] . "</td>\n";
			$Actual = $ActualArray[$i]->ToString();
			$this->assertEquals($Expected, $Actual);
		}
		// test the second time, reset function must have emptyed the array
  	for ($i=0; $i < sizeof($Array); $i++) {
  		$Table->AddSingleCell($Array[$i], CCell::COLOR_USER);
  	}
  	$ActualArray = $Table->GetCellArray();
		$this->assertEquals(sizeof($Array), sizeof($ActualArray));
		for ($i=0; $i < sizeof($Array); $i++) {
			$Expected = "    <td style=\" width:180px; background-color:#6666FF\">" . $Array[$i] . "</td>\n";
			$Actual = $ActualArray[$i]->ToString();
			$this->assertEquals($Expected, $Actual);
		}
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
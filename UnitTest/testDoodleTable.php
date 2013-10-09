<?php

include '../CDoodleTable.php';

class testDoodleTable extends PHPUnit_Framework_TestCase
{
  public function testAddHeader()
  {
		$Table = new DoodleTable();
		$Array = array("H1", "H2", "H3");
		$Table->AddHeader($Array);
		$Expected = $this->GetStdTable("  <tr>\n    <th>H1</th>\n    <th>H2</th>\n    <th>H3</th>\n  </tr>\n");
		$this->assertEquals($Expected, $Table->GetTable());
  }

  public function testPassObjectArray()
  {
  	$ObjArray = array(new ObjectArray("Text1"), new ObjectArray("Text2"));
  	$Table = new DoodleTable();
    $Table->AddRow($ObjArray);
    $Expect = $this->GetStdTable("  <tr>\n    <td>Text1</td>\n    <td>Text2</td>\n  </tr>\n");
    $this->assertEquals($Expect, $Table->GetTable());
  }

  private function GetStdTable($Body)
  {
  	$Output = "<table width=\"200\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
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
<?php

include '../CFormHandler.php';

class testFormHandler extends PHPUnit_Framework_TestCase
{

	public function testEmptyForm()
	{
		$T = new FormHandler("post", "myID");

		$Expected = $this->ConcatForm("");
		$Actual = $T->StartForm() . $T->CloseForm();

		$this->assertEquals($Expected, $Actual);
	}

	public function testOneInput()
	{
		$T = new FormHandler("post", "myID");
		$T->AddElement("text", "TestElement", "TestName");
		$Expected = "<input type=\"text\" name=\"TestElement\" value=\"TestName\"/>\n";
		$this->assertEquals($Expected, $T->GetForm());
	}

	public function testTwoInputs()
	{
		$T = new FormHandler("post", "myID");
		$T->AddElement("text", "TestElement", "TestName");
		$T->AddElement("text", "TestElement1", "TestName1");

		$Expected = "<input type=\"text\" name=\"TestElement\" value=\"TestName\"/>\n";
		$Expected .= "<input type=\"text\" name=\"TestElement1\" value=\"TestName1\"/>\n";

		$this->assertEquals($Expected, $T->GetForm());
	}

	public function testOneSubmit()
	{
		$T = new FormHandler("post", "myID");
		$T->AddElement("submit", "OK", "ok");

		$Expected = "<input type=\"submit\" name=\"OK\" value=\"ok\"/>\n";

		$this->assertEquals($Expected, $T->GetForm());
	}

	private function ConcatForm($TestPart)
	{
		$Expected = "<form method=\"post\" id=\"myID\" action=\"\">\n";
		$Expected .= $TestPart;
		$Expected .= "</form>";
		return $Expected;
	}
}

?>
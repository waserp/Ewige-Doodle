<?php

include '../CFormHandler.php';

class testElement extends PHPUnit_Framework_TestCase
{
	public function testNewTextInput()
	{
		$Element = new Element("text", "Name", "Value");
		$Expected = "<input type=\"text\" name=\"Name\" value=\"Value\"/>\n";
		$this->assertEquals($Expected, $Element->ToString());
	}

	public function testNewSubmitInput()
	{
		$Element = new Element("submit", "Name", "Value");
		$Expected = "<input type=\"submit\" name=\"Name\" value=\"Value\"/>\n";
		$this->assertEquals($Expected, $Element->ToString());
	}

	public function testWrongType()
	{
		$Element = new Element("foo", "Name", "Value");
		$Expected = "";
		$this->assertEquals($Expected, $Element->ToString());
	}
}

?>
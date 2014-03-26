<?php

include '../CEwigerChat.php';

class testEwigerChat  extends PHPUnit_Framework_TestCase
{

	public function __construct()
	{
		$this->BackupFiles();
	}

	public function __destruct()
	{
		$this->RestoreFiles();
	}

	public function testReadCurrent()
	{
		$Chat = new CEwigerChat();
		$ActualText = $Chat->GetText();
		$this->assertEquals("Test\nZeile2", $ActualText);
	}

	public function testAddComment()
	{
		$Chat = new CEwigerChat();
		$NewText = "MyComment";
		$Chat->AddComment($NewText);
		unset($Chat);
		$Chat = new CEwigerChat();
		$ActualText = $Chat->GetText();
		$this->assertEquals("MyComment\n\nTest\nZeile2", $ActualText);
	}

	private function BackupFiles()
	{
		copy(FILE_CHAT_DATA, FILE_CHAT_DATA.".bak");
	}
	private function RestoreFiles()
	{
		copy(FILE_CHAT_DATA.".bak", FILE_CHAT_DATA);
	}


}

?>
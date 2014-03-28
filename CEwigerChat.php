<?php

define("FILE_CHAT_DATA", "chat.data");
define("MAX_FILE_LENGHT", 60);
define("SHRINK_FILE_TO", 48);

class CEwigerChat
{
	private $ChatData;

	public function __construct()
	{
	}

	public function __destruct()
	{
	}

	public function GetText()
	{
		$this->ChatData = file_get_contents(FILE_CHAT_DATA);
		return $this->ChatData;
	}

	public function AddComment($NewText, $User="")
	{
		$this->FileDoctor();
		if (!$this->TextDoctor($NewText)) {
			return;
		}
		$ChatData = date('d-M-Y H:i');
		if ($User != "") {
			$ChatData .= ", " . $User;
		}
		$ChatData .= ":\n";
    $ChatData .= $NewText."\n\n";
		$ChatData .= file_get_contents(FILE_CHAT_DATA);
		file_put_contents(FILE_CHAT_DATA, $ChatData);
	}

	// The TextDoctor modifies the text so it does not harm the programm.
	private function TextDoctor(&$Text)
	{
		$Text = strip_tags($Text);
		$Text = rtrim($Text);
		if (empty($Text)) {
			return false;
		}
		return true;
	}

	// The FileDoctor keeps the chat file in a proper size.
	private function FileDoctor()
	{
		$CountOfLines = count($f = file(FILE_CHAT_DATA));
		if ($CountOfLines > MAX_FILE_LENGHT) {
			$f = array_slice($f, 0, SHRINK_FILE_TO);
			file_put_contents(FILE_CHAT_DATA, implode("",$f));
		}
	}
}

?>
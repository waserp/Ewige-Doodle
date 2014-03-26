<?php

define("FILE_CHAT_DATA", "chat.data");

class CEwigerChat
{
	private $ChatData;

	public function __construct()
	{
// 		$Text = "";
// 		$fp = fopen(FILE_CHAT_DATA, "r");
// 		while (!feof($fp)) {
// 			$Text .= fgets($fp);
// 		}
// 		fclose($fp);
//		$this->ChatData = $Text;
	}

	public function __destruct()
	{

	}

	public function GetText()
	{
		$this->ChatData = file_get_contents(FILE_CHAT_DATA);
		return $this->ChatData;
	}

	public function AddComment($NewText)
	{
    $ChatData = $NewText."\n\n";
		$ChatData .= file_get_contents(FILE_CHAT_DATA);
		file_put_contents(FILE_CHAT_DATA, $ChatData);
	}
}

?>
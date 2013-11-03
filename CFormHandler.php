<?php

class FormHandler
{
	private $m_Methode;
	private $m_ID;
	private $m_Action = "";
	private $Elementes = array();
	private $FunctionElementes = array();

	function FormHandler($Methode, $ID)
	{
		$this->m_Methode = $Methode;
		$this->m_ID = $ID;
	}

	public function AddElement($Type, $Name, $Value, $ShownFieldName = "")
	{
		$NewElement = new Element($Type, $Name, $Value);
		if (!empty($ShownFieldName)) {
			$NewElement->SetShownFieldName($ShownFieldName);
		}
		array_push($this->Elementes, $NewElement);
		return $NewElement->ToString();
	}

	public function GetElementArray()
	{
		for ($i = 0; $i < sizeof($this->Elementes); $i++) {
			$tempArray[$i] = $this->Elementes[$i]->ToString();
		}
		$this->Elementes = array();
		return $tempArray;
	}

	public function GetElementArrayString()
	{
		$ElementArray = $this->GetElementArray();
		$StringOutput = array();
		foreach ($ElementArray as $Element) {
			array_push($StringOutput, $Element);
		}
		return $StringOutput;
	}

	function AddFunctionElement($Type, $Name, $Value)
	{
		array_push($this->FunctionElementes, new Element($Type, $Name, $Value));
	}

	function GetFunctionElementString()
	{
		$ElementString = "";
		foreach ($this->FunctionElementes as $El) {
			$ElementString .= $El->ToString();
		}
		return $ElementString;
	}

	function GetForm()
	{
		$Form = "";
		for ($i = 0; $i < count($this->Elementes); $i++) {
			$Form .= $this->Elementes[$i]->ToString();
		}

		return $Form;
	}

	public function StartForm()
	{
		return "<form method=\"" . $this->m_Methode . "\" id=\"" . $this->m_ID . "\" action=\"" . $this->m_Action . "\">\n";
	}

	public function CloseForm()
	{
		return "</form>";
	}
}

class Element
{
	private $m_Type;
	private $m_Name;
	private $m_Value = "";
	private $m_ShownFieldName = "";

	function Element($Type, $Name, $Value)
	{
		$this->m_Type = $Type;
		$this->m_Name = $Name;
		$this->m_Value = $Value;
	}

	public function SetShownFieldName($FieldName)
	{
		$this->m_ShownFieldName = $FieldName;
	}

	public function ToString()
	{
		$OutputText = "";
		if (!$this->ValidateType($this->m_Type)) {
			return "";
		}
		$OutputText = "<input type=\"" . $this->m_Type . "\" name=\"" . $this->m_Name . "\" value=\"" . $this->m_Value . "\"/>\n";
		if (!empty($this->m_ShownFieldName)) {
			$OutputText = $this->m_ShownFieldName . ": " . $OutputText . "<br>";
		}
		return $OutputText;
	}

	private function ValidateType($Type)
	{
		switch ($Type) {
			case "text":
			case "submit":
				return true;
			default:
				echo "Input type <b>$Type</b> doesn't exists or not yet implemented.";
				return false;
		}
	}
}



?>
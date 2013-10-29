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

	function AddElement($Type, $Name, $Value)
	{
		array_push($this->Elementes, new Element($Type, $Name, $Value));
		return $this->Elementes[sizeof($this->Elementes)-1]->ToString();
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

	function Element($Type, $Name, $Value)
	{
		$this->m_Type = $Type;
		$this->m_Name = $Name;
		$this->m_Value = $Value;
	}

	public function ToString()
	{
		if (!$this->ValidateType($this->m_Type)) {
			return "";
		}
		return "<input type=\"" . $this->m_Type . "\" name=\"" . $this->m_Name . "\" value=\"" . $this->m_Value . "\"/>\n";
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
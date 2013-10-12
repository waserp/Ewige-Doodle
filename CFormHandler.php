<?php

class FormHandler
{
	private $m_Methode;
	private $m_ID;
	private $m_Action = "";
	private $Elementes = array();

	function FormHandler($Methode, $ID)
	{
		$this->m_Methode = $Methode;
		$this->m_ID = $ID;
	}

	function AddElement($Type, $Name, $Value)
	{
		array_push($this->Elementes, new Element($Type, $Name, $Value));
	}

	public function GetElementArray()
	{
		return $this->Elementes;
	}

	function GetForm()
	{
		$Form = $this->GetFormHeaer();
		for ($i = 0; $i < count($this->Elementes); $i++) {
			$Form .= $this->Elementes[$i]->ToString();
		}
		$Form .= $this->GetFormEnd();

		return $Form;
	}

	public function GetFormHeaer()
	{
		return "<form method='" . $this->m_Methode . "' id='" . $this->m_ID . "' action='" . $this->m_Action . "'>\n";
	}

	private function GetFormEnd()
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
		return "<input type='" . $this->m_Type . "' name='" . $this->m_Name . "' value='" . $this->m_Value . "'/>\n";
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
<?php

class DoodleTable
{
  private $m_TableReadyToUse = false;
  private $Table = "";
	private $m_CellArray = array();

  public function DoodleTable()
  {
    $this->Table = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
  }

  public function AddMultipleCell($Array, $Color, $Type = RowType::Body)
  {
  	foreach ($Array as $SingleCell) {
  		$this->AddSingleCell($SingleCell, $Color, $Type);
  	}
  }

  public function AddSingleCell($Text, $Color, $Type = RowType::Body)
  {
  	$Cell = new CCell($Text);
  	$Cell->SetSize(180);
  	$Cell->SetColor($Color);
  	$Cell->SetType($Type);
  	array_push($this->m_CellArray, $Cell);
  }

  public function GetCellArray()
  {
  	$tempArray = $this->m_CellArray;
  	$this->m_CellArray = array();
  	return $tempArray;
  }

  public function AddRow($Array, $Type = RowType::Body)
  {
  	if (sizeof($Array) == 0) {
  		echo "ERROR: AddRow needs a array with at least one element.";
  		return 0;
  	}
  	if (gettype($Array[0]) == "string") {
  		foreach ($Array as $CellContent) {
  			$this->AddSingleCell($CellContent, CCell::COLOR_HEADER, $Type);
  		}
  		$Array = $this->GetCellArray();
  	}
  	else if (gettype($Array[0]) == "object") {
  		if (get_class($Array[0]) != "CCell") {
  			echo "ERROR: Object must be of type CCell";
  		}
  	}

    $this->Table .= "  <tr>\n" . $this->GenerateRow($Array) . "  </tr>\n";
  	$this->m_TableReadyToUse = true;
  }

  private function GenerateRow($Array)
  {
  	$Output = "";
  	for ($i = 0; $i < sizeof($Array); $i++) {
  	  if (method_exists($Array[$i], "ToString")) {
        $Text = $Array[$i]->ToString();
  	  } else {
  	  	$Text = $Array[$i];
  	  }
  	  $Output .= "    $Text";
    }
    return $Output;
  }

  public function GetTable()
  {
    if (!$this->m_TableReadyToUse) {
      return "Table not ready to use!";
    }
    $this->Table .= "</table>\n";
    return $this->Table;
  }


}

class RowType
{
  const TH = 1;
  const Header = 1;
  const TD = 2;
  const Body = 2;
}

class CCell
{
	const COLOR_NO_I_CANT = "FF6600"; // FF8080
	const COLOR_YES_I_CAN = "99CC00"; // 80FF80
	const COLOR_USER = "6666FF"; // E0EFEF
	const COLOR_HEADER = "0099FF"; // E0EFEF

	private $m_Content = "";
	private $m_Color = "";
	private $m_Size = "";
	private $m_Type;

	public function CCell($Content)
	{
		$this->m_Content = $Content;
		$this->m_Type = RowType::Body;
	}

	public function SetContent($Content)
	{
		$this->m_Content = $Content;
	}
	public function SetColor($Color)
	{
		$this->m_Color = $Color;
	}

	public function SetSize($Size)
	{
		$this->m_Size = $Size;
	}

	public function SetType($Type)
	{
		if ($this->GetTypeString($Type) != "") {
		  $this->m_Type = $Type;
		} else {
			echo "ERROR: Cell type not allowed! ($Type)";
		}
	}

	public function ToString()
	{
		$Type = $this->GetTypeString($this->m_Type);
		$CellString = "    <" . $Type . " style=\"";
		if (!empty($this->m_Size)) {
			$CellString .= " width:" . $this->m_Size . "px;";
		}
		if (!empty($this->m_Color)) {
			$CellString .= " background-color:#" . $this->m_Color . "";
		}
		$CellString .= "\">" . $this->m_Content . "</" . $Type . ">\n";
		return $CellString;
	}

	private function GetTypeString($RowType)
	{
		switch ($RowType) {
			case RowType::TH:
			case RowType::Header:
				return "th";
			case RowType::TD:
			case RowType::Body:
				return "td";
			default:
				return "";
		}
	}
}

?>
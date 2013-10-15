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

  public function AddSingleCell($Text)
  {
  	$Cell = new CCell($Text);
  	$Cell->SetSize(160);
  	$Cell->SetColor(CCell::COLOR_BLUE);
  	array_push($this->m_CellArray, $Cell);
  }

  public function GetCellArray()
  {
  	$tempArray = $this->m_CellArray;
  	$this->m_CellArray = array();
  	return $tempArray;
  }

  public function AddHeader($Array)
  {
  	$this->Table .= "  <tr>\n" . $this->GenerateRow($Array, RowType::Header) . "  </tr>\n";
  	$this->m_TableReadyToUse = true;
  }

  public function AddRow($Array)
  {
    $this->Table .= "  <tr>\n" . $this->GenerateRow($Array, RowType::Body) . "  </tr>\n";
  	$this->m_TableReadyToUse = true;
  }

  private function GenerateRow(	$Array, $RowType)
  {
    $Type = $this->GetTypeString($RowType);
  	$Output = "";
  	for ($i = 0; $i < sizeof($Array); $i++) {
  	  if (method_exists($Array[$i], "ToString")) {
        $Output .= $Array[$i]->ToString();
  	  } else {
  	  	$Text = $Array[$i];
  	  	$Output .= "    <$Type style=\"width:160px\">" . $Text . "</$Type>\n";
  	  }

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

class RowType
{
  const TH = 1;
  const Header = 1;
  const TD = 2;
  const Body = 2;
}

class CCell
{
	const COLOR_RED = "FF0000";
	const COLOR_GREEN = "00FF00";
	const COLOR_BLUE = "0000FF";

	private $m_Content = "";
	private $m_Color = "";
	private $m_Size = "";

	public function CCell($Content)
	{
		$this->m_Content = $Content;
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

	public function ToString()
	{
		$CellString = "    <td style=\"";
		if (!empty($this->m_Size)) {
			$CellString .= " width:" . $this->m_Size . "px;";
		}
		if (!empty($this->m_Color)) {
			$CellString .= " background-color:#" . $this->m_Color . "";
		}
		$CellString .= "\">" . $this->m_Content . "</td>\n";
		return $CellString;
	}
}

?>
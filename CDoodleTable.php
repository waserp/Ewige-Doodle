<?php

class DoodleTable
{
  private $TableReadyToUse = false;
  private $Table = "";

  public function DoodleTable()
  {
    $this->Table = "<table width=\"200\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
  }

  public function AddHeader($Array)
  {
  	$this->Table .= "  <tr>\n" . $this->GenerateRow($Array, RowType::Header) . "  </tr>\n";
  	$this->TableReadyToUse = true;
  }

  public function AddRow($Array)
  {
    $this->Table .= "  <tr>\n" . $this->GenerateRow($Array, RowType::Body) . "  </tr>\n";
  	$this->TableReadyToUse = true;
  }

  private function GenerateRow($Array, $RowType)
  {
  	$Output = "";
    $T = $this->GetTypeString($RowType);
  	for ($i = 0; $i < sizeof($Array); $i++) {
  	  if (method_exists($Array[$i], "ToString")) {
        $Text = $Array[$i]->ToString();
  	  } else {
  	  	$Text = $Array[$i];
  	  }
  		$Output .= "    <$T>" . $Text . "</$T>\n";
    }
    return $Output;
  }

  public function GetTable()
  {
    if (!$this->TableReadyToUse) {
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

?>
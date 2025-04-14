<?php
require_once('tcpdf/config/tcpdf_config.php');
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
   public function Header() {
parent::Header();
$this->SetFont('helvetica', '', 8);
$this->SetXY(170,12,true);
$this->Cell(30, 0, 'Proforma', 0, 0, 'L', false, '', 0, false, 'T', 'T');
$this->SetFont('helvetica', 'B', 30);
$this->SetXY(185,5,true);
$this->Cell(30, 0, 'S', 0, 0, 'L', false, '', 0, false, 'T', 'T');
/*
        // Logo
        $image_file = K_PATH_IMAGES.'mulogorep.jpg';//PDF_HEADER_LOGO
        $this->Image($image_file, 10, 5, PDF_HEADER_LOGO_WIDTH, 0, 'JPG', '', 'T', false, 300, 'L', false, false, 0, false, false, false, false, array());
        // Set font
        $this->SetFont('helvetica', 'B', 11);
        // Title
        $this->Cell(30, 0, 'University of Mumbai', 0, 0, 'L', false, '', 0, false, 'T', 'T');
TCPDF::Ln();
$this->SetFont('helvetica', '', 10);
$this->Cell(30, 0, 'Summary Sheet', 0, 0, 'L', false, '', 0, false, 'T', 'T');
$this->Cell(0, 15, 'Coll', 0, false, 'C', 0, '', 0, false, 'M', 'M'); */
    }
/*
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }*/
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING.' ', array(0,0,0), array(0,0,0));
$pdf->setFooterData(array(0,0,0), array(0,0,0));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN+3));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->SetFont('helvetica', '', 10);

function customDie() {
    header('Location: /ss/inputssbe7a.php');
    exit();
}
$html="";
$dbName = "hallticket";
$conn = mysql_connect("208.91.198.197:3306", "dbhallticket", "dbh@11tick3t1114") or die();
mysql_select_db($dbName) or die();
list($collno,$collname) = explode("#",$_POST['coll']);
$sql = "SELECT distinct `sub_cd`, `sub_name` FROM `be7_sub` where sub_cd<7";
$result = mysql_query($sql) or die();
$i=0;
while($row = mysql_fetch_array($result)){
$subj[$i]=$row;
$i=$i+1;
}
//print_r($subj);
foreach ($subj as $sub){
$html=$html."<table><tr><td>College Code and Name: </td><td colspan=\"2\"><strong>$collno. $collname</strong></td></tr><tr><td>Examination Code and Name: </td><td colspan=\"2\"><strong>517. F.E.SEM II (CBGS) NOV. 2014</strong></td></tr></table>";
	
	$sql = "SELECT `SEAT_NO`,`NAME` FROM `se7` WHERE COLL_NO='$collno' AND PP$sub[0] = $sub[0] AND MT$sub[0]='' order by `SEAT_NO`";
	$result = mysql_query($sql) or die();
$numr=mysql_num_rows($result);
$html=$html."<br><br><table cellpadding=\"2\"><tr><td colspan=\"2\"><h3>$sub[0]. $sub[1]</h3></td><td width=\"30%\"><h4 align=\"right\">Question Paper Code: &nbsp;&nbsp; </h4></td><td width=\"20%\" style=\" border-style: solid;border-width: 1px 1px 1px 1px; \"></td></tr></table><br>";
$html=$html."<h3>Summary</h3><br><font size=\"12\"><table cellpadding =\"2\" border=\"1\"><tr align=\"center\"><td width=\"5%\">1.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Candidates Allotted</td><td>$numr</td></tr>
<tr align=\"center\"><td width=\"5%\">2.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Additional Candidates (if any)</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">3.</td><td align=\"left\"colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Candidates (1+2)</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">4.</td><td align=\"left\"colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Candidates Present</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">5.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Candidates Absent</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">6.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of Candidates of unfair means (Envelope B)</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">7.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of Candidates of Learning Disability (Envelope C)</td><td></td></tr>
<tr align=\"center\"><td width=\"5%\">8.</td><td align=\"left\" colspan=\"3\" width=\"75%\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Answer booklets in Envelope A [4.-(6.+7.)]</td><td></td></tr>
</table>";
$html=$html."<br><br><table cellpadding =\"2\" border=\"1\">
<tr><td colspan=\"3\" align=\"center\">Seat Numbers (Unfair means Candidates)</td></tr>
<tr><td colspan=\"3\" height=\"30px\">&nbsp;</td></tr>
</table>";
$html=$html."<br><br><table cellpadding =\"2\" border=\"1\">
<tr><td  colspan=\"3\" align=\"center\">Seat Numbers (Learning Disability Candidates)</td></tr>
<tr><td colspan=\"3\" height=\"30px\"></td></tr>
</table>";
$html=$html."<br><br><table cellpadding =\"2\" border=\"1\"><tr><td colspan=\"5\" align=\"center\">Absent Seat Numbers</td></tr>
<tr><td colspan=\"5\" height=\"100px\"></td></tr>
</table></font>";
        $html=$html."<br>";
$html=$html."<h4>NOTE:Paste one copy on each envelope</h4><pre>


</pre>";
$html=$html."<table border=\"1\"><tr><td align=\"right\">____________________________ <br><br></td></tr><tr><td align=\"right\">PRINCIPAL/EXAM COORDINATOR</td></tr></table>";
        $pdf->AddPage();
        $pdf->writeHTML($html, true, 0, true, 0);
        $html="";
}
//echo $html;
mysql_close($conn);
$pdf->lastPage();
$pdf->Output('FE_SEM_II_CBGS_NOV_2014.pdf', 'I');
?>
<?php
require_once(__DIR__ . '/../../tcpdf/lib/tcpdf/tcpdf.php');

class LearnstonesPDF
{
    private $pdf;

    private $styles = "";

    private $html = "";

    function __construct($title, $author, $link)
    {   
        // create new PDF document
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // set document information
        $this->pdf->SetCreator('Learnstones');
        $this->pdf->SetAuthor($author);
        $this->pdf->SetTitle($title);
        $this->pdf->SetSubject($title);
        $this->pdf->SetKeywords('Tags');

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, "Author: $author\nLink: $link");

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        //if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	    //    require_once(dirname(__FILE__).'/lang/eng.php');
	    //    $this->pdf->setLanguageArray($l);
        //}

        // ---------------------------------------------------------
        // set font
        $this->pdf->SetFont('helvetica', '', 10);

        $this->pdf->AddPage();
    }

    function add_css( $cssfile )
    {
        $css = @file_get_contents($cssfile);
        if(!empty($css))
        {
            $this->styles .= $css;                    
        }
     }

    // output the HTML content
    function add_slide( $html, $lights )
    {
        $this->html .= '<table nobr="true"><tr><td>' . $html . "</td></tr><tr><td>" . $lights . "</td></tr></table>";        
    }

    //Close and output PDF document
    function output($filename)
    {
        // reset pointer to the last page
        $x = "<style>" . $this->styles . "</style>" . $this->html;
        $this->pdf->writeHtml($x, TRUE, FALSE, TRUE, FALSE, '');
        $this->pdf->lastPage();
        $this->pdf->Output($filename . '.pdf', 'I');
    }
}
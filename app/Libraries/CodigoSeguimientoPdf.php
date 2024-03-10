<?php
namespace App\Libraries;

use TCPDF;

class CodigoSeguimientoPdf extends TCPDF{

    //Page header
    public function Header() {
        $image_file = 'assets/seguimiento/img/banner.png';
        $this->Image($image_file, 0, 2, 200, '', 'PNG', '', '', false, 300, 'C', false, false, 0, false, false, false);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-36);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'-'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $image_file = 'assets/seguimiento/img/pie.png';
        $this->Image($image_file, 0, 270, 200, '', 'PNG', '', '', false, 300, 'C', false, false, 0, false, false, false);        
    }

}
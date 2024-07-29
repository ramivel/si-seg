<?php
namespace App\Libraries;

use TCPDF;

class LibroRegistroPdf extends TCPDF{

    //Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 10);
        // Title
        $this->Cell(0, 15, 'LIBRO DE REGISTROS', 0, false, 'C');
        $this->Ln(12);
        $cabecera = array(
            array('tamanio' => 75,'texto' => 'Correlativo'),
            array('tamanio' => 95,'texto' => 'Tipo Trámite'),
            array('tamanio' => 55,'texto' => 'Fecha Ingreso'),
            array('tamanio' => 75,'texto' => 'Destinatario'),
            array('tamanio' => 90,'texto' => 'Proveido'),
            array('tamanio' => 124,'texto' => 'Derivado'),
            array('tamanio' => 124,'texto' => 'Derivado'),
            array('tamanio' => 124,'texto' => 'Derivado'),
        );
        $html = '<table border="1" cellpadding="3" cellspacing="0"><tr>';
        foreach($cabecera as $row)
            $html .= '<th align="center" width="'.$row['tamanio'].'">'.$row['texto'].'</th>';
        $html .= '</tr></table>';
        $this->SetFont('helvetica', 'B', 8);
        $this->writeHTML($html, true, false, false, false, '');
    }

    // Page footer
    public function Footer() {
        //$this->SetY(-36);
        //$this->SetFont('helvetica', 'I', 8);
        //$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'-'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        //$image_file = 'assets/images/mineria_ilegal/pie.png';
        //$this->Image($image_file, 0, 270, 200, '', 'PNG', '', '', false, 300, 'C', false, false, 0, false, false, false);
    }

}
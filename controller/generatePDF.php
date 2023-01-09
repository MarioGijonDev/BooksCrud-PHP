<?php
require_once('controller/sqlite_table.php');
require_once("model/DBConnection.php");

class PDF extends PDF_SQLite_Table{
    function Header(){
        $this->SetTextColor(0, 0, 0);
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(20, 5, 'Column order: ' . $_GET['columnOrder']);
        $this->Ln();
        $this->Cell(20, 5, 'Order option: ' . $_GET['orderOption']);
        $this->Ln();
        $this->Cell(20, 5, utf8_decode('Nº Book records: ' . checkRecords()[1]));
        $this->Ln(10);
        // Title
        $this->SetFont('Arial', 'B', 18);
        $this->setTextColor(25,174, 194);
        $this->Cell(0, 5, utf8_decode('List of books'), 0, 0, 'C');
        $this->Ln(10);

        $this->SetFontSize(12);
        $this->SetFont('Arial', 'B');
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(40, 40, 40);
        $this->SetDrawColor(88, 88, 88);
        $this->Cell(29, 19, 'ISBN', 0, 0, 'C', 1);
        $this->Cell(90, 19, 'Title', 0, 0, 'C', 1);
        $this->Cell(30, 19, 'Author', 0, 0, 'C', 1);
        $this->Cell(50, 19, 'Publisher', 0, 0, 'C', 1);
        $this->SetDrawColor(61, 174, 233);
        $this->setLineWidth(1);
        $this->Line(11, 55, 208, 55);
        $this->SetTextColor(0,0,0);
        // Ensure table header is printed
        parent::Header();
    }

    function Footer(){
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 10);
        // Print centered page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

function checkRecords(){
    if(empty($_GET['records']))
        return ["", "All"];
    else
        return [' LIMIT ' .$_GET['records'], $_GET['records']];
}

function generatePDF(){

    $db = new DBConnection('booksDB.db');

    $data = $db->select('SELECT isbn, title, author, publisher FROM Books ORDER BY '. $_GET['columnOrder'] . ' ' . $_GET['orderOption'] . checkRecords()[0]);
    
    $pdf = new PDF();
    $pdf->AddPage('portrait', 'letter');
    $pdf->setFont('Arial', 'B', 12);

    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->SetDrawColor(255, 255, 255);
    $pdf->Ln();

    $pdf->SetFontSize(8);

    foreach($data as $book){
        $pdf->Cell(29, 12, $book['isbn'], 1, 0, 'C', 1);
        $pdf->Cell(90, 12, $book['title'], 1, 0, 'C', 1);
        $pdf->Cell(30, 12, $book['author'], 1, 0, 'C', 1);
        $pdf->Cell(50, 12, $book['publisher'], 1, 0, 'C', 1);
        $pdf->Ln();
    }

    $pdf->Output();
}
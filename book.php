<?php
include_once("src/lib/mpdf/mpdf.php");

$mpdf = new mPDF('', 'A3-L', 7, 'Arial', 15, 15, 25, 20, 10, 10, 'L');

$mpdf->Bookmark('Section 1', 0);
$mpdf->WriteHTML('<div>Section 1 text</div>');

$mpdf->Bookmark('Chapter 1', 1);
$mpdf->WriteHTML('<div>Chapter 1 text</div>');

$mpdf->Bookmark('Chapter 2', 1);
$mpdf->WriteHTML('<div>Chapter 2 text</div>');

$mpdf->Bookmark('Section 2', 0);
$mpdf->WriteHTML('<div>Section 2 text</div>');

$mpdf->Bookmark('Chapter 3', 1);
$mpdf->WriteHTML('<div>Chapter 3 text</div>');

$mpdf->Output('filename.pdf');

?>
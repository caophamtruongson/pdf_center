<?php
if (PHP_SAPI != "cli") exit;

// initial settings
error_reporting(E_ERROR);
set_include_path(sprintf('%s%s%s%s%s', get_include_path(),
                         PATH_SEPARATOR,
                         sprintf('%s/%s', getcwd(), 'fpdi'),
                         PATH_SEPARATOR,
                         sprintf('%s/%s', getcwd(), 'tcpdf')));
// definitions
define('_A3L_WIDTH_',        420);
define('_A3L_HEIGHT_',       297);

// libaries.
require_once('tcpdf.php');
require_once('fpdi.php');


// file settings.
$files = get_parameters();
// for test
//$files['src'] = 'realReport-litleShop.pdf';
//$files['dst'] = 'realReport-litleShop_new.pdf';


// convert to A3-L
$pdf = new FPDI('L', 'mm', 'A3');
$pdf->AddPage();
$pdf->setSourceFile($files['src']);
$tplIdx = $pdf->importPage(1);
$size = $pdf->useTemplate($tplIdx);
$pdf->Output($files['dst'], 'I');


function get_parameters()
{
    if ( !isset($_SERVER['argc']) or $_SERVER['argc'] !== 3 ) {
        echo "Error: wrong parametes.\nUsage: $ conv.php srcfile dstfile\n";
        exit;
    }
    return array('src' => $_SERVER['argv'][1],
                 'dst' => $_SERVER['argv'][2]);
}



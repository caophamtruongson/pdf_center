<?php
/**
 * PDF A3 converter.
 *  - usage :
 *     $ php conv_douvle.php src_file dst_file > dst_file
 */

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
define('_DEFAULT_MARGINE_',   10);
define('_PAGE_SIZE_',       'A3');
define('_PAGE_ORIENTATION_', 'L');
define('_PAGE_UNIT_',       'mm');

// requirements
require_once('tcpdf.php');
require_once('fpdi.php');

///////////////////////////////////////////////////////////////////////////////
/* main */

// file settings.
$files = get_parameters();

// check margin
$pdf = new FPDI();
$pdf->AddPage();
$pdf->setSourceFile($files['src']);
$tplIdx = $pdf->importPage(1);
$size = $pdf->useTemplate($tplIdx);
$margin = get_margin($pdf, $size);
unset($pfd);

// reload the source and set margine
$pdf = new FPDI(_PAGE_ORIENTATION_, _PAGE_UNIT_, _PAGE_SIZE_);
$pdf->setPrintHeader( false );
$pdf->AddPage();
$pdf->setSourceFile($files['src']);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, $margin['left'], $margin['top'],0,0,true);
$pdf->Output($files['dst'], 'I');

////////////////////////////////////////////////////////////////////////////////

function get_margin($pdf, array $size)
{
    $m = array('top'    => 0,
               'bottom' => 0,
               'left'   => 0,
               'right'  => 0);

    $diff_w = _A3L_WIDTH_  - (int)($size['w']);
    $diff_h = _A3L_HEIGHT_ - (int)($size['h']);
    if ($diff_w < 0) $diff_w = 0;
    if ($diff_h < 0) $diff_h = 0;
    
    $m['left'] = $m['right'] = $diff_w / 2;
    $m['top'] = $m['bottom'] = $diff_h / 2;

    return $m;
}


function get_parameters()
{
    if ( !isset($_SERVER['argc']) or $_SERVER['argc'] !== 3 ) {
        echo "Error: wrong parametes.\nUsage: $ conv.php srcfile dstfile > dstfile\n";
        exit;
    }
    return array('src' => $_SERVER['argv'][1],
                 'dst' => $_SERVER['argv'][2]);
}

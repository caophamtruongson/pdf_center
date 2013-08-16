<?php
/**
 * PDF A3 converter.
 *  - usage :
 *     $ php conv_douvle.php src_file dst_file > dst_file
 */
// cli check and exception setting
if (PHP_SAPI != "cli") exit;
error_reporting(E_ERROR);

// initial settings
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

// return code
define('_RET_OK_',                          0);
define('_RET_CANNOT_OPEN_SRC_FILE_',        100);
define('_RET_CANNOT_GET_TEMPLATE_',         101);
define('_RET_CANNOT_SET_TEMPLATE_',         102);
define('_RET_CANNOT_OPEN_FIRST_PAGE_',      103);
define('_RET_CANNOT_OPEN_DEFAULT_BUFFER_',  104);
define('_RET_CANNOT_SAVE_DST_',             105);

// requirements
require_once('tcpdf.php');
require_once('fpdi.php');


///////////////////////////////////////////////////////////////////////////////
/* main */
// file settings.
$files = get_parameters();

// check margin
try {
    $pdf = new FPDI();
    $pdf->AddPage();
    $ret = $pdf->setSourceFile($files['src']);
    $tplIdx = $pdf->importPage(1);
    $size = $pdf->useTemplate($tplIdx);
    $margin = get_margin($pdf, $size);
    unset($pfd);
} catch (Exception $e) {
    exit(_RET_CANNOT_OPEN_SRC_FILE_);
}


// reload the source and set margine
try {
    $pdf = new FPDI(_PAGE_ORIENTATION_, _PAGE_UNIT_, _PAGE_SIZE_);
    $pdf->setPrintHeader( false );
    $pdf->AddPage();
} catch (Exception $e) {
    exit(_RET_CANNOT_OPEN_DEFAULT_BUFFER_);
}
try {
    $ret = @$pdf->setSourceFile($files['src']);
} catch (Exception $e) {
    exit(_RET_CANNOT_OPEN_SRC_FILE_);
}
try {
    $tplIdx = $pdf->importPage(1);
} catch (Exception $e) {
    exit(_RET_CANNOT_OPEN_FIRST_PAGE_);
}
try {
    $ret = $pdf->useTemplate($tplIdx, $margin['left'], $margin['top'],0,0,true);
} catch (Exception $e) {
    exit(_RET_CANNOT_SET_TEMPLATE_);
}
try {
    $ret = $pdf->Output($files['dst'], 'F');
} catch (Exception $e) {
    exit(_RET_CANNOT_SAVE_DST_);
}
exit(_RET_OK_);


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

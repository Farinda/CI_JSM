<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Reader_Excel5
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of ParseXL (used as the base for this class):
// --------------------------------------------------------------------------------
// Adapted from Excel_Spreadsheet_Reader developed by users bizon153,
// trex005, and mmp11 (SourceForge.net)
// http://sourceforge.net/projects/phpexcelreader/
// Primary changes made by canyoncasa (dvc) for ParseXL 1.00 ...
//     Modelled moreso after Perl Excel Parse/Write modules
//     Added Parse_Excel_Spreadsheet object
//         Reads a whole worksheet or tab as row,column array or as
//         associated hash of indexed rows and named column fields
//     Added variables for worksheet (tab) indexes and names
//     Added an object call for loading individual woorksheets
//     Changed default indexing defaults to 0 based arrays
//     Fixed date/time and percent formats
//     Includes patches found at SourceForge...
//         unicode patch by nobody
//         unpack("d") machine depedency patch by matchy
//         boundsheet utf16 patch by bjaenichen
//     Renamed functions for shorter names
//     General code cleanup and rigor, including <80 column width
//     Included a testcase Excel file and PHP example calls
//     Code works for PHP 5.x

// Primary changes made by canyoncasa (dvc) for ParseXL 1.10 ...
// http://sourceforge.net/tracker/index.php?func=detail&aid=1466964&group_id=99160&atid=623334
//     Decoding of formula conditions, results, and tokens.
//     Support for user-defined named cells added as an array "namedcells"
//         Patch code for user-defined named cells supports single cells only.
//         NOTE: this patch only works for BIFF8 as BIFF5-7 use a different
//         external sheet reference structure
class PHPExcel_Reader_Excel5 extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    // ParseXL definitions
    const XLS_BIFF8                     = 0x0600;
    const XLS_BIFF7                     = 0x0500;
    const XLS_WorkbookGlobals           = 0x0005;
    const XLS_Worksheet                 = 0x0010;

    // record identifiers
    const XLS_TYPE_FORMULA              = 0x0006;
    const XLS_TYPE_EOF                  = 0x000a;
    const XLS_TYPE_PROTECT              = 0x0012;
    const XLS_TYPE_OBJECTPROTECT        = 0x0063;
    const XLS_TYPE_SCENPROTECT          = 0x00dd;
    const XLS_TYPE_PASSWORD             = 0x0013;
    const XLS_TYPE_HEADER               = 0x0014;
    const XLS_TYPE_FOOTER               = 0x0015;
    const XLS_TYPE_EXTERNSHEET          = 0x0017;
    const XLS_TYPE_DEFINEDNAME          = 0x0018;
    const XLS_TYPE_VERTICALPAGEBREAKS   = 0x001a;
    const XLS_TYPE_HORIZONTALPAGEBREAKS = 0x001b;
    const XLS_TYPE_NOTE                 = 0x001c;
    const XLS_TYPE_SELECTION            = 0x001d;
    const XLS_TYPE_DATEMODE             = 0x0022;
    const XLS_TYPE_EXTERNNAME           = 0x0023;
    const XLS_TYPE_LEFTMARGIN           = 0x0026;
    const XLS_TYPE_RIGHTMARGIN          = 0x0027;
    const XLS_TYPE_TOPMARGIN            = 0x0028;
    const XLS_TYPE_BOTTOMMARGIN         = 0x0029;
    const XLS_TYPE_PRINTGRIDLINES       = 0x002b;
    const XLS_TYPE_FILEPASS             = 0x002f;
    const XLS_TYPE_FONT                 = 0x0031;
    const XLS_TYPE_CONTINUE             = 0x003c;
    const XLS_TYPE_PANE                 = 0x0041;
    const XLS_TYPE_CODEPAGE             = 0x0042;
    const XLS_TYPE_DEFCOLWIDTH          = 0x0055;
    const XLS_TYPE_OBJ                  = 0x005d;
    const XLS_TYPE_COLINFO              = 0x007d;
    const XLS_TYPE_IMDATA               = 0x007f;
    const XLS_TYPE_SHEETPR              = 0x0081;
    const XLS_TYPE_HCENTER              = 0x0083;
    const XLS_TYPE_VCENTER              = 0x0084;
    const XLS_TYPE_SHEET                = 0x0085;
    const XLS_TYPE_PALETTE              = 0x0092;
    const XLS_TYPE_SCL                  = 0x00a0;
    const XLS_TYPE_PAGESETUP            = 0x00a1;
    const XLS_TYPE_MULRK                = 0x00bd;
    const XLS_TYPE_MULBLANK             = 0x00be;
    const XLS_TYPE_DBCELL               = 0x00d7;
    const XLS_TYPE_XF                   = 0x00e0;
    const XLS_TYPE_MERGEDCELLS          = 0x00e5;
    const XLS_TYPE_MSODRAWINGGROUP      = 0x00eb;
    const XLS_TYPE_MSODRAWING           = 0x00ec;
    const XLS_TYPE_SST                  = 0x00fc;
    const XLS_TYPE_LABELSST             = 0x00fd;
    const XLS_TYPE_EXTSST               = 0x00ff;
    const XLS_TYPE_EXTERNALBOOK         = 0x01ae;
    const XLS_TYPE_DATAVALIDATIONS      = 0x01b2;
    const XLS_TYPE_TXO                  = 0x01b6;
    const XLS_TYPE_HYPERLINK            = 0x01b8;
    const XLS_TYPE_DATAVALIDATION       = 0x01be;
    const XLS_TYPE_DIMENSION            = 0x0200;
    const XLS_TYPE_BLANK                = 0x0201;
    const XLS_TYPE_NUMBER               = 0x0203;
    const XLS_TYPE_LABEL                = 0x0204;
    const XLS_TYPE_BOOLERR              = 0x0205;
    const XLS_TYPE_STRING               = 0x0207;
    const XLS_TYPE_ROW                  = 0x0208;
    const XLS_TYPE_INDEX                = 0x020b;
    const XLS_TYPE_ARRAY                = 0x0221;
    const XLS_TYPE_DEFAULTROWHEIGHT     = 0x0225;
    const XLS_TYPE_WINDOW2              = 0x023e;
    const XLS_TYPE_RK                   = 0x027e;
    const XLS_TYPE_STYLE                = 0x0293;
    const XLS_TYPE_FORMAT               = 0x041e;
    const XLS_TYPE_SHAREDFMLA           = 0x04bc;
    const XLS_TYPE_BOF                  = 0x0809;
    const XLS_TYPE_SHEETPROTECTION      = 0x0867;
    const XLS_TYPE_RANGEPROTECTION      = 0x0868;
    const XLS_TYPE_SHEETLAYOUT          = 0x0862;
    const XLS_TYPE_XFEXT                = 0x087d;
    const XLS_TYPE_PAGELAYOUTVIEW       = 0x088b;
    const XLS_TYPE_UNKNOWN              = 0xffff;

    // Encryption type
    const MS_BIFF_CRYPTO_NONE           = 0;
    const MS_BIFF_CRYPTO_XOR            = 1;
    const MS_BIFF_CRYPTO_RC4            = 2;
    
    // Size of stream blocks when using RC4 encryption
    const REKEY_BLOCK                   = 0x400;

    /**
     * Summary Information stream data.
     *
     * @var string
     */
    private $summaryInformation;

    /**
     * Extended Summary Information stream data.
     *
     * @var string
     */
    private $documentSummaryInformation;

    /**
     * User-Defined Properties stream data.
     *
     * @var string
     */
    private $userDefinedProperties;

    /**
     * Workbook stream data. (Includes workbook globals substream as well as sheet substreams)
     *
     * @var string
     */
    private $data;

    /**
     * Size in bytes of $this->data
     *
     * @var int
     */
    private $dataSize;

    /**
     * Current position in stream
     *
     * @var integer
     */
    private $pos;

    /**
     * Workbook to be returned by the reader.
     *
     * @var PHPExcel
     */
    private $phpExcel;

    /**
     * Worksheet that is currently being built by the reader.
     *
     * @var PHPExcel_Worksheet
     */
    private $phpSheet;

    /**
     * BIFF version
     *
     * @var int
     */
    private $version;

    /**
     * Codepage set in the Excel file being read. Only important for BIFF5 (Excel 5.0 - Excel 95)
     * For BIFF8 (Excel 97 - Excel 2003) this will always have the value 'UTF-16LE'
     *
     * @var string
     */
    private $codepage;

    /**
     * Shared formats
     *
     * @var array
     */
    private $formats;

    /**
     * Shared fonts
     *
     * @var array
     */
    private $objFonts;

    /**
     * Color palette
     *
     * @var array
     */
    private $palette;

    /**
     * Worksheets
     *
     * @var array
     */
    private $sheets;

    /**
     * External books
     *
     * @var array
     */
    private $externalBooks;

    /**
     * REF structures. Only applies to BIFF8.
     *
     * @var array
     */
    private $ref;

    /**
     * External names
     *
     * @var array
     */
    private $externalNames;

    /**
     * Defined names
     *
     * @var array
     */
    private $definedname;

    /**
     * Shared strings. Only applies to BIFF8.
     *
     * @var array
     */
    private $sst;

    /**
     * Panes are frozen? (in sheet currently being read). See WINDOW2 record.
     *
     * @var boolean
     */
    private $frozen;

    /**
     * Fit printout to number of pages? (in sheet currently being read). See SHEETPR record.
     *
     * @var boolean
     */
    private $isFitToPages;

    /**
     * Objects. One OBJ record contributes with one entry.
     *
     * @var array
     */
    private $objs;

    /**
     * Text Objects. One TXO record corresponds with one entry.
     *
     * @var array
     */
    private $textObjects;

    /**
     * Cell Annotations (BIFF8)
     *
     * @var array
     */
    private $cellNotes;

    /**
     * The combined MSODRAWINGGROUP data
     *
     * @var string
     */
    private $drawingGroupData;

    /**
     * The combined MSODRAWING data (per sheet)
     *
     * @var string
     */
    private $drawingData;

    /**
     * Keep track of XF index
     *
     * @var int
     */
    private $xfIndex;

    /**
     * Mapping of XF index (that is a cell XF) to final index in cellXf collection
     *
     * @var array
     */
    private $mapCellXfIndex;

    /**
     * Mapping of XF index (that is a style XF) to final index in cellStyleXf collection
     *
     * @var array
     */
    private $mapCellStyleXfIndex;

    /**
     * The shared formulas in a sheet. One SHAREDFMLA record contributes with one value.
     *
     * @var array
     */
    private $sharedFormulas;

    /**
     * The shared formula parts in a sheet. One FORMULA record contributes with one value if it
     * refers to a shared formula.
     *
     * @var array
     */
    private $sharedFormulaParts;

    /**
     * The type of encryption in use
     *
     * @var int
     */
    private $encryption = 0;
    
    /**
     * The position in the stream after which contents are encrypted
     *
     * @var int
     */
    private $encryptionStartPos = false;

    /**
     * The current RC4 decryption object
     *
     * @var PHPExcel_Reader_Excel5_RC4
     */
    private $rc4Key = null;

    /**
     * The position in the stream that the RC4 decryption object was left at
     *
     * @var int
     */
    private $rc4Pos = 0;

    /**
     * The current MD5 context state
     *
     * @var string
     */
    private $md5Ctxt = null;

    /**
     * Create a new PHPExcel_Reader_Excel5 instance
     */
    public function __construct()
    {
        $this->readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }

    /**
     * Can the current PHPExcel_Reader_IReader read the file?
     *
     * @param     string         $pFilename
     * @return     boolean
     * @throws PHPExcel_Reader_Exception
     */
    public function canRead($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        try {
            // Use ParseXL for the hard work.
            $ole = new PHPExcel_Shared_OLERead();

            // get excel data
            $res = $ole->read($pFilename);
            return true;
        } catch (PHPExcel_Exception $e) {
            return false;
        }
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PHPExcel object
     *
     * @param     string         $pFilename
     * @throws     PHPExcel_Reader_Exception
     */
    public function listWorksheetNames($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        $worksheetNames = array();

        // Read the OLE file
        $this->loadOLE($pFilename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        $this->pos        = 0;
        $this->sheets    = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                continue;
            }

            $worksheetNames[] = $sheet['name'];
        }

        return $worksheetNames;
    }


    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
     *
     * @param   string     $pFilename
     * @throws   PHPExcel_Reader_Exception
     */
    public function listWorksheetInfo($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        $worksheetInfo = array();

        // Read the OLE file
        $this->loadOLE($pFilename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        // initialize
        $this->pos    = 0;
        $this->sheets = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        // Parse the individual sheets
        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet
                // 0x02: Chart
                // 0x06: Visual Basic module
                continue;
            }

            $tmpInfo = array();
            $tmpInfo['worksheetName'] = $sheet['name'];
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;

            $this->pos = $sheet['offset'];

            while ($this->pos <= $this->dataSize - 4) {
                $code = self::getInt2d($this->data, $this->pos);

                switch ($code) {
                    case self::XLS_TYPE_RK:
                    case self::XLS_TYPE_LABELSST:
                    case self::XLS_TYPE_NUMBER:
                    case self::XLS_TYPE_FORMULA:
                    case self::XLS_TYPE_BOOLERR:
                    case self::XLS_TYPE_LABEL:
                        $length = self::getInt2d($this->data, $this->pos + 2);
                        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

                        // move stream pointer to next record
                        $this->pos += 4 + $length;

                        $rowIndex = self::getInt2d($recordData, 0) + 1;
                        $columnIndex = self::getInt2d($recordData, 2);

                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                        $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                        break;
                    case self::XLS_TYPE_BOF:
                        $this->readBof();
                        break;
                    case self::XLS_TYPE_EOF:
                        $this->readDefault();
                        break 2;
                    default:
                        $this->readDefault();
                        break;
                }
            }

            $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

            $worksheetInfo[] = $tmpInfo;
        }

        return $worksheetInfo;
    }


    /**
     * Loads PHPExcel from file
     *
     * @param     string         $pFilename
     * @return     PHPExcel
     * @throws     PHPExcel_Reader_Exception
     */
    public function load($pFilename)
    {
        // Read the OLE file
        $this->loadOLE($pFilename);

        // Initialisations
        $this->phpExcel = new PHPExcel;
        $this->phpExcel->removeSheetByIndex(0); // remove 1st sheet
        if (!$this->readDataOnly) {
            $this->phpExcel->removeCellStyleXfByIndex(0); // remove the default style
            $this->phpExcel->removeCellXfByIndex(0); // remove the default style
        }

        // Read the summary information stream (containing meta data)
        $this->readSummaryInformation();

        // Read the Additional document summary information stream (containing application-specific meta data)
        $this->readDocumentSummaryInformation();

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        // initialize
        $this->pos                 = 0;
        $this->codepage            = 'CP1252';
        $this->formats             = array();
        $this->objFonts            = array();
        $this->palette             = array();
        $this->sheets              = array();
        $this->externalBooks       = array();
        $this->ref                 = array();
        $this->definedname         = array();
        $this->sst                 = array();
        $this->drawingGroupData    = '';
        $this->xfIndex             = '';
        $this->mapCellXfIndex      = array();
        $this->mapCellStyleXfIndex = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_FILEPASS:
                    $this->readFilepass();
                    break;
                case self::XLS_TYPE_CODEPAGE:
                    $this->readCodepage();
                    break;
                case self::XLS_TYPE_DATEMODE:
                    $this->readDateMode();
                    break;
                case self::XLS_TYPE_FONT:
                    $this->readFont();
                    break;
                case self::XLS_TYPE_FORMAT:
                    $this->readFormat();
                    break;
                case self::XLS_TYPE_XF:
                    $this->readXf();
                    break;
                case self::XLS_TYPE_XFEXT:
                    $this->readXfExt();
                    break;
                case self::XLS_TYPE_STYLE:
                    $this->readStyle();
                    break;
                case self::XLS_TYPE_PALETTE:
                    $this->readPalette();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EXTERNALBOOK:
                    $this->readExternalBook();
                    break;
                case self::XLS_TYPE_EXTERNNAME:
                    $this->readExternName();
                    break;
                case self::XLS_TYPE_EXTERNSHEET:
                    $this->readExternSheet();
                    break;
                case self::XLS_TYPE_DEFINEDNAME:
                    $this->readDefinedName();
                    break;
                case self::XLS_TYPE_MSODRAWINGGROUP:
                    $this->readMsoDrawingGroup();
                    break;
                case self::XLS_TYPE_SST:
                    $this->readSst();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        // Resolve indexed colors for font, fill, and border colors
        // Cannot be resolved already in XF record, because PALETTE record comes afterwards
        if (!$this->readDataOnly) {
            foreach ($this->objFonts as $objFont) {
                if (isset($objFont->colorIndex)) {
                    $color = PHPExcel_Reader_Excel5_Color::map($objFont->colorIndex, $this->palette, $this->version);
                    $objFont->getColor()->setRGB($color['rgb']);
                }
            }

            foreach ($this->phpExcel->getCellXfCollection() as $objStyle) {
                // fill start and end color
                $fill = $objStyle->getFill();

                if (isset($fill->startcolorIndex)) {
                    $startColor = PHPExcel_Reader_Excel5_Color::map($fill->startcolorIndex, $this->palette, $this->version);
                    $fill->getStartColor()->setRGB($startColor['rgb']);
                }
                if (isset($fill->endcolorIndex)) {
                    $endColor = PHPExcel_Reader_Excel5_Color::map($fill->endcolorIndex, $this->palette, $this->version);
                    $fill->getEndColor()->setRGB($endColor['rgb']);
                }

                // border colors
                $top      = $objStyle->getBorders()->getTop();
                $right    = $objStyle->getBorders()->getRight();
                $bottom   = $objStyle->getBorders()->getBottom();
                $left     = $objStyle->getBorders()->getLeft();
                $diagonal = $objStyle->getBorders()->getDiagonal();

                if (isset($top->colorIndex)) {
                    $borderTopColor = PHPExcel_Reader_Excel5_Color::map($top->colorIndex, $this->palette, $this->version);
                    $top->getColor()->setRGB($borderTopColor['rgb']);
                }
                if (isset($right->colorIndex)) {
                    $borderRightColor = PHPExcel_Reader_Excel5_Color::map($right->colorIndex, $this->palette, $this->version);
                    $right->getColor()->setRGB($borderRightColor['rgb']);
                }
                if (isset($bottom->colorIndex)) {
                    $borderBottomColor = PHPExcel_Reader_Excel5_Color::map($bottom->colorIndex, $this->palette, $this->version);
                    $bottom->getColor()->setRGB($borderBottomColor['rgb']);
                }
                if (isset($left->colorIndex)) {
                    $borderLeftColor = PHPExcel_Reader_Excel5_Color::map($left->colorIndex, $this->palette, $this->version);
                    $left->getColor()->setRGB($borderLeftColor['rgb']);
                }
                if (isset($diagonal->colorIndex)) {
                    $borderDiagonalColor = PHPExcel_Reader_Excel5_Color::map($diagonal->colorIndex, $this->palette, $this->version);
                    $diagonal->getColor()->setRGB($borderDiagonalColor['rgb']);
                }
            }
        }

        // treat MSODRAWINGGROUP records, workbook-level Escher
        if (!$this->readDataOnly && $this->drawingGroupData) {
            $escherWorkbook = new PHPExcel_Shared_Escher();
            $reader = new PHPExcel_Reader_Excel5_Escher($escherWorkbook);
            $escherWorkbook = $reader->load($this->drawingGroupData);

            // debug Escher stream
            //$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
            //$debug->load($this->drawingGroupData);
        }

        // Parse the individual sheets
        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                continue;
            }

            // check if sheet should be skipped
            if (isset($this->loadSheetsOnly) && !in_array($sheet['name'], $this->loadSheetsOnly)) {
                continue;
            }

            // add sheet to PHPExcel object
            $this->phpSheet = $this->phpExcel->createSheet();
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $this->phpSheet->setTitle($sheet['name'], false);
            $this->phpSheet->setSheetState($sheet['sheetState']);

            $this->pos = $sheet['offset'];

            // Initialize isFitToPages. May change after reading SHEETPR record.
            $this->isFitToPages = false;

            // Initialize drawingData
            $this->drawingData = '';

            // Initialize objs
            $this->objs = array();

            // Initialize shared formula parts
            $this->sharedFormulaParts = array();

            // Initialize shared formulas
            $this->sharedFormulas = array();

            // Initialize text objs
            $this->textObjects = array();

            // Initialize cell annotations
            $this->cellNotes = array();
            $this->textObjRef = -1;

            while ($this->pos <= $this->dataSize - 4) {
                $code = self::getInt2d($this->data, $this->pos);

                switch ($code) {
                    case self::XLS_TYPE_BOF:
                        $this->readBof();
                        break;
                    case self::XLS_TYPE_PRINTGRIDLINES:
                        $this->readPrintGridlines();
                        break;
                    case self::XLS_TYPE_DEFAULTROWHEIGHT:
                        $this->readDefaultRowHeight();
                        break;
                    case self::XLS_TYPE_SHEETPR:
                        $this->readSheetPr();
                        break;
                    case self::XLS_TYPE_HORIZONTALPAGEBREAKS:
                        $this->readHorizontalPageBreaks();
                        break;
                    case self::XLS_TYPE_VERTICALPAGEBREAKS:
                        $this->readVerticalPageBreaks();
                        break;
                    case self::XLS_TYPE_HEADER:
                        $this->readHeader();
                        break;
                    case self::XLS_TYPE_FOOTER:
                        $this->readFooter();
                        break;
                    case self::XLS_TYPE_HCENTER:
                        $this->readHcenter();
                        break;
                    case self::XLS_TYPE_VCENTER:
                        $this->readVcenter();
                        break;
                    case self::XLS_TYPE_LEFTMARGIN:
                        $this->readLeftMargin();
                        break;
                    case self::XLS_TYPE_RIGHTMARGIN:
                        $this->readRightMargin();
                        break;
                    case self::XLS_TYPE_TOPMARGIN:
                        $this->readTopMargin();
                        break;
                    case self::XLS_TYPE_BOTTOMMARGIN:
                        $this->readBottomMargin();
                        break;
                    case self::XLS_TYPE_PAGESETUP:
                        $this->readPageSetup();
                        break;
                    case self::XLS_TYPE_PROTECT:
                        $this->readProtect();
                        break;
                    case self::XLS_TYPE_SCENPROTECT:
                        $this->readScenProtect();
                        break;
                    case self::XLS_TYPE_OBJECTPROTECT:
                        $this->readObjectProtect();
                        break;
                    case self::XLS_TYPE_PASSWORD:
                        $this->readPassword();
                        break;
                    case self::XLS_TYPE_DEFCOLWIDTH:
                        $this->readDefColWidth();
                        break;
                    case self::XLS_TYPE_COLINFO:
                        $this->readColInfo();
                        break;
                    case self::XLS_TYPE_DIMENSION:
                        $this->readDefault();
                        break;
                    case self::XLS_TYPE_ROW:
                        $this->readRow();
                        break;
                    case self::XLS_TYPE_DBCELL:
                        $this->readDefault();
                        break;
                    case self::XLS_TYPE_RK:
                        $this->readRk();
                        break;
                    case self::XLS_TYPE_LABELSST:
                        $this->readLabelSst();
                        break;
                    case self::XLS_TYPE_MULRK:
                        $this->readMulRk();
                        break;
                    case self::XLS_TYPE_NUMBER:
                        $this->readNumber();
                        break;
                    case self::XLS_TYPE_FORMULA:
                        $this->readFormula();
                        break;
                    case self::XLS_TYPE_SHAREDFMLA:
                        $this->readSharedFmla();
                        break;
                    case self::XLS_TYPE_BOOLERR:
                        $this->readBoolErr();
                        break;
                    case self::XLS_TYPE_MULBLANK:
                        $this->readMulBlank();
                        break;
                    case self::XLS_TYPE_LABEL:
                        $this->readLabel();
                        break;
                    case self::XLS_TYPE_BLANK:
                        $this->readBlank();
                        break;
                    case self::XLS_TYPE_MSODRAWING:
                        $this->readMsoDrawing();
                        break;
                    case self::XLS_TYPE_OBJ:
                        $this->readObj();
                        break;
                    case self::XLS_TYPE_WINDOW2:
                        $this->readWindow2();
                        break;
                    case self::XLS_TYPE_PAGELAYOUTVIEW:
                        $this->readPageLayoutView();
                        break;
                    case self::XLS_TYPE_SCL:
                        $this->readScl();
                        break;
                    case self::XLS_TYPE_PANE:
                        $this->readPane();
                        break;
                    case self::XLS_TYPE_SELECTION:
                        $this->readSelection();
                        break;
                    case self::XLS_TYPE_MERGEDCELLS:
                        $this->readMergedCells();
                        break;
                    case self::XLS_TYPE_HYPERLINK:
                        $this->readHyperLink();
                        break;
                    case self::XLS_TYPE_DATAVALIDATIONS:
                        $this->readDataValidations();
                        break;
                    case self::XLS_TYPE_DATAVALIDATION:
                        $this->readDataValidation();
                        break;
                    case self::XLS_TYPE_SHEETLAYOUT:
                        $this->readSheetLayout();
                        break;
                    case self::XLS_TYPE_SHEETPROTECTION:
                        $this->readSheetProtection();
                        break;
                    case self::XLS_TYPE_RANGEPROTECTION:
                        $this->readRangeProtection();
                        break;
                    case self::XLS_TYPE_NOTE:
                        $this->readNote();
                        break;
                    //case self::XLS_TYPE_IMDATA:                $this->readImData();                    break;
                    case self::XLS_TYPE_TXO:
                        $this->readTextObject();
                        break;
                    case self::XLS_TYPE_CONTINUE:
                        $this->readContinue();
                        break;
                    case self::XLS_TYPE_EOF:
                        $this->readDefault();
                        break 2;
                    default:
                        $this->readDefault();
                        break;
                }

            }

            // treat MSODRAWING records, sheet-level Escher
            if (!$this->readDataOnly && $this->drawingData) {
                $escherWorksheet = new PHPExcel_Shared_Escher();
                $reader = new PHPExcel_Reader_Excel5_Escher($escherWorksheet);
                $escherWorksheet = $reader->load($this->drawingData);

                // debug Escher stream
                //$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
                //$debug->load($this->drawingData);

                // get all spContainers in one long array, so they can be mapped to OBJ records
                $allSpContainers = $escherWorksheet->getDgContainer()->getSpgrContainer()->getAllSpContainers();
            }

            // treat OBJ records
            foreach ($this->objs as $n => $obj) {
//                echo '<hr /><b>Object</b> reference is ', $n,'<br />';
//                var_dump($obj);
//                echo '<br />';

                // the first shape container never has a corresponding OBJ record, hence $n + 1
                if (isset($allSpContainers[$n + 1]) && is_object($allSpContainers[$n + 1])) {
                    $spContainer = $allSpContainers[$n + 1];

                    // we skip all spContainers that are a part of a group shape since we cannot yet handle those
                    if ($spContainer->getNestingLevel() > 1) {
                        continue;
                    }

                    // calculate the width and height of the shape
                    list($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($spContainer->getStartCoordinates());
                    list($endColumn, $endRow) = PHPExcel_Cell::coordinateFromString($spContainer->getEndCoordinates());

                    $startOffsetX = $spContainer->getStartOffsetX();
                    $startOffsetY = $spContainer->getStartOffsetY();
                    $endOffsetX = $spContainer->getEndOffsetX();
                    $endOffsetY = $spContainer->getEndOffsetY();

                    $width = PHPExcel_Shared_Excel5::getDistanceX($this->phpSheet, $startColumn, $startOffsetX, $endColumn, $endOffsetX);
                    $height = PHPExcel_Shared_Excel5::getDistanceY($this->phpSheet, $startRow, $startOffsetY, $endRow, $endOffsetY);

                    // calculate offsetX and offsetY of the shape
                    $offsetX = $startOffsetX * PHPExcel_Shared_Excel5::sizeCol($this->phpSheet, $startColumn) / 1024;
                    $offsetY = $startOffsetY * PHPExcel_Shared_Excel5::sizeRow($this->phpSheet, $startRow) / 256;

                    switch ($obj['otObjType']) {
                        case 0x19:
                            // Note
//                            echo 'Cell Annotation Object<br />';
//                            echo 'Object ID is ', $obj['idObjID'],'<br />';
                            if (isset($this->cellNotes[$obj['idObjID']])) {
                                $cellNote = $this->cellNotes[$obj['idObjID']];

                                if (isset($this->textObjects[$obj['idObjID']])) {
                                    $textObject = $this->textObjects[$obj['idObjID']];
                                    $this->cellNotes[$obj['idObjID']]['objTextData'] = $textObject;
                                }
                            }
                            break;
                        case 0x08:
//                            echo 'Picture Object<br />';
                            // picture
                            // get index to BSE entry (1-based)
                            $BSEindex = $spContainer->getOPT(0x0104);
                            $BSECollection = $escherWorkbook->getDggContainer()->getBstoreContainer()->getBSECollection();
                            $BSE = $BSECollection[$BSEindex - 1];
                            $blipType = $BSE->getBlipType();

                            // need check because some blip types are not supported by Escher reader such as EMF
                            if ($blip = $BSE->getBlip()) {
                                $ih = imagecreatefromstring($blip->getData());
                                $drawing = new PHPExcel_Worksheet_MemoryDrawing();
                                $drawing->setImageResource($ih);

                                // width, height, offsetX, offsetY
                                $drawing->setResizeProportional(false);
                                $drawing->setWidth($width);
                                $drawing->setHeight($height);
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY($offsetY);

                                switch ($blipType) {
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG);
                                        break;
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
                                        break;
                                }

                                $drawing->setWorksheet($this->phpSheet);
                                $drawing->setCoordinates($spContainer->getStartCoordinates());
                            }
                            break;
                        default:
                            // other object type
                            break;
                    }
                }
            }

            // treat SHAREDFMLA records
            if ($this->version == self::XLS_BIFF8) {
                foreach ($this->sharedFormulaParts as $cell => $baseCell) {
                    list($column, $row) = PHPExcel_Cell::coordinateFromString($cell);
                    if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($column, $row, $this->phpSheet->getTitle())) {
                        $formula = $this->getFormulaFromStructure($this->sharedFormulas[$baseCell], $cell);
                        $this->phpSheet->getCell($cell)->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
                    }
                }
            }

            if (!empty($this->cellNotes)) {
                foreach ($this->cellNotes as $note => $noteDetails) {
                    if (!isset($noteDetails['objTextData'])) {
                        if (isset($this->textObjects[$note])) {
                            $textObject = $this->textObjects[$note];
                            $noteDetails['objTextData'] = $textObject;
                        } else {
                            $noteDetails['objTextData']['text'] = '';
                        }
                    }
//                    echo '<b>Cell annotation ', $note,'</b><br />';
//                    var_dump($noteDetails);
//                    echo '<br />';
                    $cellAddress = str_replace('$', '', $noteDetails['cellRef']);
                    $this->phpSheet->getComment($cellAddress)->setAuthor($noteDetails['author'])->setText($this->parseRichText($noteDetails['objTextData']['text']));
                }
            }
        }

        // add the named ranges (defined names)
        foreach ($this->definedname as $definedName) {
            if ($definedName['isBuiltInName']) {
                switch ($definedName['name']) {
                    case pack('C', 0x06):
                        // print area
                        //    in general, formula looks like this: Foo!$C$7:$J$66,Bar!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

                        $extractedRanges = array();
                        foreach ($ranges as $range) {
                            // $range should look like one of these
                            //        Foo!$C$7:$J$66
                            //        Bar!$A$1:$IV$2
                            $explodes = explode('!', $range);    // FIXME: what if sheetname contains exclamation mark?
                            $sheetName = trim($explodes[0], "'");
                            if (count($explodes) == 2) {
                                if (strpos($explodes[1], ':') === false) {
                                    $explodes[1] = $explodes[1] . ':' . $explodes[1];
                                }
                                $extractedRanges[] = str_replace('$', '', $explodes[1]); // C7:J66
                            }
                        }
                        if ($docSheet = $this->phpExcel->getSheetByName($sheetName)) {
                            $docSheet->getPageSetup()->setPrintArea(implode(',', $extractedRanges)); // C7:J66,A1:IV2
                        }
                        break;
                    case pack('C', 0x07):
                        // print titles (repeating rows)
                        // Assuming BIFF8, there are 3 cases
                        // 1. repeating rows
                        //        formula looks like this: Sheet!$A$1:$IV$2
                        //        rows 1-2 repeat
                        // 2. repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536
                        //        columns A-B repeat
                        // 3. both repeating rows and repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536,Sheet!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?
                        foreach ($ranges as $range) {
                            // $range should look like this one of these
                            //        Sheet!$A$1:$B$65536
                            //        Sheet!$A$1:$IV$2
                            $explodes = explode('!', $range);
                            if (count($explodes) == 2) {
                                if ($docSheet = $this->phpExcel->getSheetByName($explodes[0])) {
                                    $extractedRange = $explodes[1];
                                    $extractedRange = str_replace('$', '', $extractedRange);

                                    $coordinateStrings = explode(':', $extractedRange);
                                    if (count($coordinateStrings) == 2) {
                                        list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[0]);
                                        list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[1]);

                                        if ($firstColumn == 'A' and $lastColumn == 'IV') {
                                            // then we have repeating rows
                                            $docSheet->getPageSetup()->setRowsToRepeatAtTop(array($firstRow, $lastRow));
                                        } elseif ($firstRow == 1 and $lastRow == 65536) {
                                            // then we have repeating columns
                                            $docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($firstColumn, $lastColumn));
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            } else {
                // Extract range
                $explodes = explode('!', $definedName['formula']);

                if (count($explodes) == 2) {
                    if (($docSheet = $this->phpExcel->getSheetByName($explodes[0])) ||
                        ($docSheet = $this->phpExcel->getSheetByName(trim($explodes[0], "'")))) {
                        $extractedRange = $explodes[1];
                        $extractedRange = str_replace('$', '', $extractedRange);

                        $localOnly = ($definedName['scope'] == 0) ? false : true;

                        $scope = ($definedName['scope'] == 0) ? null : $this->phpExcel->getSheetByName($this->sheets[$definedName['scope'] - 1]['name']);

                        $this->phpExcel->addNamedRange(new PHPExcel_NamedRange((string)$definedName['name'], $docSheet, $extractedRange, $localOnly, $scope));
                    }
                } else {
                    //    Named Value
                    //    TODO Provide support for named values
                }
            }
        }
        $this->data = null;

        return $this->phpExcel;
    }
    
    /**
     * Read record data from stream, decrypting as required
     *
     * @param string $data   Data stream to read from
     * @param int    $pos    Position to start reading from
     * @param int    $length Record data length
     *
     * @return string Record data
     */
    private function readRecordData($data, $pos, $len)
    {
        $data = substr($data, $pos, $len);
        
        // File not encrypted, or record before encryption start point
        if ($this->encryption == self::MS_BIFF_CRYPTO_NONE || $pos < $this->encryptionStartPos) {
            return $data;
        }
    
        $recordData = '';
        if ($this->encryption == self::MS_BIFF_CRYPTO_RC4) {
            $oldBlock = floor($this->rc4Pos / self::REKEY_BLOCK);
            $block = floor($pos / self::REKEY_BLOCK);
            $endBlock = floor(($pos + $len) / self::REKEY_BLOCK);

            // Spin an RC4 decryptor to the right spot. If we have a decryptor sitting
            // at a point earlier in the current block, re-use it as we can save some time.
            if ($block != $oldBlock || $pos < $this->rc4Pos || !$this->rc4Key) {
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
                $step = $pos % self::REKEY_BLOCK;
            } else {
                $step = $pos - $this->rc4Pos;
            }
            $this->rc4Key->RC4(str_repeat("\0", $step));

            // Decrypt record data (re-keying at the end of every block)
            while ($block != $endBlock) {
                $step = self::REKEY_BLOCK - ($pos % self::REKEY_BLOCK);
                $recordData .= $this->rc4Key->RC4(substr($data, 0, $step));
                $data = substr($data, $step);
                $pos += $step;
                $len -= $step;
                $block++;
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
            }
            $recordData .= $this->rc4Key->RC4(substr($data, 0, $len));

            // Keep track of the position of this decryptor.
            // We'll try and re-use it later if we can to speed things up
            $this->rc4Pos = $pos + $len;
        } elseif ($this->encryption == self::MS_BIFF_CRYPTO_XOR) {
            throw new PHPExcel_Reader_Exception('XOr encryption not supported');
        }
        return $recordData;
    }

    /**
     * Use OLE reader to extract the relevant data streams from the OLE file
     *
     * @param string $pFilename
     */
    private function loadOLE($pFilename)
    {
        // OLE reader
        $ole = new PHPExcel_Shared_OLERead();
        // get excel data,
        $res = $ole->read($pFilename);
        // Get workbook data: workbook stream + sheet streams
        $this->data = $ole->getStream($ole->wrkbook);
        // Get summary information data
        $this->summaryInformation = $ole->getStream($ole->summaryInformation);
        // Get additional document summary information data
        $this->documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);
        // Get user-defined property data
//        $this->userDefinedProperties = $ole->getUserDefinedProperties();
    }


    /**
     * Read summary information
     */
    private function readSummaryInformation()
    {
        if (!isset($this->summaryInformation)) {
            return;
        }

        // offset: 0; size: 2; must be 0xFE 0xFF (UTF-16 LE byte order mark)
        // offset: 2; size: 2;
        // offset: 4; size: 2; OS version
        // offset: 6; size: 2; OS indicator
        // offset: 8; size: 16
        // offset: 24; size: 4; section count
        $secCount = self::getInt4d($this->summaryInformation, 24);

        // offset: 28; size: 16; first section's class id: e0 85 9f f2 f9 4f 68 10 ab 91 08 00 2b 27 b3 d9
        // offset: 44; size: 4
        $secOffset = self::getInt4d($this->summaryInformation, 44);

        // section header
        // offset: $secOffset; size: 4; section length
        $secLength = self::getInt4d($this->summaryInformation, $secOffset);

        // offset: $secOffset+4; size: 4; property count
        $countProperties = self::getInt4d($this->summaryInformation, $secOffset+4);

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        // offset: ($secOffset+8); size: var
        // loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
            // offset: ($secOffset+8) + (8 * $i); size: 4; property ID
            $id = self::getInt4d($this->summaryInformation, ($secOffset+8) + (8 * $i));

            // Use value of property id as appropriate
            // offset: ($secOffset+12) + (8 * $i); size: 4; offset from beginning of section (48)
            $offset = self::getInt4d($this->summaryInformation, ($secOffset+12) + (8 * $i));

            $type = self::getInt4d($this->summaryInformation, $secOffset + $offset);

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02: // 2 byte signed integer
                    $value = self::getInt2d($this->summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03: // 4 byte signed integer
                    $value = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x13: // 4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E: // null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->summaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);
                    break;
                case 0x40: // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-time
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->summaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47: // Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:    //    Title
                    $this->phpExcel->getProperties()->setTitle($value);
                    break;
                case 0x03:    //    Subject
                    $this->phpExcel->getProperties()->setSubject($value);
                    break;
                case 0x04:    //    Author (Creator)
                    $this->phpExcel->getProperties()->setCreator($value);
                    break;
                case 0x05:    //    Keywords
                    $this->phpExcel->getProperties()->setKeywords($value);
                    break;
                case 0x06:    //    Comments (Description)
                    $this->phpExcel->getProperties()->setDescription($value);
                    break;
                case 0x07:    //    Template
                    //    Not supported by PHPExcel
                    break;
                case 0x08:    //    Last Saved By (LastModifiedBy)
                    $this->phpExcel->getProperties()->setLastModifiedBy($value);
                    break;
                case 0x09:    //    Revision
                    //    Not supported by PHPExcel
                    break;
                case 0x0A:    //    Total Editing Time
                    //    Not supported by PHPExcel
                    break;
                case 0x0B:    //    Last Printed
                    //    Not supported by PHPExcel
                    break;
                case 0x0C:    //    Created Date/Time
                    $this->phpExcel->getProperties()->setCreated($value);
                    break;
                case 0x0D:    //    Modified Date/Time
                    $this->phpExcel->getProperties()->setModified($value);
                    break;
                case 0x0E:    //    Number of Pages
                    //    Not supported by PHPExcel
                    break;
                case 0x0F:    //    Number of Words
                    //    Not supported by PHPExcel
                    break;
                case 0x10:    //    Number of Characters
                    //    Not supported by PHPExcel
                    break;
                case 0x11:    //    Thumbnail
                    //    Not supported by PHPExcel
                    break;
                case 0x12:    //    Name of creating application
                    //    Not supported by PHPExcel
                    break;
                case 0x13:    //    Security
                    //    Not supported by PHPExcel
                    break;
            }
        }
    }


    /**
     * Read additional document summary information
     */
    private function readDocumentSummaryInformation()
    {
        if (!isset($this->documentSummaryInformation)) {
            return;
        }

        //    offset: 0;    size: 2;    must be 0xFE 0xFF (UTF-16 LE byte order mark)
        //    offset: 2;    size: 2;
        //    offset: 4;    size: 2;    OS version
        //    offset: 6;    size: 2;    OS indicator
        //    offset: 8;    size: 16
        //    offset: 24;    size: 4;    section count
        $secCount = self::getInt4d($this->documentSummaryInformation, 24);
//        echo '$secCount = ', $secCount,'<br />';

        // offset: 28;    size: 16;    first section's class id: 02 d5 cd d5 9c 2e 1b 10 93 97 08 00 2b 2c f9 ae
        // offset: 44;    size: 4;    first section offset
        $secOffset = self::getInt4d($this->documentSummaryInformation, 44);
//        echo '$secOffset = ', $secOffset,'<br />';

        //    section header
        //    offset: $secOffset;    size: 4;    section length
        $secLength = self::getInt4d($this->documentSummaryInformation, $secOffset);
//        echo '$secLength = ', $secLength,'<br />';

        //    offset: $secOffset+4;    size: 4;    property count
        $countProperties = self::getInt4d($this->documentSummaryInformation, $secOffset+4);
//        echo '$countProperties = ', $countProperties,'<br />';

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        //    offset: ($secOffset+8);    size: var
        //    loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
//            echo 'Property ', $i,'<br />';
            //    offset: ($secOffset+8) + (8 * $i);    size: 4;    property ID
            $id = self::getInt4d($this->documentSummaryInformation, ($secOffset+8) + (8 * $i));
//            echo 'ID is ', $id,'<br />';

            // Use value of property id as appropriate
            // offset: 60 + 8 * $i;    size: 4;    offset from beginning of section (48)
            $offset = self::getInt4d($this->documentSummaryInformation, ($secOffset+12) + (8 * $i));

            $type = self::getInt4d($this->documentSummaryInformation, $secOffset + $offset);
//            echo 'Type is ', $type,', ';

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02:    //    2 byte signed integer
                    $value = self::getInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03:    //    4 byte signed integer
                    $value = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x0B:  // Boolean
                    $value = self::getInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = ($value == 0 ? false : true);
                    break;
                case 0x13:    //    4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E:    //    null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->documentSummaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);
                    break;
                case 0x40:    //    Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-Time
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->documentSummaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47:    //    Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:    //    Category
                    $this->phpExcel->getProperties()->setCategory($value);
                    break;
                case 0x03:    //    Presentation Target
                    //    Not supported by PHPExcel
                    break;
                case 0x04:    //    Bytes
                    //    Not supported by PHPExcel
                    break;
                case 0x05:    //    Lines
                    //    Not supported by PHPExcel
                    break;
                case 0x06:    //    Paragraphs
                    //    Not supported by PHPExcel
                    break;
                case 0x07:    //    Slides
                    //    Not supported by PHPExcel
                    break;
                case 0x08:    //    Notes
                    //    Not supported by PHPExcel
                    break;
                case 0x09:    //    Hidden Slides
                    //    Not supported by PHPExcel
                    break;
                case 0x0A:    //    MM Clips
                    //    Not supported by PHPExcel
                    break;
                case 0x0B:    //    Scale Crop
                    //    Not supported by PHPExcel
                    break;
                case 0x0C:    //    Heading Pairs
                    //    Not supported by PHPExcel
                    break;
                case 0x0D:    //    Titles of Parts
                    //    Not supported by PHPExcel
                    break;
                case 0x0E:    //    Manager
                    $this->phpExcel->getProperties()->setManager($value);
                    break;
                case 0x0F:    //    Company
                    $this->phpExcel->getProperties()->setCompany($value);
                    break;
                case 0x10:    //    Links up-to-date
                    //    Not supported by PHPExcel
                    break;
            }
        }
    }


    /**
     * Reads a general type of BIFF record. Does nothing except for moving stream pointer forward to next record.
     */
    private function readDefault()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
//        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;
    }


    /**
     *    The NOTE record specifies a comment associated with a particular cell. In Excel 95 (BIFF7) and earlier versions,
     *        this record stores a note (cell note). This feature was significantly enhanced in Excel 97.
     */
    private function readNote()
    {
//        echo '<b>Read Cell Annotation</b><br />';
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        $cellAddress = $this->readBIFF8CellAddress(substr($recordData, 0, 4));
        if ($this->version == self::XLS_BIFF8) {
            $noteObjID = self::getInt2d($recordData, 6);
            $noteAuthor = self::readUnicodeStringLong(substr($recordData, 8));
            $noteAuthor = $noteAuthor['value'];
//            echo 'Note Address=', $cellAddress,'<br />';
//            echo 'Note Object ID=', $noteObjID,'<br />';
//            echo 'Note Author=', $noteAuthor,'<hr />';
//
            $this->cellNotes[$noteObjID] = array(
                'cellRef'   => $cellAddress,
                'objectID'  => $noteObjID,
                'author'    => $noteAuthor
            );
        } else {
            $extension = false;
            if ($cellAddress == '$B$65536') {
                //    If the address row is -1 and the column is 0, (which translates as $B$65536) then this is a continuation
                //        note from the previous cell annotation. We're not yet handling this, so annotations longer than the
                //        max 2048 bytes will probably throw a wobbly.
                $row = self::getInt2d($recordData, 0);
                $extension = true;
                $cellAddress = array_pop(array_keys($this->phpSheet->getComments()));
            }
//            echo 'Note Address=', $cellAddress,'<br />';

            $cellAddress = str_replace('$', '', $cellAddress);
            $noteLength = self::getInt2d($recordData, 4);
            $noteText = trim(substr($recordData, 6));
//            echo 'Note Length=', $noteLength,'<br />';
//            echo 'Note Text=', $noteText,'<br />';

            if ($extension) {
                //    Concatenate this extension with the currently set comment for the cell
                $comment = $this->phpSheet->getComment($cellAddress);
                $commentText = $comment->getText()->getPlainText();
                $comment->setText($this->parseRichText($commentText.$noteText));
            } else {
                //    Set comment for the cell
                $this->phpSheet->getComment($cellAddress)->setText($this->parseRichText($noteText));
//                                                    ->setAuthor($author)
            }
        }

    }


    /**
     *    The TEXT Object record contains the text associated with a cell annotation.
     */
    private function readTextObject()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // recordData consists of an array of subrecords looking like this:
        //    grbit: 2 bytes; Option Flags
        //    rot: 2 bytes; rotation
        //    cchText: 2 bytes; length of the text (in the first continue record)
        //    cbRuns: 2 bytes; length of the formatting (in the second continue record)
        // followed by the continuation records containing the actual text and formatting
        $grbitOpts  = self::getInt2d($recordData, 0);
        $rot        = self::getInt2d($recordData, 2);
        $cchText    = self::getInt2d($recordData, 10);
        $cbRuns     = self::getInt2d($recordData, 12);
        $text       = $this->getSplicedRecordData();

        $this->textObjects[$this->textObjRef] = array(
            'text'      => substr($text["recordData"], $text["spliceOffsets"][0]+1, $cchText),
            'format'    => substr($text["recordData"], $text["spliceOffsets"][1], $cbRuns),
            'alignment' => $grbitOpts,
            'rotation'  => $rot
        );

//        echo '<b>_readTextObject()</b><br />';
//        var_dump($this->textObjects[$this->textObjRef]);
//        echo '<br />';
    }


    /**
     * Read BOF
     */
    private function readBof()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = substr($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 2; size: 2; type of the following data
        $substreamType = self::getInt2d($recordData, 2);

        switch ($substreamType) {
            case self::XLS_WorkbookGlobals:
                $version = self::getInt2d($recordData, 0);
                if (($version != self::XLS_BIFF8) && ($version != self::XLS_BIFF7)) {
                    throw new PHPExcel_Reader_Exception('Cannot read this Excel file. Version is too old.');
                }
                $this->version = $version;
                break;
            case self::XLS_Worksheet:
                // do not use this version information for anything
                // it is unreliable (OpenOffice doc, 5.8), use only version information from the global stream
                break;
            default:
                // substream, e.g. chart
                // just skip the entire substream
                do {
                    $code = self::getInt2d($this->data, $this->pos);
                    $this->readDefault();
                } while ($code != self::XLS_TYPE_EOF && $this->pos < $this->dataSize);
                break;
        }
    }


    /**
     * FILEPASS
     *
     * This record is part of the File Protection Block. It
     * contains information about the read/write password of the
     * file. All record contents following this record will be
     * encrypted.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     *
     * The decryption functions and objects used from here on in
     * are based on the source of Spreadsheet-ParseExcel:
     * http://search.cpan.org/~jmcnamara/Spreadsheet-ParseExcel/
     */
    private function readFilepass()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);

        if ($length != 54) {
            throw new PHPExcel_Reader_Exception('Unexpected file pass record length');
        }
        
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);
        
        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->verifyPassword('VelvetSweatshop', substr($recordData, 6, 16), substr($recordData, 22, 16), substr($recordData, 38, 16), $this->md5Ctxt)) {
            throw new PHPExcel_Reader_Exception('Decryption password incorrect');
        }
        
        $this->encryption = self::MS_BIFF_CRYPTO_RC4;

        // Decryption required from the record after next onwards
        $this->encryptionStartPos = $this->pos + self::getInt2d($this->data, $this->pos + 2);
    }

    /**
     * Make an RC4 decryptor for the given block
     *
     * @var int    $block      Block for which to create decrypto
     * @var string $valContext MD5 context state
     *
     * @return PHPExcel_Reader_Excel5_RC4
     */
    private function makeKey($block, $valContext)
    {
        $pwarray = str_repeat("\0", 64);

        for ($i = 0; $i < 5; $i++) {
            $pwarray[$i] = $valContext[$i];
        }
        
        $pwarray[5] = chr($block & 0xff);
        $pwarray[6] = chr(($block >> 8) & 0xff);
        $pwarray[7] = chr(($block >> 16) & 0xff);
        $pwarray[8] = chr(($block >> 24) & 0xff);

        $pwarray[9] = "\x80";
        $pwarray[56] = "\x48";

        $md5 = new PHPExcel_Reader_Excel5_MD5();
        $md5->add($pwarray);

        $s = $md5->getContext();
        return new PHPExcel_Reader_Excel5_RC4($s);
    }

    /**
     * Verify RC4 file password
     *
     * @var string $password        Password to check
     * @var string $docid           Document id
     * @var string $salt_data       Salt data
     * @var string $hashedsalt_data Hashed salt data
     * @var string &$valContext     Set to the MD5 context of the value
     *
     * @return bool Success
     */
    private function verifyPassword($password, $docid, $salt_data, $hashedsalt_data, &$valContext)
    {
        $pwarray = str_repeat("\0", 64);

        for ($i = 0; $i < strlen($password); $i++) {
            $o = ord(substr($password, $i, 1));
            $pwarray[2 * $i] = chr($o & 0xff);
            $pwarray[2 * $i + 1] = chr(($o >> 8) & 0xff);
        }
        $pwarray[2 * $i] = chr(0x80);
        $pwarray[56] = chr(($i << 4) & 0xff);

        $md5 = new PHPExcel_Reader_Excel5_MD5();
        $md5->add($pwarray);

        $mdContext1 = $md5->getContext();

        $offset = 0;
        $keyoffset = 0;
        $tocopy = 5;

        $md5->reset();

        while ($offset != 16) {
            if ((64 - $offset) < 5) {
                $tocopy = 64 - $offset;
            }
            for ($i = 0; $i <= $tocopy; $i++) {
                $pwarray[$offset + $i] = $mdContext1[$keyoffset + $i];
            }
            $offset += $tocopy;

            if ($offset == 64) {
                $md5->add($pwarray);
                $keyoffset = $tocopy;
                $tocopy = 5 - $tocopy;
                $offset = 0;
                continue;
            }

            $keyoffset = 0;
            $tocopy = 5;
            for ($i = 0; $i < 16; $i++) {
                $pwarray[$offset + $i] = $docid[$i];
            }
            $offset += 16;
        }

        $pwarray[16] = "\x80";
        for ($i = 0; $i < 47; $i++) {
            $pwarray[17 + $i] = "\0";
        }
        $pwarray[56] = "\x80";
        $pwarray[57] = "\x0a";

 ��6,�E+n�KCw�q�K�-hc}��2��1#�	Zf��G�|���C�<#M5:��o�N�3����)M�`1
��]�zb$��r)m��� Y+xVOnȔ3%��#��7d"\Q�id��L�0�0e��(9��GQ�s ��es/������zR_�J�'P����SO�����CNM݋	(,���A���N�_�W���wl�� 'Ȯ&:"0#{ y��| D�l�6;�8"���N���.�� t��9#�ry�wgy����'�;c�`ya������&à,������{���h=�@1栩�$d�R���n���X�tI)��-iT�"+htEG��d��6�/(���${g>Sk��PM��JG�Y�/�/��v�'RO�a<�ʨ&x�T&�O���_J��+�a3���{�=�{��D�/� b!`��P�ɡXx��Êf^��-CƢE#��g�#q� �k��Օ �Ǉ���,.���&�,üwi�d�la��g[s�缾qz��!�������y��U��]Ag��V��b�a� ��I KH!I�׋��'z�n��
����)f��T�$�)���Tx_�"{��Kr(�
���9O�c�.�"�v
^�0T��f���.��	%�u��-�C�0td6��k%��|�}�m���$�;vj�d�L*�`N%�gB������P��Z��
GK������@�W��X�~���3/w?�h | ���(z"q�LKY�;�EemƁ�0�%_�?U3
wVH�Kfk���/c��
��{Elm (�H�f,���{�f��;�8եN�24�>�Y�������-3Sң\>�x����&�|B�_g�U�Pe Ɣ&r���Bv=s��~)����pu���)�dj	�mbnƼ�QJ�"�	C���Շ4��"xW�w�;]K���d��gi�f�n��o�� r@�g=�� �ߓ(Pxl	O���g��%,��\��Ϗ��r�eJ�.�~�MGrmO=-�t�5�d$ �m�if>"_t�e;��Hn.��'n'�I%:xP��QK��N����<�+��ɋ_�j���g�����r�cu�aH�UZs"���̙�*���xv��Ĭ
>�(-����t��8�ˈ}TPj�'-�	��+}j�e��7��P@1���[��8~�-�_�*�'��1åFP*ὡ����fl*	���T���Dǿ���ο�����ԟ�f\�Ƨ��� X5�$8^d���c�8��OqY�d�[����q��`=�
&98=�\�h�ʭ[�];�4����c8"����Z��Q�d,V�gBC�������P�zsk����#'�RJD�="�]+ �6�&�p�Iv	d�+��4,���f�jf��4��#-�o�4:�K;�$�1�G�!vu��l��s�^�>lr�e�H[��
\�i���Q��b�d&��d8{�B��b�PxU�N@1�,���R�*��![@8J��.� ���) '"9#�?��W��_�@�J�-���Y��T
`Rawl���
�����.)	��7��$�]��@���tJ�@��,t)?.�]�ƴ��Qft�a.`J[R���4����DtY�\3k/�!
}��	�<�3s�.w���˚��ルbӟ<���QMup���"�60Th�f3��P
,�bX�l��&D=�Y�/�b}��`�)�Q.`|�p��H���h����Ϊp�r cJ9X�ljM�>y_e��$_�h��w|�m���:qW�ũ�y�f@\@��u���C�ٿq�+�;M��%jN��Qղ<@3Vw��{�r@9��ٞ�W���I(8��'����i��R�u&Jd�GTt8jHR6�.7s��'@���*0$H������%+
�o��
Z�#��D�
Leg%��i�PR^�� ��rҺ)�o�&P�bQ{7�YDMv01��`�H���Ԕ+w��b�b��O��f>�OG�l'+v��dh&�{r��;im�#B��]~��3{���y��D�E�}�a�O
+��C
��n��~nN�yZ�~G�꺩�+;�]p.�'�#�m� L�T���g<��%9�\��J+d
���1�,f�CE�>c��(*7���Fe�fp�[rh��=�#��d-�lg�E����@d��^��4�x
[��m��ٴ���;D����-��W��{
)������,%
�?�� ����ȷ�}�c7G�Q�	��A1��f�`�h�ʓ��IyO��Pm��wW���ug�,/����>��d�e��St7xLv`�96�g(�A0��~_����	��m7Q{�%3��oyϼ��?�Q�l��Ħ����أ�d���ghs�H�Yi������'ژ����)1����+�q޴��H"�2�O�si���ce*�a&y<�c�'8-s	�> ,�+��3;[r��X�̊��d�4��u�P&6����u:"���r��b�e�{s����.�n-D���nkl���V7NoB�0�y_\��;�Q�й+Hcl���4ul`�;l[�2�5	d�) I�z��F/ЭS#>m�:�(؝
�f�3���*�k%@dowI�!B$�Z"��$.!�L߳�5%j��i�vnN?�agR���!����29½���ۺ��L��a��hu��Eޛr]��������X�"z��n�4������(����9�z��4��aQ�n?�I�Y�}b���<�{`G��uɾ�`ϧ�<:��#���V�-�䜅5�=�
��E,�8e�{"f~�\�ol��>��4<\Z{2�s�d���X2�4�O�i@&�
�^;�8� 5�M�Q���wHz5kE�w�ݣe�r��=P��Fa�+�jx����A�w��Κ�pɼ��͖"�]� ���/���[�p8�NI#�l�B�ȩ��N�P�U�v\Jpso�Z�hiћ��|��9���+�k��M6;}���
)?�l�W��a,���P�t2�X�� `�urI#�*jX59Ř�i�]�c���bH�ג�3Ȉb�Y@+)�e����pS��(������5a�a�2��`��aۮx��+h���Ǩ�~:�O7��m�z�����f�����X3֡�e�1�fu�K�P	�rt2x�-8�R�NБeQ�N�#M�"�����Z22NF�d{�v��[K���2sk�0����Y|#�Ex/<s�!���q-8�)���ô�Q|�1��p�3���ҴD!44]�c(2QE�<w�:Ƌd�B_,rbB O�Lܟ��])����5�������Oz�vDS|	)�x��!�L�cp�Ao���r*8v��tQ�&�6*�X70��efJr���?T O�d�c�Ix���/�"����DF� �Z��g�K�/�t�+�rn~���s��=�:�D�I�*,�ɽ�<1���fA�o� �
�Ns�kɒ�}$U�,/Є�M6���@���'uV�{
�-�	�46�l�f�T���R��A��GP	5��v��?O��1m������pyim��NF��ƾl���'�Ɂ��倠X��|ov�@�a�ə��=��QG�4��zP�3����wL�jrʲ	�)�)&;��<���
B^G�:x|������r콝qA�r�{�vN���k�/@�%6�}Ī�7�{��o^�Ǩ����q��ke�:6 ��-p�$��4��y9�in������:_�r�NH0ϙ��K��� �'��$���aʯ	��2�sd�����q��[7#Y�3�Nl�{��W�_�[�e�b�W���pL�:��"o]9���gwC�!�c�A-�a�iM~P��Vu��e�`)o�N
�F���~��$��>1��}�<0#GѲd�k8��SC��Q�ED�H�Kw���ȟzv��*P��3�p*�R�2�7�`�FL�.�}�=~2��o/�Ɗ�'�<"�����~�ݝc=�+�PP�x=���M���b:T�a/�<;\$n�oe�[֝�T.vί�J��P'En�=�@�5��.�R`�k�|�^���
�J�@��A����;�vM�w�l��%k����>L�ŭ�3��������3�ʸ )���1�F����e�q����BE q���0	
h�	\O�
҈m���l ����5"EL��J�`��q�Ҩ��ѻ���������s49Pk�(���0�
C5<l�� ��p�[g��"8d^ K�fJ�F ]Υ���V��_8�k�$�O���T���TR~'l��)^;�H%���Z-p���M�h>��DMq�K��o�f��.�r�S���о�Q��-���E8�!]t�f8� �qLr���c5�t{���Uqbǹb�pg���d�ƫ��0V�H(z:�D$\V ��2��1V55���b���Ϊ�!����1��gI�	dD1�,���2E�Ts�)s`�օ��x}Ҁ�4հ^zi�n�mV<��4����ԯ}
?=է��6��B=���E�}���dl{,����ߘa��ʍ�$��Q9����z�`�Ȣ('Y�ڙ�}�u�{�O-/#w�=o;^�o�-��f}r�)��N���S�,��:�8���Ÿ�4���`Zq�(
���{�W���
��o�0PҮ�7Y�	N���C\���x�c�X��L�23%<��c��*fai�б�|�}�\?bLi*�Q�M�(�3���
�u�(ڀ�OHuD�Yw�$Ӕ�:juN��y=H� �7n�w�y��ӵD���Z�� (��.�{gK| '�>��*'�}�BG���tl|6m'�+��5�a����o:m2�i$a�j��RbG��x�٦�hrX?��Qe�C!�mc_�C��đ��B�~��r Po��k�xE�>��k���̣7���}c?(Ȝ{Ki�?���6}Y��(��9�e5�6��n�`��*��[Eh�d+nn.����5-d�3�	����W�IpK��B-��$x��%���O>��X�rB P�W�3UB~"N�Hqe����L� ����j���h�$[�z,����p��9|��hrF�&$31����a<�*z�+ ǚAX! (^	�����6�խ�]���P�����Vt��� zW�
�y�mrct�2ؐ-�D��3Ϥ�KoOGB*����;�b�S���?x~�� �8��4 e'��i�GP�nT��<�;��l�lP`�?n�����'^�x�3�'`C��q}�%��� �!�X��,�o�UO����h�����T����ǋ�Pm�	ט�@��<
��W�H���j�4�̈́���&{'
����q���>劝��L7`AE�`�q�`���Yca2�=F�ōu�x�g̴A]��R�T 	��|K�T4G|eY����C�Ѿ�2�-�����+ٟ����DR�>�Ĕ�^'����f6�s���q��bT�v
�'�0�8j���jL�x$eF����%UM�
LA�%�4���"������83�f�rWJ-��i�>6h*6��đV�	�VW@�5�+bi�DH4S��0l�pc ��ơ/}CT��t��K,��&l����1��B�4Y�Xd�}:�_�
�]8�4��E(¦v�k��L��i?�q�~§A�l���>���qWH��Q���:�I��7������Z��t� T-�C ctͽ߾4�z��I}����d��cKpb?
!T0K8q{z�⠶�UDb�C. !��{�����u�:#���뭒 a�I�[x�%�wMt���B�Oʆ)��G�>v�j/�kD�D�e�):�j0����=-�K��l�?�R�uL͓h�8�m|L���n'��2M=���$]={�W"�U���9�$��f�E�/<�B�z����t-���A|U6,���g%,~��
�Nf
) ��N.i�UE�,�s7=�+6�yA�����@��Y�qQL8`$ıL�2�o�%u�x?~���&L4��W�Z��zl��0u�qp�8�cA��OO�i��
aT�co�%�V*�#:�(�a�©v�i_d��WKF���l�׎���b")��f�`���w�T3�o�H�'�8Dsu1��;��3y�V\5J��"5�]<�2��sAr��*���~E$��g�[�p�Q�GNLH�	ܙ��v�+��^ִG4��t�I�ߌj�+`������B#$��Ϋ^�U��l��`�P�:"(hĻ��$�+"��L�r����
�=�lv,:	�~��wV���S���b Q+���x2��4��{�=����;/�=W����-B�$/�<D˽IGv�.�����]a�i�t,Qz������% �1����Qڐ�	�_Š�­~O�@��%>�_�}ۗ���aipXj36j����n�h�!�L��4-��0&:�3J�0VqluiP�|���v2qd89�0�:��	z���t�S�ȇ���h�&y~��
\v�s��Zj���MAYVk=#q��]ߟe�z��2 ��:׺x�Ջ�����P���E���#N�I�.5(��[m*_Pz��=�h��|�p!��t��fIK��3,���@.N���_"����Mj��$ҩ�v�:g�.V��f��#:�!�{��07���	�B�8�)�:�C�$W��ͼi+���E�
�*�#ԋzJ��k[��j�� ^G�]�)��Z��n�<ty�5(��>�2
SV;gbf�Υ0ʰ������i��m��'ź�O&X���Y�a�D�d2ސ��߯(��s,�"� 
*�'��8�aqP[L�j"#�%7�c�+�=��l~˺{�ʥι�VI�p��-��2�&:ۅy
L��'eÔN_#s��[���,�t�ų��V5����`�ۥ���O��:��K�R�6�.��KP��t��jmc�����k��"Aq�kE�_�f"	�o�gq�m���|8���h� [Gqž��W��
W1j�O=�:�ڰ�z���&����$#���S��F�Q��C/wo��Byꇽ��Rct�d6�l��hg)�=Ad�5�Ki8I�v�۠��e�3=g���y);�Jȱ�f�a:w��T�uZB�Ou�
���zf<�}r���+��+��i�l3�% �� �[t�F�2�3�"�id��
����)d��U�$H�)���Ty^�%0{��KR(�-�`!Ԋp��(s	}Gʸ�)$(qWlΰu�r�0��L:���V�W�v���=e�ֱmex�
A���E���v6�e���H�b��r� �u�6�c(�_3��,� ���aʧ�����K�ZQr�b�x�N+�d��@�so��R�=O��ŧRfV�eR)L:_S�%��E*�nsq��1I_ONy��r���9�$&��v��Q3ф�+����6´�DF}>lHBn%v�I�Rw}�*�29���C@$F	�ՐD���Pt�*<a�Q��ڎ_�����M��
;$����YU�i	��o���.�\�w#ZT=���eL�7l��9�{��)n7�QIjt�-G�8�J&!W~n�;<0n-��zyҜn���HjO!Tۿ��).�sP<�}�ݠ��7�^��p81Sc�h�Cm�0�;��Hs�;�)�oHZrx5���"*�":�Lg�����'"<�X�v�.����x-�A[=b���(�hGoG^.$�� �F.�Vr�9m����>�����n�
�:��Dvm�A�O��?p,�����A|D&"!��ۃ�(��d9���
����@5l�)��׷ �1:d%\���5�xG��ѡ>I
12o�{�+f�49'�9��a1h�p.I ��w�%�%"U�yP ���o��!g{�%l�zu5dV큡>T8�Y��E�� ��v��D!� ��)	
~�,�}bF~� ��g3�xk�TJ�<l2�9��j?9���/v02���u04p�n��_�� ��7�4m��	�u�<#B@�9��D<��AaN�U%����qKF"�J�%}!�wf�Aw�Ď�=ףa��u��3g9��9,׶��d�$o��LˮO���/n-�G=\0��)�b3{�m{{�.�+M���5.�rٽ`3|�і�o5�دe:Ynh�eTF���8�ɾ�����qj�H17G��y 	a<�����u���-\m XM�7rM��as$��3x>a�y��6wqq����)#=
֬�u)՚1� �Faa^1�{!�.�䇶!��""qMM�6#�f$�.+H#�ʦ���^kx֬�B�ۯbd�$w�����L����cf�?���	�71�Kw�ﱶ]4Q�������7�K�zƶ����_���1O���M�l�K��~��*q�a$��3�=&QB����P���Ӟ*�譢S*��N��ͳ��n^$c�b��톀��,]�J���u�Q�MŦ?]x��7����
H��{E,, ���ej�u�C�?�5�;�8ԤO�
M�/�
������-3S��\>�x�����&�N�7M'��U� Ɛ&r0��
>=3��:)E#Q0q�����yV4D1�J/ׄ�+a��S���4�~�&xG�w;}K�����ay�f�j2�wh��rB�C
� �%��$��Al��@�nL������bw*@�x�`����� ��Q�$)�Y�PjE��n����#g|�6�Ԩ�/6g�z\�a8���Ouf+�ܫ������2n��62����c"��f{k�d�~�j\k,�h�O�LC(�Z��� 6.3� ny�w�P�F�A���(#9b���U���9��%�~�<�r� ��/*ZU6�s6��F��+�s$V�����e�s)�2����{t$b��qq`�����	�v+�$Vt���wde��;H���\!�"��	/nOcX�wӠ��z� ��q"q�?/����~g�r�c~�U$̀:)v�����vi�r(�I� �����n�V�$~-�(�h�l<G�U-f7r} ظ��v)��-z��[*����2��G����!�3��&5F䩅:Bߘ��/�fJd�HP�>G�DC��׮�hB�G[hxa�\"�>�%!%<H�*��?g���m͍y#�" 'S����V�F�FU�(�k�TYf�/O�-�F�&cb����������7�u�uF8�,5�"tM'�Z"�"�; 1��O�`�'�%�͗�c<Q!�+?���>7�Q�¬�iJ��͉`$�05*�m�^��9)��<˷oSF�g3�p8��)�I4IU��ԉ�h��L�t]\�[�;���s�~DZ�A�=";
��z���$$�k�WgBK�q����잮~�txp⵩��-SD;"�Uk)�4�'Op�Iv!P�]��Jy@���<�R�:\��C��-J�FlF]�);K8���G�g(;��\�o_����,YA�G�te:��-]�v}� \e��~��H�4��?ڧ��#��$݈�W.S�� �/�Mx`�}}	��z$0���m�G{&�dąn��m$5f����0�reb4�&Y��`R@s�M`x
F�F�=�gGd�ޮ):`{�R�� <�2�f�.��} 5�դ6���u��'˱Z�@Qg��PH��a3�Fk�ބZ�:i��%�:7[�xs5�r>�>���o}��;%
w0���;aC}Vq�	pG(��-�j���mo
F���g$j�.^~�=c?�l���;�r4 ,���bT}��d��i�	
��~,�R8�>ʹ�T���/���5&s[c`Y�X��7}�L�Un,eK%���������JE{@GE9�J8լ$�����jI�8Y���m��:>[lE$%�[������bf� Wi��L�h�.F�`|dQv&��Fi�]�δ�Qft�`.hj[R���tI���DTy��1+8&�!*}�H�	�<�3s�.w%����ユbӟ<��MUp���"�6]h�d1������	�Z�l��'E�XVX�b}��`�)�Q.c?�P�#��eB���3�ުp�kJ1X�dh�29O}����^�~�}��r��'����oP�|�X��e��C���q�+�9L�%Jn��[Բ<@3Vw��{�~wA9�����U���Y.8��'T�ീi��S�g~x[m�F� p#�0�2U*Ar=�����&+�E�k����r��#
�o���n$�'��f��b!$�4�L�qT�q�p�~zM�����A�s��sX^@k�1|4��0(�*��4��̸�Ja�58r�P����S�8l1�A��@;A�����G�z`��O�u�~{]3�sm������ c�-� א�8K����'��/���eO�'�,��gю}�g�l:A�b���l��sd6�6�U�#a,T|br�;��l((.aGe��~S���oH�}Z��iD��yh-W($2�C>ގ��ɬ0H����K�u�[�}�^xc7�K��r���5Y�_�<}�!�D�XDqN5�d�iM�D�����3
i<�}>B^��EH��~�{G�6.�FZ�7�uaIn%KgUP:3xy��O��;d f�pa�M:hE��j�a$y(j2�k�@V~�|�1"����g�*�:LP�"gnU^hmj;DA�~N��`�tS�8#��
)��C\!mLB��W~e2\%(n��)A����k�\4!���%t���m~Q�Ґ	������T���R@ɡS��A��P�l+A]'JO��5T�n���n�~ҳJ��n��nnf.�{^�zG�����+#��HjԠ��8�`l��h//oJM�!"}l)XSK��J�1���I̅�S�!L�T����^ҁ0�_��D1�'	���n������j��_�:���3
�w�����tna��	���$�p(��iB�8 u=h>U�~
!�m����$$��MA�DA\=X�i�tף7�W2d��R��6M��X������mi�61�f��hI0)�Q�$0<%/H!���ȳ!�Lo׈p=vky�j<�J#BG�.�2�j������g��H-)x����J�W,���n��no�-n�5+��y!��eE��zAty�p_{[�~`����F6�:S�;�A������x��c�࢖*���Ң7D���s35�.)�>��mv����O9 @��G)��HTr�RጇtQY�q�!L�pi�z�O�D�m�n�.WŹ}���B�����fw�X<y#���d6�pYA@Z��2�Xu԰�r�1wS;�r��X�+Đ��%g��� VB4KiS��L�QR����I*�@�
~e�ź�F�]�SW`g�S?`)��T�f�� *K� �����;���1�,f�CGI>c�ͨ*/���F�hf�[rh��=b#��d-�jg�fE���5dd��^���iX�,� ���M-�(�zy~gO5��f���^x�C41�Z �l?��iMQ�$|NRg���(3:03$�%�b`h���Pd��,y��u�A��XtD�D����?k��Rj�e]kt�AS��O������*�R��~K.4J2��35��h�ۯ(�4.5��u�
�c1�bd0a�̔�(��1~����FŲ��׈ovU4nY�1�	,2��ˌ��_Js�$#ť��?�'��=~����o���BW{�f�e�!M��׸(N䝦N�%��_#ky���|�=�<5�лU�*T��$
[�S�c���4���;<{����G�oK `u_���h��`N?\f��o�G��跍o�agRE��u��SA1�� )
�c=	����-89��&m���=��-g�9-/����=��l�a��SwzLvb�yV�f;(�A0��v_斧1��M'qmdw-�OJ�
���?M��dmNƒ��
� �39Kb��X�̋��d�8���w�P&l����u:"���r��c�E�X{3����,�n9/D���lin��T7N+"�0�9�\��;��K��(�,���4ul`�{��2�4	d�!$�j�D/ЭSa>u�:�m؜�`3��*k+5`$ow	�%R,�[">�wg!��߳�%ej���6jPF7�Agҝ��#����28½���ۺ��L��!��He��e��s]�������X�"zh�.�������(����9�z���<4��aQ�.?�I��}b���|�y`F��uɾ�qDϧ�<:�ǣx��V�-�朅5��=�
��U<�0e�{&f~�T
�ol����4=\Z{2��d¥_\4�=�O�i@&�
�~z�O3��m �z������f�����X3���e�5�fu�K�r	�ft2x�-8�R���EQ��#�"�����[22Nf�d{�f��[I��d6s6{�<����]|#�gx/<2F%���p-8�)�����Qx7�1���p��?��҆T3$4\�c(2QU�<w�Ƌd�B,r`B"O��ܟ��])����5�������Oz�cTST!�p���
?T|T�l�c�A����?�*�|��d.� �Z��&�R�'�(��r�֞�R�
�Rȡ��]0p'�p�g�=A^���fvo��
sNs�k��m/_4,Ќ�\6�N��QQN���W�u��{J�-��3x�h�>�U����RY�P��l\�W1�S�$�-]�3F�>�NɰC)��JF���l��3�#GɁ8ߏ�m����8�3�)ܚ���C��)�e9���z@�3���P�L�j2LʺJ�)�<&"��<���
e�14_&��0`��Ux�~����ª4���p���	,)���xb�hh5�5M��i=�!}�Nd��$��i2�]1ė�:�<G�yotA"�Ty8Q��z�LEϩ�'qM��[��e���(�d�B��Y���u�������n8�J�>3�W�`r���z!��*�����#vJ8�st�#�ds�甼C
���"�>�v1�ɗ�9��	=B尽�ݞ��B� ���g��-�(!Q�l�<��Fq6��+ĸ�%�M:���pWa��j�m#�0.:l4��~�d�``q9��1"����.b`���LhAp��wT2s���Jn��b�v{��B-��G�k�&��O��a��#�*J�k��)hm�G]�W�K�{�4�E�?(ͩi2eg!{�Z�J���a<��;͙�F7֠%�vR�*�Ũ�#��������3�� +���e�F���`�y�������E(u���	
B54l��`�"�p�[g�� 8dv C�fK�.F �����v��_8�#���O��@T��dVP~'l(�(^9�%���Z-`�4�
?=է~@6��R=�����m���fny,,���q�ǘi��ʍ!d)�Q;:	��6z�h�¢(y�ڙ�|�e�{�_-'#w�=m2V�g-��for�)��N���C�,��#��1�I�Ÿ�����aZq�(��՘v�@8ʌ��IiK��,�1�*�(O�{n�A0E�/9! �gpfn��箔ZzY�}x�Tl�Ӂ"�~3"�
���z�W�����l$6ç�Z�g?H��
�u�%ǖ��z<(vm?b.��E(a������b���F�f���W�{�y4w������W/$rs��A!�mw�A؉Đ��`�_��vDPf���*�T��E�eʥ��I�����?(p�{�	k�=��f7eY����>X�E���`L��ߗ���i�m �mT��]���  �Q�L�M�*A+Y�q �8|A�-&�"Y�;sZ�����}V��l�_z0~�6g�?9yJL{"~eE����k�s�}*�@���P�\��+�\�
c�I>��Ħȁ�	L�$B?�' 
�ʦ���D�Pi�?V4�)t0-28�Ɯ&�	I!����a���?<�����dq9���0Af��k;�{V�m� ۘ�4���S��=1En7'/d�cĨ�v��8��zm-;��>���`MYb
iЬ�4?�4��T�O��n!+f�$��L�����JM��]�B@n��K�V��6�K�9p��lq�H���bq����[�S�lЙt'��Ƚj��L�p/�)㶮m!ӪmH~8&Pmv���\Wa볫1���֠����$�&���`�;
r�2{�5{7|aDPT�ۯsc�`xv�>x��#iY��5��)���(�"�Ud�%1gc�ldO� ;C`(NY���_&8�b)��h�g"&M��֞,�?�`�Lbŧ�m�IxCvv|����ι��4"()`��p����Qm7��̠�T b�.�����y-��uF::��[%Q�M��"��K����ld)0��
%�5I���
����q�ׂ>�����L?$@D�`�u�b���Yca2�<�Ōu�h�g̰YU���T¨)��xK�T}GtdS����G�HC��r�}�����+ٞ7]��7�dP3?���^/���!f�r�Oq�&�bT�w
�er0�8j��EnL;x eF�憤�GU
�-g0�$���)@�V�#�T�KiJ�f($=�p�L�iaL� X/��5���X��Y��4��ʵ��aZ��t�n,KC 4bt�m�#�1
�j��y=����d��cK0b=g��=e%|�%��Tw~������tNe s�9����YE|)�w)^	©�g��6�/�m�D��pr �`p; (2{�$���L�{w���e|��]��+^��2�F�=��-��F�
�(�hq,2	�v��'F��#S���" +ؽ�x*��4����c0�����ǹ!���i��K�VtK��W?�,�����Ua�a�p-Qr������!�1���� ��	�^�����O�@��%<!�
Zb{5k����(
Q5mIn��U�R�.��:�:`qD��@�l��mv"ad(;�0�]$��@5F��{��
����f�u/(�
\wƛA��Jj��M�aIVi?E�d.מo� ��2��M�:v٭��a3�k��XP��d�\�;�>V�e<�.5)��Ym*_Pz��-�h6��|�p1Щpp�
(<��_�
>V��f��3~�1v�{��r'��_��B�80-�z�c�,W���%ɼh*U��C�O*q�ebRE���r�!�p�� �EM��73L�y.y��N��bt�H6���y}��6 c\������1�����0��y�\S�&�˾*#\�@ԘB�4/1���:1�c�S��ͫI�!S �{���RD�Fq���"dAZ�5!b�Y�r�p5[XbP�6lX�a�a�a�#[t&���<r�J�*�#܋{�8-k[��j��]C�]�+�U���n8<`y�t(�6�2
*�/'��=�a1P[M�*"3�%�c��=��l6˺z
vK��w#s���2�;R�t�
eeJ���g�t���f� Te��l�li�V�>�L�\�X�(t.83df/�((.ckU5fDx0�E(l�K�	-��M��k(*:y�Q.�ત�]�
@.A�xv�Ԥ{Z�:<d@�v$�%B��:�!��@�(Jtiq�kS���-&0CER�Z�%1�F�)�"Q�c�E�PV1��z�b
��)1w�S%��UF�hl>��tfvWs]�H9~��<a��@�m�����"Nʷ9��(�>��b��i�|��-W��(lKõ�� �dEj�>�B4��)/E
�vV�<��0	9�D[6w"�Q�� �U�8r���!W�~v��=:�$FjH�E�y�B!�Ƈ�`Z�5{nx��[D��:`��,)���K�������*`���w��t���*T؁�J���=�k'��5�T)��6�)�����8�xI��
Q�E�ҙ��g4��NڌL`*K��o�&n�gc@��*N�;W������H�x5���
DRJ#�4ƪ��U�W�y��y�2�8 @��^Y(�T|,�8�(&����H�jN7e���P��OzQ&v`+C�
����)f��T�& �)���Px_i"{��Kp�
�Cv���
�%�빁19�y	�ak�ㆳ�$�H�s++̄��Y��I�C>� 0�fGOv`ɥnh݄�n�8�J&!W~N�;� =nP-��ty~���H^EA-Uۻ��)..	<�y�}����M%�V��q:I)c�(� ��Xz��N�oK ��QX<�"����"�r,MwV����'<IX�#%v�?Ά���m���7�t���!V�YW[o^x�� vD.��@�1m�
\,=�@(��Y**��m=e�c5&j�"�6"^�0t��"�
wvh{Kfk���c��͆��ә@D�e%i��J!Aè�+��M��Js|0`m�,C*z�t�AF��X	q,S�Lu��2GI]8֏�&-�I
��sE,l 8��fhקpB.�U= 6
4�*�ZQD���ń-3Sң\>�x=�£�]�N·_%��A�� Ɣ&r�X��vu3��f9M+��)\Y�,�g�K+��Ԯ�OL��}Y}�P�]1Eԧ4��#xU�w;]C���{��dy�b�wr�s@�w=�+p��1(XplO��b�
��P,A5�GcM2mZJ�bH�`��vG�i7+J8�h����M��M�c��dk޻�S�e�]�����b^�8�\�W�s�B�<FL.h�, ��}^.��1���c���$ %��$��DLu�x�^l������cw*D�y�`�_�<�� ��Q�%)�Y�tzD�n����#elE�H��
>N�*�k�T�j�/Jw��F�$gb�����Ȱ�s�����7�u�Y"F`軐�X�7d�n��$��R��AAP`�=n�E!Xs�5~/B�Cy!��+?��R�7�A�ɴ�aN��͉`$/�?+���n�WI!��<¿fQD��2e+�Y)�$ӱY$HTĦ������]��T�������chAYܠ�;B���z��Q�d,� �_gBK�y�������rUoj�+��U/pB�/b�]k8��v�LP�Iv	T�U��N}b��<�R�:T��S��-Z�ajE]�);	*t�Vre�_� �(8N@�S52~kHC�G�te2o�-�v}� \���V��H!�4��7҇�|�݃��}�DG.�� �?�h �u(�0{$����)�Ok&�dĥnl��i�$_�f��U7�1��"mb4�&]��`r@�0M`8
�B��gA ��.1):ar���� <�7�FEn�u e�լ���u��'���DQg}��QX��a3�Fc�ބ[�:k��!s:7Z�xw0�r.���
�o���;%|�w�
w '��+!C}Vp�	pC(@�-Uj���eo�F���g n�/^R�}g?���;z4 ~��erTm���5,�	
m�ȉ		<�3s�nw-��˚��ユbҿ<��PmUpE���"�60\h�d37�5���
��y�lb�'D�`��c}��l�)�Q.c<�p��<h
�o���B$�&��3��b ??m@�(}�	'�(��
�
Jo0�G��ߑ�� .����e�����O�*�
y%<L�tD|���f�g&�k��f�	2�4�]�)�rZ���� ��� w1�n�~�a�+r�8p!w#f�s�X>/�kb�@�w�7@ei�C@g� ��^�['�|�<u
Y195 	�<a* /UWj�H� n�
2C�(H(7"D(6�HB�1�g	KJ�F�3l=,�0�~v���:��G&G�dp�zqO7umO^mB�S!��h�˽%�ZK��
À��FW���v�
+��B]aoL�ŷW$0N$(j�#�A���o�\t!��-�,��->�QGW:H�C��/�i �����Ȁq�	@����l3X�*O��5\�n����{��}�#J��n�{nf H�y~�zG���z�c#���
�`�N�Ezyu��� ^%�t4��(����!���I̕�_�)L�T���T��8']��@0�W}
��d��ja_�(�����8��Ю,��vB�cp
1w�;�rǜY�+Đ�%g��� FB�-s��� QR����Yj�DC~e赅��Ƕ]�SW�g�Q?�-|�V�f��
Ju �����
���1�,f�c��>c���j5���F��f��[rh��9�#�d,�jf�e����`d��\����x�=� ����%�,�8y~o5��F���^x�C4S?�Zt�S(�i�Y�4�.vg���(3:?0 �m�bhh���Qd�
�Vʢ>f`4a����h��1|��hҴFǡ���יogE8n9�1$�(F 2��]����GJSjT�ǭN���Y
(Fq:������6"M�Oװ�M�װ)�f��N�R%��]�cX���l� �"��P�TO�*\��%[��i�p˵m��"6��6��&�@`�=�$�vj.�5�a�F\b�e�尩
,H�跍}�a7�q�)�S!A1���r�J$g����^{N��&}����u��u��,-����>��:�e��S<H:1@�z�6"*�5D��[pe���*��/T~_�5G�L1`J�1��}�օp�2����%���������G<5>=|<�nI���������5���L��;!,���Q�ŇO���k�MRҜ�M�
�L�%5'�(:(&Eb3f"3��#�(;tL��Gv�ẃ�P�.��Q�!Y(0	5���"#�U�����X<���Nug�Ѱ�ںVwk��Q�lCi���c~f3� �"�l,�
��k��0h�T�XG ��{W� si�Qg�Z$�ReC��cgA
B����MNw��Z���r��� �ꩾ��"�d����PU�kg�,�{�	E�"t�(k�i"��!<��L
��}!E.)J��1f-?M�
����#���!Gw���lk?�q�a�>Lt�`[� �	,��������\�9	>�H�z�����W��r�kc�+1�$�e��c�t�"(��2y����_|�:�n��aA`/�q����S,zaBS
xLx+&����}P%/�M��	�Q����@c7�F�tS�*��yC@�
�9�i���uP]~���dd�Pd�^(C��ݦ�)��xmi��jg����]�mM�+en�4��ǕJs���B��_ʈ%s�t��,��%��I����ע�/F�@N6�lV�)�W�F�p������vX7��d�1�ft�K�R�wt2x�m=4Q��Eq�N53L�"�����Z22nf�do�v���[0Y���6S{�4?���yx"�Ez/<c�%���s-8�)������Q
|�1��p��?�K��D!44I�c("Q�,����d�B_<r`@"O�����])5���5�������Oz�bT[\!�x���-!�L�*z�= ��$�6
���Q�&�v+�Z_30��efJr���/<Tx4��c�k��k�738���.� �Z��/�S�7�)rMHǷ�K�,E��f]44a 5%i#A��f�f@�k\�.�Ns�kɒ��/:=@P�QU6�I"]O�}�'�%n�J�-�	�4jx8�~�L�]���W��A�#��~sC������/$r�(>cdq	��{�Fd���|��)#���x������H l|�R�,m�ɘ׸=�	G�>y����P���RJM�f-
ʲB�-�&NP\��Q纠0��D�
ZFwj�$D4=UA<��~"�8ant'>�	&?MxO@߈��R�
=�!}�Od��Ӕ �F��p�X8���9��totE3�q@P�d�)�MUө�'a
��&O6�D�k3�P��\l_1A"��>A��#gr{��,2�W;[uFa�q9{��x"���.CD���lhI|���]�}�k�(��,�U�v��J�(�G�2k-��Bh�a*��c�:H=�T)Wh]�-U�Z�J׽�^/Z}��,zq� 7y�U�>$��׬& qT�~�]ր%k�𠘮L�݌�#���� �ҔӤ�$L����.���`�{���;0���,(���0e
�J@���
§���_q*�kG�`'A2%e��H��:\V���X잷���#1.���dE��G�ED)w�%�!��0�	?)����!��8��=��2��mw<��44�����}
?=է�v�6��R=�c���}���dn{,����Ϙi��J��d��P;:	��Z�x���(Y'ڹ�}�e�{�_-'�W�=o(V�o�-��dr�9��^���S�-��2��9bP�U������`Zq�(��չr�@9ʌ��YiK��&�1�(�*I�{n
���{�W�����l&rز�rj� ����C]���A������	H�23!=��c�*<s�б� P�u���[ Li#�S�]�`�{��S��|CXsK7�z�#��xG ����Ƀ1, a�?0k��|h� �7.��y��ӵt����Q� (F�.�{' �,%&�~ՓJ
U�M�B�p�zw|`m?�2��
d�L��1ʒG C�*{)NsmԜ�B �&7c�
�h"��^�n��dwR�"ev��#d��Ḟ/-%�"�l� 9N��ju�7��	�.�R���z��Z}����h��F��-������,�PG�m�ŔB��C|OI!r@��I!|������w$��6���[�U���#�9SB�K"��xSn?L2
���?�$�4�01� �n!wF�
� ��` i�>|yy2�ia���""WzmN`D
oZ�&�А.:k3D��.�Zo���h�-ߍ��8��X�R��B�_2K���~�'o$-��2.+ Hk�\���F]J1�ljgE�� o{e�B��2��l�J�c�"e�9ܔ90JjB�~�6iAE�`����&X'�X6+`�
�`�q�ǂ>�����L3`@E�`1�`x��Pca2�=F��u�x�k̴Y]��P�¨
�ep0�8j���jH;p eF�f�4%Q M�
L5A�%�=�q"1�����83�f�rGJ-��i�>>h*6�i��^��TU@Z=�+b`ADH6S��<�^��	2ƞ�ơ.}CP��M��J���"l��e�1��
=kT�d��gFq�1��Bj�ܦI�-!�$���k)㟡���bJ��ftG�Qqd�i�[��= �U�f娝��$���w�v�~�T�$"�����4�M���~*� "I8��T����kk��h�)P��%%ԂD���z���Ь{?:�!��ũmus)���r�Ȫ"�����M/�NFN��3�m��$46A^�����c0dIp#ZtQ� (g������]J������Ic�*�{U�M�=$�r�-/��Z��"]f�G�w'�k��|�#Sܯ�)�n
'R0�T�HÈ*��\�9%��A���O��\sr�nNV#yUΦ]8$ov����YH��~u�2�^�8Sq�K�$O�M�A�KPa3L�!�k�a�]�e��ԡ�	=t4b-
�B�p,0�>��'��x���b`+���x*��4���1L;Jw�e8R�Jh�p�ҍD����ԁ+"��$�����Ua�a�t-Us���.��! �1����(�+�(�_�����~?�@��%<!�ۏ���r"j3=h��8e�#n��BQ,���% `$P��17{��-A�|��mv"qt89�8
\w���rAZj���L�AYa=Eq#Gd'��g�:��28$2��eaoy���	�tզ����t��,���5��
.ʶ6�mFPz��=�H��|�VpqǠtp�f([���_�
 �)���:f�
?T��g��2.�!r�;��07��_`�B�82)�:�C�dW���5��h+Y��`�N�1�e�RA��(�`X� `�/7�?2QL��#3l�Y�y��N�UB|	��6�8�yu��&h

��'�9�`1@[M�*""�% 0c�+�=��l~�:{��J��=�VIP0���-���&8��Y*�!�!e�O_*3��[���5�d�Ų��V%����`���%��蟋)�:��K Rt7�*��KP��TX��jb�����#�e*Aq�IN�&_�f�@Wo�gy$i��|���`�".{�⹰U �0�~���J�#>���d)�<Ux�$��Ru���,�{�`��Q�vK�wc#a2���;�_t�	g]a���Vw�1�}- �`E3X��l[D�b��qR�$z��QD�Lb.윒wHa~ܠZD]#��.�;�Rw/���*j��w��#\�`$xV���Eg/����u��d� T$�%!�K��"j�2T����i���'�Ja6m&9�Gm�).ccU7o$y�1�E(L8_�	-��$	�zg�%Ӝ�kT��Ȩ�\H)�@��r��sr<L@�~$�%@�uM2:� ��B��K�kp�{O��z�'%'wM&�,e"��_M�nv��-� S��NP<��#1��:�tp��uCp�rfZ(7�c%��8�h>��6t#
]�4h��4fq� �m
�%���"N���������b��i�|Ě)GT��(lK����4�dE[�I�B6 �)X@
�vT�dZx�F��h�J�P�� �T�yx��� W�V^��?z�,GzM�E�mQ�Bc����`�Y4kjq���D`�l(����˹����*a���u��p����*T܁�Jꮄ�9�i'��3�T����!�����8�pI��	�l��'�~�� � �;JQ�E����� g4���ެCO[`:�K��&f�n�wc@��*��9U��,����Vx5����	EO'3���� �Z'�4F���UQ�����+�  ��VY �U�-i8��(&��bX�h�j7e���P��OZP&v�+C�
���z.<�uj�P����5F��]%b3���r\x�?��A�,�oid��M��1�1w��.1��BU�� ��$s� �%��ާzRO�j�NQ���2�
���iC[��yV�}{��D�{A��H��ᘆ��^\�pb8?�FEߍ��iD�l��v ;�x0���M�����UR9�H��`|)�C|�0i�o=��3c�f))�����v�E������c�ꏲHu�P]
�
�!bwR��v�h�4�h��E�n�s#dM܏AҴ^�j�g^4.Cv�Eչ{4i� �p�x0��1�ǣ��,�#�$�,ּ��\�	b�<�$[s ��>�uz��5��}����}��]��"P/g׼��cD�a���I KH!i�w���'x�f͛[����)f��D�$)�
��ݢ�\)}�7���S�H�j7�u�L���3�]8L��-<h�m�rq�P�0��
�Q����Z��$z��M�(�"�%����aQ�y��o�놢�L@�n#)i���f��&H����]����b�m�t�-���X�B&1W~N�;�0=nP-��or�n���Nի
��d��{:Q8~���y+;7��	��rx<pO}�bFtGdI�9��(X��A
~�X@�~�a�I`!���T��lV͈_�K!B�4):`Ͷ��/sV�����s�O� ,�$�Ea��5,�m**o�����$`CpG�j��	�AxG*�J]��@�ЫY+k��a$#5&(�"�> ^�0tc�f���.��	g�u��-�C�
uVh�Kdk<�c������@DJd i��K8!Qê�+��Mo��]s^0`i�,QC*~�p�AF4��BZIq,S�L5��2Fi](���&-�I
H��sE,m ���dj��O7�%>#A�Nq�8ԥO�
6�?p[�5{���-3Qң\>�0�����`�MC�]'�Uq�� ��&r0E��v}{��~!G8x� �f
`f�o}5�I{8�b(�%�!�V�"�#��{�&xWw:]K�����nx,�f�n��w`�zc�S=��p�_�(Ppl	G(�a�e������x ���L
���P$���`soZB�9�f�`�m1�w~A�:J��!
 ��ˉ�L�3��(d�k޻�S�e6_�����c]�8�H��W�~q�J�<F�*h� ��|]�WԱ���o��
�$�!��&�jAL��@�_L������bv*`�i�4@�Z���� ��C�%)d� Q�PjD�Xn����#g~����6g�zT�e}��igf+�ܯV���)��2n��2����b"��v#ii�q56:�c\c
aj��͉`$�ʉ '���~�$Wyth��<ön]FۋFg+�i<��)�Y4HU(��p�(q!�=M˘a��+ɺj�5�)sΗfy�r�C���Z��U�l,�
9~WgbK�s�ԉ�t��n�t!x0�]��#/@�; �Y+1�4�p�)v	D�}��f}`���|�2�Z\r�c��)^�ih"�);	(��Vrg�06>�)L��<g�uR�E�4e:of-!\�v}�\��y�E�X	�$��7҇��&#ݣ�,���W.R�� �/�	h �5�h ��z$4���m�Gk&�f�o��m�$1f����4�Zpm`4�&Y��`^`��
�o}�e;5�l�v-�
u ���;aG}V��	pG)�L-Wj���Ew,Bs��g j�.VS�C?�h䙆�r4 >���Rl��d�%$�馲6��A��ᒮ�Ɵ艧���}��s��+�:)��%�5^Mx�B�C�d "�����e����a��c��gv�9/��W�!?k:�!#��g��8�)R��M���&���FԤ���Pk�u�e��!���9N��~,�Sx�i?��C�!T�� X�/���5V&s�c`Y�X���}�L��Tn,%K%��QI�ͷ��NE{@GM9�Z0��4��,���j��8���y۵:~{lAdu��[LY�u�|�b|� �t�h�~F�`d�Pp'�ګfi�]�ƴ�Qf4�`.ijkR���tA���DtY��1k.�!}�ȉ	4�3s�.w������ユbӾ.<���QIupe���"�6\h�d3�����*S��&_a�Z'E�X/X��
v��`Ɩ�)�Q.c<�t�� 7&�e"�����h�r K9$"lj��QK`��&<O3�=����O�gQ�TL-l���+���~>B�Y�Q�+�9͝�!jF��P��=A1Fu�\; ��s0�����g���1*8��'�à�3��V�w} �0e�F��߹s�@�����.N1tVw�I@v�3P�Y�+���m���N �'��&�!�b '��r0�qh�{�Q�~.L���i���N�cXhI�1�4�� (�*��*z�����s-rmlP�`a�T:��"��;��N�l$
W^�%%h�k�l_��!%�+I�J+1�A������.���������f�2�c켉vf��#X��3^��d�7+8��R���ǭU�orz�%.4O`�$�
<�RM6%T[%^q�2��Ѵ5ѿ�KW6�.B�ARn3XoW/.<�&�����4d�b��V�9��q�oPd�^��h��w�X�fg�P�j� PF'5�F!c�`�_R�b�O�t]$I&�9bi+FG��2j��p>�TG�o�om;�XA:#Rdny_��3�29*Cm�!j�w\ot��R=i4��8�@^
;���P]S+Wl�ZlL��<.����I����%y��C��?�y-K~:�a�9$�Ze�+EQJ^\"� $�+pt|D�a��m�"g(���r�6QD̴��Ia���*}��Rr��W��b�iռ��4	Z}Ⱥ2�02h���v�AIk�Gck$_�&p�wА��"<G�:̬H�l
I�Zy��� .`]�~�PgI+\J-�����<$`��$k]�A(Z<He>Իw�t7b���2���CϪ�r�?������n=�!N��D]�� g�ѱH!>�# u��IrmJ�KHo3̯^�P���fF� 6e�fN�ii�������5����X���e��Р&&�!��iMD�s��x$56wL�u��}b�c&���Zdy�A�@0|�*$�}�b�t�Y�-<�{����s�8�L2�(�B�&
N@���t'�Ak/izZ��t`{��ܾ]nbK��:06o�e���� v�?���-gup�-j��|e9�G��e>g�j�*HUƀ!����k�`bb�!I�s6�[DAz�B�s�10�.4��d��;�&*���w�B�B��M˺�jm���'h��_@�"R(��rxgo xHJ��3f��?P���=��ׂ5h�J�I���� �_�ZT^{Ac�t��q��0U$x�?~�<O�
�:�Q�H ��+�J\T.3g�
*0B��Z`(誂-�I��;w�c�O�3�WfI�bd0e�̤�hW�1~��h�O=Ǣ��O��fD(n1�Q�	( 6��]ħ�OVSO�phu�9Xf��Gm��-I�im�N�i�n%�~�! ��߰�un�^�a��[�jR�	��l� �)����tO�\i�]
[��a�����h��3?޶s��|Ȥg㔻1���_W1*����=6)�Gn5�#S�ɷ�}�Fa7
�F_,<F'g�~:B22i_<s����Ֆr%���أ�da��e`s�P�Q��������J��$1���g��C�܉��HC���#�sѯ�b�*�a&y��s	�#p/k��Dh?$�3�/Hc$@��6�񡞦� ����-Ql# @gᓼ">�ZIT���D���j�eY�4����.��<;y���lOCC��fn}7�3�%�-,��(�+��)w�j��D\��i�D�xU)Qa�H$��r?�%gD����'�4XdZ)���J�HmN Y|.}�w;�j1�+H �;+��;	Wǰ�i�,d^5nT�tG����Ҳ՜"	ш�������,���Ԯ28��e�p��,Ϫ�m��g�Wٱ.�"���>�������I�k�ʪ%©(\`�
P�]�k&����|h�R��{�� <���1#m�|'��W�x!�d*��8ih
�')?m���2�R}�D�'	�}���řВ��-3~�����x�I8�dm���@���"V��e�ZJ
"��m�T�G�]2�x_\�SȪ?$QD����M y�������������\G�p�����1� ;:�j��q!]�Ωyk'H��}w1VXoԕCQSRu/�c����W�hh��IiE����J5���K!4l��`0Ơ�	|�p��ɒɪq�I|!^h�'�4ھ�	����Lj�hw�f!QB�c�
`��$�!�q��0~�`��A<7<�|˔
H��ؑ"8 :Q��"uj�x	�Ffq�֌�N}3�C�US��CR�ĤZ"p�:��]qg�x'XL�^�a��w}Ѡ`C#�X��q�HWyM�Xh��ĤdX�,+ ���6Pd���U�il	�\����fs���9���Ȕ+ L�g2� ��U��U��K�hX����j%�4OH�~]1_z7�L�*�����&F��Ú�v-vr�Đ
�T�$�~a�|*�CPQ�m_�<���&6b�6c�K9�R���1X&'1�#���{Q\`���M&V�P�C��cS�*]�R��_,2����ؑ�~|.r�1ū��j�O�H���vq�%G\%wi6o��:��(�cqN@AMk����|�}%ܗ��F�Ѫ�q§O�"���l!�x���/d!�L�(<�("��&h(5D���	R$�?V*��^32��efBx���?Qx4���c�J����?�*��x�dOF� �Z��f��/�)���)",3zrh����<+Y�5	'i�/.�nAvol�
�Fw�k���}/TugP��]&�@�l�|�'u%��kj�m�
B���:��sd�jYBR��q��Y
7�٠3�Nl�y��w��^�SFuU�F�U���pNa2�n"o}=/�VguC��c�A1/�!�a
�Ȟz%r��*P��=1�lq.�R�5�5�a�FD�>nK�=�;~;��o/�̊�/�4 ���n���c=�+�`P�<;�ĭ�
,�#m;Od��җ$�G�'�s�W=�ý:�i�:rt@6�p8h�4�r�FXW��'q	��_��a�κVSWg�@��[⼟��t���ܑ���n8�j�>#�V]��$�H[Q��S�x��O��G�C��Ģ�#*d2᧐�C
��&�"�>��| �i��91��U9�~챽����n�Pĳ�g��.�(yQ�j�<6�� �E��*B�5�K�����3�T]�&�|�x+ �Ozh�y�4 �q9{��q#ғ�>Ki���LhA<�ؓ}\�{���n$��B�6v�w��FJ���G��k-'��F�`)!*�#�.J�k��-Gh}�'Q�W�k�{� �E�?,ݥi2Eg!�7�B���(u��uԉ0~g��)\,�u��.l�ļ�3���.����3�ʘ )���1�G���`�y������Be�u���0)
�(}���$( â@�5"L��Z^$(�Y��)��ѻ������:���sd)R{(����
C%<l��`��Z;p�ZgM� 0$� CgfC1o+V ]����t��_8�#���O4��\��dFR~'d��*^:�h%���Z-p�t�]�h>�|LDEq�K��o�g�>�p�R�F���>�Q��-�ܴ6E8�!]t�f8� �!X��(s9�t{���Uynϱb�pg��d�«��4O�h z>�	de\v ��8�� V1���b���Ϊ�!�ֶ�1��cA�tD!�,���0U�Ts�)sa�ԅ��xy���4���_jg�n�mw<��44������y
?=U��~@& �R=��v��}���f.{,.���q�Ϙi���ͭd)�Q;:��:�h/���(Y
'��}�u|{�O-7�W�5o;~�o�-Ȥf}r�;��N���C�,.�"�W�9� ��E���4Z��`Zq�(	��Րv�`8ʈ��
�p�3P�x'���>���zX��oM�($rm��M0�a:�(1Y�+$,Ֆ�02	 ���s5u���I���[>��uZI��oU�r�k�kt%g
o��xCtw|���α��B0)`>�p��t��Pu5��Ġ�\ B�].������,k�uF*+��[%���"�pC����lf)0r��
>6�i1vP�Y.����O\2o��+���w#�.�r�ck����oڱS��/{Gh�Tq'r*)�r�w�� w�\�R�8ZZt�h<7|~����%��5���N�x��!G�CHO� D��
,Z�"���.8k3D��0&�{o���h�-ߍ���<��\�R��C�_"[���v�'$-��".+H{�\�)�VYN1�njfU�� k{e�P��2��p�J�c�"e�9ܔ;0J�B�~�6iAM`���F=6H7��6+a�
����q�ǂ.ş���3l@U�`�q�bx��Y#a2�=�Ōu�x�gʹIU�FR�T¨��|K�t�CdtI���S�X���p�=�熄���kٞ7���LR�?���N'����b�v�+/q�drb\+�?�g�0��o���jL�x eF�f��%UM�
LA�%�=��c"����h83�g�rVB/��i�>>h*6��D�\��TWD�5�/bi�
�-8�<�� �.V����IqJ�c�1��9yʎf7jr1;�8}Z����`��:���y��7��¬���Z��t�e,�B4ct�ͽ3R8?�k��Y}����<��#K|c2
14V0^I8p{{�⠶�UDfPO+!��{���|�u�{#����뭓 a�I�[x%�Mt���b�oʆ)��G�>&�j/�kD�D�e�!:�j0�����=<�k��l�?�R�uL̇I�0�ml]L���o'��"O-���$l=1�G"�U���+�X$��F�U��8�F��LZ��yu5
A- �$x���+�̌$
g��t�]���8�2<�,rX�֪*���$c��P�P�:Z�1(ddյ�{�ҍ�dP�g�7x�$$����zKI׾6�����H�K��ct�bۄ���������B��칖6T��N��gyy<Hr_'gfx"�5`�b..�+�y5o�����"�+���2J�"J�%8��,|�skmt,A�%$�\�t99LJ���\��#�E�2os=Z3Ye".|+/�o�$��5[>���I���k3�*5ي��
[�mH�Q2�Ec���<	"6K�
��
�Nf	�$�N.i�E
b����03�o�H�#�8D3w1��;��3q�V]5K��`u�}|�r��sRڀ(���E&��ʒ�Y�8�A�ENOH�	ޙ��v�+%�^ִF?4��u!H�_�h��+ ������B"$��\��|'�Ȁ�fu!�P�>%+�Ǿ�j$jF��Li�r������o,;+�~��cV�Ö"3���b +���0*�����Cc�u�dLdF��f�2S�f" �P禺�<y�K��~�[�Ua�i�t-Qv������  �4����(����O���p�~O�@��%<��=
"�%\85�at@[M""1�!W s�+�=��l~�:{�JŎ��VIPp��m<��&:��y
ġ�gGÐO�#W?��[����,�t�����V5�y��`��%�/���Oi�z��ˤR�6�*��kT��dX��� }b����c�a"Ey�_/��f"	@V-�d}�my���~����Kx ==�纕g��')���
H�".���V�)�:8�$��Re+?�;�K����WEvK��s3s����3�_T�-g]
l�k�	-��)q�j+
�i��U4���ހ�H&��\r��C�+�l@D&�%@�uIv:��mB�(1ukp�k�hCL�5:/"QL3��]�54�
����#� 1��:�d
@R^'�4B�*�E�W����Y:�< @��^y �T�,i8��(&����H�j7e���p��OZP&v�+�
���'8d�t�kG�r���vyir*<�h6d��L�p�4t��*;��BU�� -�er�	ϳ��߯zR_�b�+*����PO����COE��8�Ι>5z��;�2s�]�
f���x�=���884R���e�ml�v;�xr���M�
�,��J"~����AYɄy�w$m�l�B�;c�ayi
O}an�B,mt^W��&�O"0��_
C�o+W`1�a�y�<�i��D�/�  !h^ؔ@ٱ\ko��se^�(CƙF'Ѹ[ 0@J�u�0�S(�3������,.GޙQ&�<¼wi�t�k �$�e[{�ž�pZ��!���f���y��]��\@8c��T��a�a� ��I"KL#I����'z�n���)��-F��T�$(�)���T{[�	"{��K0�
��k|9��.9o �Z�,�>3~�|�{���M��'�%��/q�"TR�=��=%��_P�K;���4m.ZZ"�P�%y-Xep
��"/zv�a6e^@:�)[�~`4,9�İJC�p3Nh����0�2�0�M8.{SH�qn�����`q��hf
�Jh�6o�x@
fÛ�����gse�)��IJ�Z8�����n���*v�l�r�`��!�Y�?�U��q?r%�{�bu|�ЂIjf6��#���y�=���a.�{� �L_�k��L��l�5U���X
!�i�?C��"��p���0^$cT�cyBg���]�J��5���MŦ?x��7����
��sEmp���ff��HoP B��$�5ԥ�4�.�B��	���+3Sң\>�x�ƣ&�v�L�7m��U�(� ��&b3��v}s��~)G-x�#I0���]�u������#
;p<�M��cSu+�z�&xWq�:\C��n��eb�f��2�r
�q<j�R=��@�ߓTPpdo��a�d�#���xE�Z:?��2)"�r)��m!{�
����8wC:[MI��#�6�e;��HN.�{'N(�PNx'��Qd�N��l�,�;����?�E�ݱg�����c��v�aP�uZOQ��_с������$&���ی��z� &�V��m�w��D1|9���"b5��LJ2�f�j��bb�:�����^l1*��x�d����Ege?��槥�D�0T5>_���.������w�Υ�����0���Ռ[t�4h���m"�x�c �L2䢂"c���o,=��L��
�F����4b�>�r�ll1����
�[s
,?�A�-e�f���7��l�*!W�;T``p��@"���	B�"�0�ѹ}g�h�S.��+�|iWK���b��0���PC��8��+пN��B:mCga� �xZcohE8���!�aw�?b!���;=��3DL3Grd�7d�k���@
[!.�؈� L�����=Y����M�%/����"�f�� F���|L����K��% �xp�us�x ޭ�̀bk�+GC�yi$�U\�fy5)5>H�@+�zw1� �-4�8*�#��A�r%�U+mJ:N� 	i�T���/vV�ׂ&cb�s��ܘ���� ��7Uu�]U&�;���rdC��9K��N��	w�lԜ��6B}8��a$��-?��R�?�6q�ɿ� `n��͉`&��)�z���n'�9 Q��<÷gQJو"=+�q �$�1"4H]�5�jV�Cn��:�_E��@)��`��}&ZCbRoDG��x���d v;�gFK�s���R��,�1z(9bꢘ�-DRDw �Y+;�R�FSPuiv)PbU��Ny@3��<"i�T��b9�){��,G��=4�|vg.�f�i ��A aB��.,yC�G�de:�f)�!�w}�I���7�w�,	�$��M����'b���]�f+KB��d!�:hV�:يl�0z$���b)�cq&�&����-$#B��-z�Vx��d�	vf�<5��"Rad�vL�� a@m>a�d6Q%BB�v� g�I��x�^f�@���&��ug��'��k*�n)/�$�~/��pI&��u�s�#�J/w	
b�D^R/�\	J
؁'�AJ��/�M�F3�3}N	��mD4F�=]`f��F4��jT* �o�F8D�Qn�Gc+�E��[/R6��V�|!-������%G݊�;��k0��ɦ����������, +�:��'�.��juW�z*���/2φ ��������A��5Wּ[�2<&�pu�
d���IB���"�6\h�d3��r\�t�O?�#շ*/��'H�Z`��b��`�)�Q.c<�P��@��E&���}{̪x�r c*9X"l`�9a���<��?�+clg��0��j�����a�]�d���\��%ٿu�*�9��!BN��P��7JsFw��; �s
xL �0��r�'����Y��+�c%,p��n�
5D��2<D��ld#���Z�`_1�C ���1D=k$nk���;m_�e$�\ ���ΝH^�LS�q��6�"PrU���oR�]e㠡ޤֆt��RZ��> t>�����,�`7�7���
=�A}n�pzd�~`pz�.�"����B�qGwRv��%m~�Q�OGҔzI_���G8s��(�̼�A��B��@��P��"7OG܄eT�n��;�~6��L��
X�i�6�=�MS�ī$k�N ��+
ϫ�}�"�B�amU��:,/�ax	cJa	he�����Y�.?#W�`eGys�=qx��. >t���x澻��kL��q
�Q�$β�q7�d
c>���"�0��>�����V��!�o`F�ᮤN6�|���m��:�V��^K*$b���uk���$�;X�j�!u"q�c�M��Wc�ff`0a�̴�(�1~��(���䢓��w�?fE0~9�1ŉ,B6��]O�jK^���x$#Y�w�6iP�"�m�G�g����y`{C���Ƹ!^5�
�2i�$���%�����)?��zb&��+�qN��� &���K�{c���ce*�a"9<�"�gpmt}@,4�*��3�iv��Yqϊ��e�H�+�w�P���{>�U�u*"��p�c��$�;sK�m��/��ly/T���lknP�E7FoC�0���<�;��ڹ)a,��jtul`�zlY�r�5	DidI�jXS�D/�Sa?e�;ŬؽJ�d�s
�!@,�"��m&`��߳�%%j���lD62ag����#����69½8��۲��L��!���hm��u��r]��O��#��Z�"zxS.���!6��j,����9�Z��4��1aP�.;�I�}`���<�y`F��uȟ�p��<>�ǣ���V�,�服5�=�
l�Ul(:e�s&o~]�\
�/m��>��4<|z{2,�d���^0��K�{@&�-Yy���;�{.W����z����t("2�|r9n�@���淬���|�_m�	7�N���slA�k��]��B�>R6��=2���];�_�`J'$�iU���\t�i�]H�g���p��cj>D*�`� g��`��u+H��mn!*�6f���+?y.Rw��� �Pn�7j,�}��6h�F�2��(��!`h��/���#��cyEk
ký�d�(b2�C�uV��.S�'N�:/54���ӽ|��E�p�X`��}7"/�>Ma;#�Mq�pЕ�Y�n=5���V�_��pmP�f���/� Y�Q@b��z�f4�d���ky���M�E�u�/�h��kas ɩr�Lz��?��uA�g5��Y�Q�ah�y\R'hj�m
pFC����8t0�c��k��c��6^w@׫�]�sEJ��*i�l�W��e,���P�t:�X��@`�urIK,zbXuy�ܻ)�U�c� ���EbL�ϒ�;��b�Y +!�e����pS��(������5a�`�2��$��cۮ|��)h�����vz�O3��m �z������f�����xX3֡�$�1�f4�;K�p	�4p2x�-8�P�M��eQ�N�7M�3���Z22og�dk�d���[K���6s4k�<���{"�ex/<0�!���q-8�)���㴢�Y<�!-�0���+�ҶD344I�c(2QU�,��:��d(bO(b"@ M������M)��0�5�������Or�vTSTA)�h���$5!�Le:>�7"��7,j5�4X���	A�&�n*�X?20� afJr���?|4�d+�I����?�"����D.� �Z��g��/�9%��y��#�@�;j��t�|ww�~Xt.`���LDfa�o� �
�q�k����/T5, P�Q]6�@�\p�|�'u��{uK�m�
­��"��s`�Xb�5q��,+6

����z��$��>���=�,0#FQ�d�k0��WC=�cQ�`@/�Kr���ɟzv��.6P���=r�rn�R�3�;�`/�DL�.+�=(�>>2A�o/�F��'�5 ����~Iݝc9�)`P�x=������j:01A/� 83L nyoesQ�ت�*dį�J��P'Dl�=�@�5��,�R �<i�x����
�%�N<	v@" a)PEt�*Jϩ�'q	��
��e�΢ر$d�A(�y⾟	�u�װޑ��:n(�J�D~3�SR�^R��)��i�'�o#M/Z�]�r�]d^h�m�X2dre㖼C
��P"�>��t"��5�9��E8F�������: �s�c��/�(zs�:�<��!!�� �* �� &�G��#qxq�o��a�WG��>�*)��a!`a9z��0"���>Ra���LxA}�В7�Z��=��'�XM�?��%J� ko��,'��C��i"���/�/��ZY]g�+;�b�"��h�Hg��XJ4F.�~�j���|��Sx����-��^�뀥�~���R����6:̹��w�;#���	�f�H��@�L��*`ؚ�q�/���1/�v~!Nt0d�<;Kn�4��D�,���,Y�}���
mxPUl�Ӂ'�~3��
���{�_����l�v�n�s����N]���A�*��Y�vL�22!=��c�W.=i�K��$~�5�Y�K`L	"�Q�M�`�3�ᬗӌ�Ǯd|�P�B1�Sf����Г�}�j/89V˜� �7n�s�y�qӵD���W�Z�LhF�n�{ p.�z�:Փ�
g�=�z!ǖ��x4|umezj���a)������ݼC\���egG��8rh�>(ioIC�%��mcW�C؈���`���T@P.`f�5�ZE�/�d�i�>��ɢz����gi(7�{NJk�=��>?eY��ŏ6�x�E����j̾�����b��l>�u�\��E�zp��4,�$�M�-|ai-&��#Ix;�X��#��|5��l_
0�Yf�G8Jb{�~3E'���k��|*�H�����X��/�X�
c�L����H��	D��&B�%PU���$��cǒ\`�_F	��)p2#���"�7���%�Òk���;9�|���lq9���0Af��=K;-kV�%�?0�Z`,��Ӌ�4.qE�3/d�cT�v�
�8���zU=;��.�'��`MYb
Iм�4?�t��\�O��N1*f�'��Le����JM�+�]�@@f�EK�VĈ�.�	�9r��$!�@�:�aq��ǅ���lT�ug�rȭj���po�)㢪l!ëmH~(&R
"�2{N��6s#
NY�ʙ_(b!�[�h�GG""M�%֞�/� ��Hbe��o�I|Cvg~��������((`��p�洄�Qm=��� �^ c�.�����y-�nuF*:�7[%a���#��k����lf)0�b��
�<(F�*B�-Uw��OqA�C�y�3��e��:y�f>��Y�:*�T�{rz$.VS�8�G�j�G�U�S�ID�u'8�1���TU8�IFr!��\u&�$>si�\�TȘMć<E'FȪL/�r!%Du'j�����o#vp0PYf� ��=k��aӞg��"��ͽI4 ��h�@����v/B�~TQ5�*.P����&E��&+� uJtyb�Q@�8��{�|,�g��I]1�-`c� ܒL��S�	N;�MdF��;N�@��& a���h�s:&
Y�I�U�Pfn�d~�k��l�es�pmL10�0 �t�T�+$45'�0�4Q!H�
x O6���i�3&�W#+Pp-��cQx��� '`�l�haH=M���*�[y`E�wm�'=�0g�XD2�~�d���L(U��Mʈ�Yn,�D�5�RYȰ��k��}��iGQ�}@=;�$<�G%��2W�,��c6)`(ł$h0����=��T���&�v�i6�"ya�(~��;Ptsn�b� I :%'��66�oA�īi���/�ʛ.��t�x��`�a.d���@7B{��pSI
s%$�|�j��:ccm�Ne�ʼ�``�u?ў��pF��F=�,�
�ur2�8j��E~L;x eF�f��6%Um
�=06D��E(ĲV������iR�c ���8)_�x��o
Md�¦_n0Y��t���6&J���xD��c��m`��+){�v-�졾�1�
-�kP;C ��5L�h���QuNW�a�-�R\"�1���Heis[R�=���?�I�e�<x�`7�dCN3����aqe��vj�%hr^FWX��.mAW) �|�怯�3��>�"$U�9`����o�?�կ:zF_NNlOs,/��r	��EqH�+nf�� �``y[���3��cć�f.8�;5�f�E�cB�`qa�5��<�` <d���B#:�fǺv�L��d�dq�����;��a~yx�zmZR~��av� آ'fb׬K�,3����3"������pJ�BpXpz+K�W0z�m�T��9u�@�	f0����-�NE��D��s,�9�E�4Sb���K��/����a~�8r0o�K� ����f{��+��V��.rȢ���|m�.����};�Mb����*;w���c��m�Ƈ���r#�+�o��;/�>��o8Q0 KcX9�����8tJ�
]k٧@e
ɖDS,"[��0fI�-��-��]a�y�t/Qf������*�1�K��(�m�N���B�vO�8ñ->��
Щ��� w�:\��z��3BG��o�^D��'4u�x��E^�W�p�K�ֵ%��gH+�a�a+3�qx�jax�-�Px��,1G�KN{|��z'h�y�1��
I'��5�I��� }c.���+��"Ayzo,O��f"EVo�e}$mq��n8���H� .>�h��7��d�E�J�!���[e�(�8U{�$��Ru9?�.�J�z�DvI�ww3c���r�;b��-eU	���FN��	��%�:�J7vh��dP6@t|��nN}�<�E�Lb����waz��[D]%�.�i�R7'�Ѽ(�@ z�v��Xd�� V�,��e/	��G�l%��* ue��\��,h]�N*�H^���m��n�R<sf��b	y>c+�7Nx��G-l��-��<��(zQz0֥�Y���*�
�l)G@��v��{�	;<�`T_&�%@�g�X���C�ˏjpih��8'�t
�3�����1 %��{%�N`�8���d
>%J����"K���^���'���r��i�|��)?wV��(|K����<*eEZI��$��)HA��.�
��L�]�m6�!7�V���)�e����)�xm��
�Zc�4Ƣ"�U�W̹��y;�< @��^Y �T�,ix��h&��X�`�j7e���P<�OZP&v�+C�	�M=���C��"�88����O᧢�4��@P�X`ܯ�/`�X��m��u1c:^�[3mVW�1�,�pjg7�3߂C+U�]]4a0K�P;Ӱ?����%#�$�j��l�j�m����mn1e����9{���7]e��3E\���)�bә`�<D+�e�v�^.>G9���� )mICC�%?�"EPe�w�,c8H�"�E"'&����Y�ܕRK/jZ��
���zVx�}S�H��WDLYYvif#N$.a((�r�v   d�($Ty$��L��o�1�4u��(5��JQ�� ��%r� ���C�zR_"Z;'PP��� L���iO%�il5�vZl�e6�/M7r�kh�(0`��f���epl��g���l�JI+�ps�\��v�.�<�R*:�@���xm��zCpG�d��`,3c�8{a%�g���f �������b�p�(!p�,j1���Ã�
�J��zt4�Z�D�_�O7$y`�r,'h�F�:9���/G1C/7c��Vfw��m��{>��_ 'za�(*Jbn-�2�as2�q&*$:�l+xXl��7��eA�	�Z��d�G�#[/y|�;d��)<J����Pv�R}f�p1)��" ��D�^��)A'��3�#�g���9�}�[[�_�I�=�2IkI%�Ĵ�CZ����MP�)�Y�($�hp8Sπ5�� -%�S�8 '�sehs����)�g�>?�:��Q��g�^iA����y[i�6i.�xt(�:?�Mv�$��<8]"|��2!&$#$&U`��C���8��do.�d�L6�k�wDC��KO�p/�`��ޥ��BI� �e!:�f#B3���/�nAGo�UqQ'�Vފ�I�&��d�EbC�$�1��#�6	R$8k���YR��0(hnz��T�.�}�����g Pq�[#�93� ��1�ldu�,=�d��������f��@mf��'E&�_QmRDh����5���]�L0fg�wS(1�(Q~�.p10��?$���8��R���% ^@��h!�DZ!�6�Ce�;g���#c�>��G1Ȋ��l����"�;ds~+�.�S�#��*�8���y����;Hm �@`d#M4Ws6w�
�$��9� i�ya��k�b���$�wb;e�����`�d�r�3���qJ��z�d�4v`hql�j&!W~�;�0uoP-��a��L��3�hN�CatdV���!.�q(RL�y�_݊�3�̣p"	Rb3�2ph"�9�/��{������vi�*c%�&���ARA׀>����#"8( �" 2��go���F}h�W���E(�c!U<Gpn�z D,�Wr�9�o<n �+��s�@�e�b�ֶ!yD'�u�4�HAS�|���:&&"P���� �#f��P�(���@�2!�L/uW5�\�+C8~���yKyc��h)8$y
tVJ;kdk��c��
[Ʒ&���q3r%[�'cu����Hj�.�8�Y�幽5U��A.�x�	#*�dM�k��N��D�f���H�i'���i��L���$
!!�C��"��`�f�!N$c�b�SxFr���_nJ���5�D�.Mŷ]x��5����
�f{E(l ���f"�3�1bYk A� �p��O
5�/0c����@��-6S»\>�x��£FK��N��O'��E�u ��Wb0X�
~5r��~)O1}�m���Q
�:)�v=��h�_�,0rln��a�e���-���u�H�:=��z�����xm&�hn4+T������w! �^&�e;d�@9hD{#j% � .x!��"$��#�϶��N�L�����	��V�e�����c�hw�bp�EYOQ��1щg]�O�$&�l{�}X�[�vT�3�D��ܵj
�$�>E�CDJ� �dhm��`b�z���:��%\�1*�e�����ja6;0����0'b4$9���qZ'*�|�m�ޅݿČ��p��l�
t�,h�d4�m"�'| �8.l
���@$���ad")�)#� �b�)@��pTrjJ8m��8Ac����L�b��,3`>k���s�e�?��-��u�3�(��S�s�H�|V�.x�. ��|^*q�1���m�� � %��$��Ed��@�Ol�|����bsjP�`�4@�^���� �=@�%(�DS0pnE��n1��#"`G0����
6g�:p�a�lfmuz+�ȪBo��(���2+��2�ڄ��a(ōvmnBa%�~�Zj]k-���M�MC(�^d�9�`6o�� jx�'�P�6f����('9f��W���8!�V'�>c0,���  �/ zE�X334�`D��+�3$6����쁸�e�s)2���bp$"��qX`����6~r�5^t�-���wdu�u+H��k9^!��*�!	'nokx�2[��
z�%�8a `�?(��2�^e�r�s^�Ud� >!2�&����vh�p �MS0���h��f�t�4~)�!�h�l,G�U!f6r} ټ%�2%�-z��Q*����0������!�#��"1V䩧(@ߺ�io�yje�Jp�<GS�tE��ר�h�FC`Yoi�|"�>2&$!-�H�c
*f��`Wu�p�Ph.>Ƶ`t'Hv&�
�Fi�\�´ �QgtztnhJ[R���t�ߡHESTY��skPg0(m�؉	4�2g�.w,�����b��bӾu��iup%�ܣ�b�60p�d25��^��8fZ��F��k_ez'@�Yx��K=�`)�Q.#8�P�U�*$#���_Ġ0�b cJs9X�b`b��O,���
@8�#d���jz�sKvd[l�G�xfmb|�:u}m�����qz4J�cX����٥�Em3���f$�'�p�c�r4'�D�k2�0u�[�p��bM����h�����cX^XC�p}4��0(�*��zl���s5z�vPq�`���l��BC�3���8��a/�#1#}��|�Q�&�o�o�No1�G��ߔ��
.f�㶳$ 	����?�x�*��'`��*XB��WVs����:uрR��<{��U�LrnF%"F4W`�21�< XLT.%tns䵬Js�0�um%ɐ1|��>_�60L�ݲ&�Z�tz�a���Gf�i�d�2�5�\�)]2R�n��`��Ԁg3�n�Z$�a�!p�8p4c^#f�wW�I>.�+j�`�u�7ae i �C@fe &�~�I?�B}�lu�[1{ �|g*"w.T�Vj`L�`n�rB�,(7"el?�XB�3�&K*�D�3t,,�0�:d�Ƥ;��Gde�Vmp�{wO	�qmO�V}�Cu�jh��|wu�HK]
N�f �-81mx�-�� @�p���`:+:��x�N�;�2��=$su��\�Ba���/�7Oj��pUd}�Fb�p�8㿕�na�3R��0�^*Rf@���X�WDg�pK)��h���kd�c7p�v�&A�N�X2��*3
À!�N��{E�V�
kW�R�mMB�wW:$�L$*j��iA����oVD$!�J�eq���g.A_OW�TL���U@%�.�QⷦR]A]���@��y�mD]&JK��%T�*��~�b�Y�#
��n��~nd^�y~"~F���0Ůk1#Q���VJ{�&7��`$��
s>oi�"1�d,�HK )��%�V4+B#9>s%5`�^!oӈp�vjqC#j�c�@o.�:�j6�����Go	�N�	r"���B�_:%�n�u�foB)o�4
8#�)E�kzIty�3z[E�>ip��ZFv�r���{�R`�ݰa��x��"ࢶb���B�wE���ss5G/)�:��iv�L�]O98 bvrCij�HVr�[6��tQY�x� L�qI�z��D�m�.�>ǹ]狔b��|�&��H<q#�`�l&�xQhRk�G`u�0�r�1S1;pU�x[+İ��%g�5��3 VB�)s���qR�u��I+*�B�|e�1�����qSW�c�@?t)|t,�b� �*Ju$�����
)���0�.f�cg:c�
eY��a�sɾm��a+&1/�3ȣol�,.<:g�?���@�2�4���:Rde�D�2��m�.qeRg��q�	�S!A1���
�d0	����Q% 8a�}.;������'�1`/�%�.��&�d��S47rDvp�9�f3j�I0��vV�֦���n2P-�%7m�c �Bi��6z�Js@*Ypl�$ȩ�d���g` s�Juxa��$}�a�%ڕ�N���i1̉�k�+�q̴�@b���J�si�
޹Hae���uUl`�hl[�r�ud	md@�jS}D/��[+?u�:��ڽ(�r3;�*�+%@$ow	
!9!B$�"6�d,!��H��$%j���2jD=�agR���!��q�6<½9���(��l�ta��ht��D��q]������#�x�"zhA~�0���?1mj(����}�R��<7��!A�.;�I��)by��0�y`F��wɾ�0ˤ :�ǣ艊^�$���4��=5��U,�0d�{&&v]�T�clo��,��4<|z{0�wxd��pN �5cO�iB'�-y�}��:�x.S����z�	����d("2�^Zgsd�@����f�����\�^l�3�J���vl��o�1]����{R6H��52��8Q{�]� JoZ K�m��� nk�]j�f����U¬#j�L�a�Am��b��}+H��)j�F�7f)�ɫx'uO�� }�n�1j"� |�� j�G�77Ȭ�-ih+�.��x!�f  ((`
��2���'��F��[.�5?�ygf�c� j|��z�t�Pg�y!y����Yd02�?g�8$=e=�z�d��F.d(@gIuew��}�d��59"ĩg#�4�
mm<���w�g�8�{��q`x�26���nt� ��h�
^�8V�/ %5��a�Ȥ��7F�|휐w4cs�e�
��OU�n?�!2�I\�lL�a�s��'>byZ'RW"�C����K.BAei�*���>eVy,��+[\h2\yE��5?f�gPf_+���2
���mb�f@��FL��GD*bX�d&�@^o|�rNE�{����/41<
Q�QM6_@�@�p�'u��k r�,�
#So��ޚ4)k�e"���x�o �7g�co��q��fl��3�#���x�����J��)j%�4mݠ�"��}5�y���8@�:#���Rrl_�b2
ȲJ�)�("?�� �T�	�� �xm�/	ci��NЬP�6�2��մA%�)�Lɞ�}d�aI �(j��LL�UG��~�34��)F%���dAY�� �
lfr0�4�D��� f��poByT$AN���Ź0�0�"�0�\�q���3�9H��M0
��i��]�%��\,`�OK`0dRdqA�E(�:bJg	#�:
�gn^�ǬK���q��K��86 q�-pQ:��4��y!�ib������{_�bFlNx4ΘJ��K��� �7��$���Py
J�H
�Ƙ1�~7cdG,�>0�j}>�<0#E�:d�k(�grS��Q�Du/I�iw�b���~v��*P���9�p.�B�0�=�`�DD>//��?~rAF|/�Ă^�%�="�����nG�}�c>�+�`X�x=�¡�-�Bj(Tqa/� �8?L oxgus[ֽ�D.vƭ7O��P'En�=�@�5��-�S �?);�|�^���
0�k��i%��`��dr�~,P�yA����aCl)�+Drao��OJ��&�"�*�u!�h�x;0��u9B=F��m���
� ���g��%�(zQ�l�9/��  6m�*��0G|�AC�I�9}�W�\wd�W��y":�i@�mc{��q#ȓ
/�`�~)�G��"	�~n
C%<l�`�"Y�p�Zgm� 8d� C�nj1�.N]_��W�t�_x�c�&�_4�@T��NdFr>!dj�(n:n(#���Z-p����`*��e��I��f�o3
5=��.�2 �B=� #t��|�F�dnh,���y�ǘi�ڍ%d��As��6[�h��8GQ&��|�e�z�K=#!w�5m"v�g
-��ffr�);=N���S�>��;��8� �]���4�jd`a�(
�:�&i3�(�*K{m	�d0V�/)0#�% f��^�n�:yY�
el�T|�s�"9|3��*���j�w��
��l&v�1!7�N"��5�)P��@3�+�y��y9l�26-<��c�j<`��9�!|�t�Y�y`iB'�P�E�`�#c����ԛE�6E9l` �4rc���^o�%8	�rwP1�yb~!�7n�w�y��ӽT��V�X�G+)B(,�:' p.(�"Г�*W�}�)�x�z>~tg�b��Wd`�-���",����bF�D��P�lm�9k��'L0�E`~�U�'lg_�C؉D��`<>��p Pd  ��(���bά��i�<�y?^p�Y{DKLi���f}t\��E
{�4EE`���i�w�|+�a�����P�}k�X�
k�I��d؂�	D�$B?�'jE���HE�Xz�T(��t2)"x)N�#ԉ%����i���0.����dq)���0Af9�k;7{D�a�? �X�~e��Q��~,qE~7/t�bD�~��8���8ms��?{�,pMQbi���65�8��P���n0+f',I�LC�����JL�;�M�@@n��{�F��g�A�9p²l!�@��xhs��A�[�џl�0w&��Ƚj��H�r/�(㦨m!�+mH~*"2=h7��$VWki������!֠���[&�&/ E`�3r�7s��5z'/naDHT���2�s�bXv�>h�1#h��5��)!���(n"�ud� ;ccmfo�;CbO(Y�	�_8b(C
�,`�DG#"OO���$h_�p�dLb���Rm�@xEV6m��$��1��B (c~�y����Hm5��Ȱ�xABL'������-��uF
;�WO%a�L��"��k!�ilN) ���	?}�L}lf�N��2��	6��stab"!� �{Z,�����.<�c�0�'�H!04��<�b/@�!u�{�)�oI�z��FΩT��y,1h6�;|�n�, _a=-��8�T'"���BR/�|(�
8�J[Y0�xISQ7 p�8�Vj�mm�+d鈛��JU%r|�t*]l4�
��{2�m0~Q,M�ᩳ�o 82/��s���uW#�oe2�kk���tgi��S��'kG �Pq'r*)�Sv�e�� W�PD�w�4ZZt�h4tf���b$G�w|3�N�y��)�Bh_$(D��OnzJ&�q�+({2<D��n�Zou�h�-�����1��\�R���_"���~8�'o$,��2V.+Hk�T���V]0�nzcE��� j{e�R����2��|��J�c�*e�1\�9 J�B�|�>kDM HA��@7q@�;q�
�d|xjǂ>ŀ��SL`[ e)`�q�"���X!a2�5F��-t�(�bL�YM�fV6T��=��t[-T�t`Q���S�Is��p������+ٞ7��7�VdR72���Z&��� j_�v��Gq�f�fT+Fg
%e�8�8>��Anh9p`
!D0^L<`{{�r���^WEf�c( 0�/3;�k��u�* ����ꭂ(e'�A�;x�%�wUt���C�O�& ��D�>"�j'�kL�Eqc1:�:0�����=-�i�<Q;�B�uL@� �8�n|]\���7�0~M-D@��$M={%Q"�D���(�$�
T/e��6�u�rDIHb��;��(6
�B�0(l�,�eF�t��E�g����ʺR0��	쾕c{(�!�8`��a�aj/��t}�&q$����'�"UL+9�^�9-����!��B��Lp�%l^39En�@%�w�'���!H���>}�2�N�*Rk�C�$H�
@/8��.����Q�0ϻ&@n�`.�S!BdB�NQ\�֪{���$a�/�`3:;Z0�;4�*]R4s�G�.�PJO;�ay��# �Pv��:�I礐4y�p��H�J�B�$fyAzӟ�S�Ԥ���f]at�-*B�h|�v?6�|�L�A��~r_aRx@v�\&,F��

�k 1�(X�w+\~ri�9;�e7���7�#HGFk`XQ3$ @�
˨�
C�#�p�5i{�[�>�2� :�\jv}ԠUD��f*.�F$�y�y�F�88�c�c>�k@��":u�N����m���
'�h�U��p*9��2,�I����x˧%<�@�⁺b)��&e��
�$����4�btM%�v�f8A#n|r1�Yk':�/>�H��di�O{"�Vg��W�l�-����iN��/)*�:@+1;�+/�b@X�nv#�T�$b���hs� ���q@��n�37h>s8S�%j�d�+�o*6'�KDp��+n �g�i6�.$Z�C;()1�F@G�Qy�8ad�U�l�h��h/��2=:k���]�0r.�$�_C��U��� >	N.2pa��&*�r�|�:,�I#k
._��f�O7/�9�}�27��W��b�82;�z�C�tU���� k+H&�@��m� �ebk@6�-peXX�Yp,TQ�/'S]��73d�x.}��N��Bt	�H6�0�{uc�"0
�;@WT�,wU)���FvJ��};ƈV�l2�	KXA)b0��].{�v|�1ǰ�m`����wHaz� z\!��*�2�v6'Q�*�@��4z:�GdT�Q$x.s�� [moJ߭�G�t$�fy[qe��M�)���d�J ���;��U�u�Xv`R�rksU8���l,ckU=FD{��w(lz�
3i�� U}����<�b$8��\v���#Z8.L8E~>�%@�gM:�(�hZ�)Jtp9x��]%>-�Lԁ�/�=^���\�Hy%��s��e���$1 ���w$P��e5p�}g<j n%���hn.�?�v`�vqi_��j;�<n!�`�$Jf ����"N˷�.-�,�7:��"��i�|��i?W�R(H�7	�%�$Fx�IP�B% CiQ����i��cki�F���i�_�U#� �T<5�8te��)�vTW��=v�.wjI�E�}�BC���D`8�Y%kjq����+`a�~)��P�c����.b?�
���zt,�5s�S��ݦT�yK��F:��v!7Md7�M������	 ��e��E�2�0t��(9��BU��Lm{�A��Z`{RW�f�.Q���2� /����G ۱(-�d}_`�������Pd�ew�M@��0?X��64jG_'~jh��wI+�0r�\��o�
(���pV 1��Ν1(��y8wzm���'[.|c� y -�wt��và(�����a�_��3Pu�@�w��׿�iO��lc3��0�� �pA�L&��g�$@e�=&�/(���u${c>##��cTz��U�d�'/��o6�cO�a`�Dhnh^y��&5OE��]WK��*sa;�c=�X{�?�i��D�/� z!`�P�ɡ�+m���d:��,CFdE7&��[�:1V��)v�A񇇇���$2�ޛ%�,Ӭci�t�la��$[r#�ź�1rx�!��yf⅞y����@g��T��f�a�"��I"JH#I�׀�f'z�o��
����)e��T�$�)���Py_�
%F��C����u�*<u�p����O����c��*� ;%���8Kgi�a/����@�w#;q���8���v"A5��u�����B�h�t�+�EَvK!W~N�;�0}nP%��~i×����L^�[(TnV���).�g(C<�y�}}���7$�V6�p:Sb���2Po.���.�*���lk4���lNp DU4��H~h�R����'�<�H�cr�/��g	��'s$�4��"�/W)6^0��w~/�Vr�9}vM6&�j,���P�g�)����yE%nN�t�EI%�L!�:ng-O�0OY!���0����
�1�a

�� ��212e�h`1���\��HVͨ�1Jy$i>bͥ�#/�#�&e��h�]�!-�&�F!��qo5 ���D�09e<4]#Re��X��I�Apg.�]9�H쐫[(���=O!=$�"�4b~�1T@K"0��,�=��u��-"Cf
GI������pPW��X��~ ��2w>�` x���(�"Q�EkZ�3RTei�CH�%Y�/UMw�0 �\�r�+V
wFH�K&k<�c��
!!i�C��"���w�a\$CT�b�aig�ިMnB���1-��'MŦx��5!���JH��{El}(��(�d.W�y�{d\rdq��;7���O�J4�*�Z���X�-2T��\>�x��ģ 6[95B�]'��Uq����'[0DX�
~-2��2)M)`�oñ^ho�&��[�l|��rb8d&Q�p��'ט�ݣ[�"x_�w?]C���w��my8�c��&�w 
�@rj`z<��s��(lG��a�c�������>��\�:=��s��+��[�e*h"��(�
�$@%��%��aLw�@�^L��tI��bv*`�i�4`����� ��Q�$	�@[�pjD��n�;��#&x�6�(��K6g�~p�a<��"
�o��a3%�|�g�
u ���;!G}V��
��~(`Bx��>�0&D���/���5&s�c`Y�X���<��QTj$%K!����M���KsAVM9�Z0��$�,�OK|j��<Y���y�:~ShA$53�[�A�t���bf��0a�p�Ph�kF��x�Pv"�
�b)��´��Qfv�`.hJ[��Zua��DYTY�|ck8&�+*|�ȉ	41s�~w����zV�ユbӿ.4h��Qiup�\��"60\H�d3���\Ž�cuՀU�j)_'H�Z%�b0� a���m�Q.s8�R��M�����ۥs���x�rDcJY]�,j�>Oa��fT&v�at���Zxn
>�H�!vx�{}�\l�X�qQ�.�{��%JO��PԲ<K2F'��8 s@9�|���W�R�I*@��'����)xS
Jmf<��b,�<�xh=�"Ԙ�Hxa�K���N�-x���-';۰�N$�'����b 3&<P�(Tt0'�_�]��;��E�?�A���bHJHI�}4�I0 �*��*h����lJ�6P�|`V��:$\�q'޷��9�x/~%�R��d&�Ť�񸐡��o�
Ko0�#H�ۙ��.�X���je`����K�1i���S�a�Y*i��Wp��i�S�DLe������7a�
T�l2x�%6ftO`� �	< XZD7%Tfv(��B{�0���o�ːqH1GK$�6�NH;(}t4k��'�!��f�O�wdF2�5�]�i�"Z����!ن� �1�n�VD�c�+r�9x!wV#f�sP�S>-�jj�@��7@do�@@ge &��^�[g�F~�~}Q191aI�<e
"w-Wj H �BrC�(X(7"Dl7KLT�3�g
[J���sd->�4�~d���{��Cnu�WEp�{qWuum^}�C0��h���$%�
K?M
��.��~ndN�}~�zG����Z)3�]�JB9�7�dR\
�.8<)ȗ�s�!H�T�{�Wօ0,^��A4�G���{c&���,�0�_�(��M���8�Jʔ�`�_n
'5�t�z��%)v"�=V���tn+QC�,P�d�d*��YV�b�w}p>t�v.]�!S���n+5z�\P&�]�+!�m0����"4P֬!�"c�2�ww�N ���,b@�2�]Obkh�;��9��e���c�nfa�
p!�m×Cl4��
;$Z��,X4�������<yV���A�=��^�%��^(ky �8��l���9�mxO�*\��$dY��i���4�x��#6��7w��|�7n�r�΢b�+�4Q�B��c�100�$e)%�~b�m�.agQG��q���a1��
�k�����({O��Xm��ه���u x1,/��~�.��<�e��S47zDt8�y6��(* ��r|�bd�uL,���1>tb���M�pt�H!��զ��7�أ�d���ghs,j�Vi��$���%���
�)C�zd
p&,jsCqw�e:"~��p�{"3���{2�d��w.��nQ-D���lkn �U7N(�0��O��1��J�yH�m_��uul`�2L[ar�uHt�i`M�:h�tD/Х_s#>u�?�옽
�$`
0�*k+1@do0wIA�%`m�_"��e&!�
�3�%%z��I�4nF?�igҙ��#��u:&9ƭ(��󺷏��!�!�`4��eںr\�����3�X�"zxCn�:��	��(����9�R��44��!Q�n{�K��jyպ4�y`F��uɾ�pDϤ�<?�Ǡ���V�,���52�u��],�8e�{&f~�
�il����<=<z{p�|t­�Z �?�K�i@/�
�;�r,W�!��z�	[�
V��4�b2�^r 1t�@����桬���^�_o�3�N���,I�k��Y�� H;BvD��=r�q�U{�_�"J'Z,��iU���\[ ro`�]B�g���ptʭ'*�D*�a�Pmg�`ʽ$}+H��)j!�7$)��+=Ry&R�o�� �Pj�5k/�|��6z�w�6�̨�KAJ��/���#��iU-EX+s�_���l`*�q {�u�����#hb*u4��пn�q�~"�Xa��m?7!os=-a�c}Mw]p׵�	�nd7�,�[��(=@#����l��g��u~O����d��)y���
p��47U=edT��78�/·���Z�_!X��e��-�i�zCL;tJ��TF��t>�H����)[�swZ��t���qǶT��~�1m�y>��s�J&:��!�<�Qi�y$p����Zg�$�?��	�,$:�}�c���C090aArfM��E($�6(&�~O�G#�
������B�%2(�/�k0����}��$�y1%�Ek�̓Js/�|�����`i�L972�Z�"c�Y�>��)h��ǩ
�~z�O#}�m U�~�����b�����]vw���e�1Cft�2I�R	�vt;x�-0�R���Eq.�N�3�c��怿Z22fg�d�`���[1I���6sk=|���YP"�ww/<cd ���q-8_k���ô�Ql�!��p��/���T1$dMpc(2QUV<��ƛd(B,rbB"m������Y)����5`����Gr�vTR\	!�x���-!�L�:4�c.r�KaX5w)*��	R�&�v)�^3h90efNz���?\|4�d�ci"����73*���Dl# �Z��g�s�7�(��i� y�#���b>_��tQ�J`1kvV�tTX�Е�o��*w^w�k����/Tq,OS�QL&�H�PN�g�&t%.�"
�,�	�6l8`�|��?��S�{*�>X����`�w�/���b
�Yby�/S����
�l�Mr�>d��3�'���(��a倀
� f�;� m�I���,��E�6?���~P࿳��YR{m�n2ɲli)�:-&>��<�T�ԁ �||9/	k[��ZȦW�6�����j$桴L��G}8`I� �(�M�AG��v�3���9����4S�b��`�l�fkr��D}��w��0oR�Tdn]��ձ0�Wp�2`0�,�q�
έ��2��{�doY�S��p��,_*7)�٠#�Nd�y��u>�^�SFe�b�U۠�0\�:�,2oY�,�Rfuc��c�Ax=� �azA��VuD�%� @)o�n

���zW}�4��>1��=�<0#VѪd�k<��SC=��P�eD�HKw�F�ȗjv �*T��=�,p*� �0�3�`/�DD�>>
�?�;~;�R|(�V��%�4 �����o��c-�+�pP�|=���Zb8t�a<��9?\(�x�g�QF��.4N��N��p%En�9�@�5��,�R`�?):�|�V�z�
��,%7Ç�+��0_�gvh�D�	�� b(?�=hLC�>��i! Wh�=r9Z�zy"û
����l�ݸ�3��������3�Ƙ +���1�G�d��a�y�[���A(u���5
(��'��+B%4|�h� �:p�[gM�"8d�(C�f{�B �&��t��_8�c���O�AT��dTR~'d(�h^;� ���Z$p���
�ڙ�\�e|{�_)	'!W�=g8v�o
-��ns�9�N���C�=��"��" ��e�����!Ze�)
��l�v��26�OW�������(@�#��a���]�23)<��c�*<
`2ѱRWx�uf�Y�spLi"e�E�M�`�#�)��׾2l	�Q����q.�f�w���p^�.!�*_W��7.�wy�1�=d���6�^�� c�h&�+�p('L+ز�
7�M�B!~��pt|0m(~P��xC��t��"��ߥ��J+#��Ƌ3�/1j�Z��llP�a>D�f{�KX��Q��`�~��t@0,d�����'��)����'����cl &�^{I)�;��f7#u]���
\yGv!E걠��P��*������T��g�|�
c�I��@�ڃ�	VCdF�+A����M�M^a�V0��/t2)"�H�7�	Kz�d~�&���?<=\���$q9��0@f��j;�Kf�=�? �Re~6����=qE�7/��c��v��8���pe)��.�&��`MQb
IҼH�$?�t��P�GݮNi+v'"l	��LA�����J(��M�B@n���V�&�H9 ��,a�`���`y���Z���dP�vf��Ƚj��L�p/�)c��mi�*}H~8&mv���ki���t��!F����O2�d��M`�;
b�2s��5{"laDPT���s�c�bZv�>x�1"h}��7��!��� ~"�Ud�;cc�lfO� ;CbNQ��9�B)A[�>i�GG"&M_���>�/�p��Lcɇ�Cm�AxAtv{�������B(�`|�p�����%5�����T �& �����(c�qD*:��o'A�M�"7�C0���lf)0��MA~y�l}�f�N� Ћ2��s4Ubga� �{Z.����6.>�"똊.�Ja0���:b/S�N!e�z��	I�xv�D֩��{<!H4�:x�:�$]`4��1��-"���JJ'�t8�8�
aXɧ�3���s[�!��q�ju��K:�J�fp���2�)�eOqq�s �}�c|ke� q�bi��Y��d"+5�rr����d��d�m���#+я�8`����0���U}<Q�M�r!��dp�$>sh� �N�M;AdFI�P/�rW�3�#�Y����i/h�(to�lp��-O�T;��h�[��2��U0n��j0��� ����]UW6�6b��
 &}�D3(�de~bhl�%rQ+�mh�s��EQl1�,�i�*�@��Q�Hf�GJ��-\@G��(!j�[�y7{�Z$�e��9�E�^/,�f|�1�1g�(K�}mLha�*gFt�B�$H9#07�0��T9I�paL@g�_O��~�7>x`x-��g"�DҨ5u�n�o`U/O�\ ��5&8A�+1�*�� v�1Fc�=.���C$xy��F���D85���E_����]k��a��4k~N�di;U^��u ��/ �u��n,;P��|	uM��u��r���e�h��QgS�,$*�O �y�/Nw�K�%�yEhs*�oK
5����'��"���]��x�&�B1�p=%���n Iq��)
j��^�V
dc�
��`�p�ǂ<�����L7`Q@E)^`�q�rz��]ca2�5�Ōp�(Ac̰	E��2T¨-�
�{�K���q�j#U˕c�� atI�[x�%�Ed���C�o� ��G�>v3j'�kYD�D�1c):�j0�����=-�k��|Q?�B�uL��	�8�lt���n'��2G-���$m=s�G&�E���9�4
Z�FMD��8Z��Hڢ��p!	)�eaz~ ]���BG\!m���v�:DT/]��'Pp"�mHc��R?Y���7�5�@*n�8��f�$��5� �����0"�������x+�#k�@ ���2�c�;;=B��h��>�~~`|�9e����A�H�O�e]s��lN#yUL�@9Tt{��8��AH��u�r�^�8h1�é$E�"��
@=)�w�l��,�S�8sF�g��Tϱ"061�p��}�֪oތ�$b��x�x3_�#4�[�Z�$�C��pU�=H��Rq�.~�:�I��Qp����L�J�R�5f�]Z߆�Q��W���"na&
��4�K��#�A�6o3,k#Qec.t .�*e�$�5�n���PxF�+#�i6�S��"�`1�]S�4bW�� �s!p4�i�/�k�6'8�F�A�)4Kdqd�+�B.f�(���yt$�԰�'�(�(,|��p
�b�8t��3fؤ�rc(I*a��bo�-�v*�!>�l�Av��f�(d?�sB���l�Ѫ���b+"�ٻ�b�`-��v03o�H�'�>Dsqs�g7�28�VT5J� 5�]<�2��3R[�*��.Kje$����[�q�LA�EFOH�
�B�ht,� �v��c��S���baQ+���p&��6�p�?ioԽx�Xq�u`�!�|����c� 
!IQ<͚���Ua�!�t-Qb���! 0���� ��	m��B�v�`��%<�-Lь�p>chp";5��6ip7 �1P��p?Y�m�s(�e.�Xja�J�� w"qdp9�0�t0a�P:O���I��Ń=��h�&X~^OxvF���
R���Ym�azi=Ea��d'Vg�k>�:Z$�/i�%aoi��@�tֆt׺;~��<������D$)����š���=�H��|�p1Ǩp�f(K���~���h@F�B�(_P��<��Mj�
 Щ���8v�
/^��(f��3*�1s�;�`7����cиr!�;�C�$U���%ͤh+D��e�O*p�	ebrA��(�e\�#�I�-7�7vAL��'#H�X,y��N��@tm��6�F��qw��&p
SV�gb��AF�PZ���G:�ñ�i��e�����c'X���h�a�Ż$2] �]��!��sl�p�Jk��'��=5apP[M�*"3�%�c��9�l~˺=����h�vI�0�$�-����f:��Y*̡�-eSDO_+[� Y���� t�Ű�P�D%����`��%����O��:&�A�Rd6�*��KR��t��� yb*���#��"AQsMNM�_��jQzm�g}�/q(�|����H8 2}����B��<�VFIf� ���cm)��Uz�2,�Zq��,�iq�#"tM�ws#c>��"�S^t�dg]m��VVr�1��� ��v
]8�n��<b!!`�e
v!���bN�)�Y,� �b�&h�|Ěm?W��,lK÷��0�dhi�C2%�(XA�wT��d[x�E���ɱ_�A�� �T
5�8~�AT!W�v^��=~�.EjI�2E�mE&�be����`�}$sjp��[E��)r�Rm)�u�K�������*b��
�s��$���5�*U܋�H���9�i'��w�i'��6=!L+����,�xI���h��%�l�� �R�?JU�D�c��� g>�)�ڌ`:$I���&>n�uc@|�*��;W��$���L�85���	EGg3���� �Zc�4"���U�u����ٕ;�< @��^]"t�.�x�-(&�%0�X�H�j7e.��p�_/OZP&r�+�V	5��C��(8{��0O᧧�4��P1�P`Ԯ�m`�H��e��e1c>^�3mTQ�1�,�0*7�7݆c/]�Y]4� *�t3Ӹ/��j*x�%!�d�~��l�j�m��T�On1e�W��8;���7�|%��1a����"��"��(L+���w�;�.,	C���ù )mICB��rEPd�v�-a=M�*�E"'&$��ܯQ���B+kZ 
[f��g�~��`C�G!N$:]n�M���q��)M� 1
���zf,��:�R������j�-����w�;:��LBf2��>��N:,v ���E�0�4v��(9��@U�v ��%R�@T���wA_�f�WQ �2�QN�O�hE_]� ahݝ�Nz胵1�(��u�V%*t&a�C��d"�a�.�m
Z���v:�82���.����V 9$C���|m��xt^s�����;`�xya)�g���&ߠ,�������o���jU�4i4�����C�2 �� _�Z�\��Y�,i�l�kDe'8�S��#�
���oL+Z/��Cdr��(P9�{"���|�z�UC��m@xh��(�op<��eql�:�`71,v@8�)��8
�]�q��J��$[Q#��`(�x#C۳0
n"�pbk<@��&[��[�}	��<Ī����S�3i�lI�m�Ne�U�I�>�6TkS7���38�����R���Q��d��rt!K��{�l4,��5�>�3Cf|{�棊�h�Yѭ;׸��.��e�1͢-���Tn�p,]#�+g>�5}�U��$��dEq]q�V� W7V<6o_�A��Ĥ@��d%*�t�B��1^|G��JA�9!>�u��\����XXsE�&��8%�pe1*���$�(ACwlsS�0��W����4(��e�SSd�e����0�&y{�/�t�FqX��#{ 9q��B��!�����#-�h�	��[E��r�|-�"z��d������� ��I`h�2P�@�z(Tv��	����5�<�l$lf�9`;�� �]E'0r(βX��5Ơ�X�(�ɽb�1(�Z�`�0ZaH6�m�e1��	�3������|������a#�񮰠%�x�F�Z�d�� �{���5K��ŧ0dQ�eD-z_W�'�[I*��qKu��1IU�^}��r��l}�'��v��QQ��+��Ѱ:¶�fy.lBj =��@t}��K`���D(�Jg���1�*<q�p����O����bEQ�*�'���� Y�y	�}�o�ꆣ��@�?++`���F@�j�+����]��CA��I�OI�����r�'�'vN�;�0=oX-*��sa�4��AHN�S TLk��).�s0 <�	�o�"��$�Tܻr:IKf�h�2PO��G�Vp1xueW��{��#o8�7)�aYL8Ȟ���#=�H�2�-���	�A&>`����(�`0*p.ׄ� D.��r�9m�N6"�?���ѻ&	��Զ�y%zu�4�J[�m�Љ:'S�D���I
��#n�sP�'����a�MyvMx�pN�J:8~ﲈ!8Jzc-�+��xi]�4OyOF{GOY�1��*\Z�AN3�`��20T$a�h`���V��LV
],8�H�ЫY)K��o^-G!�& � �6 ^�0|CCf�-�.��	%�}��-C20tf���b��w�|lm��z��:vj8�d�M*�dJ$msB�����P��[��FK������`DW��X��~���2
u6H�I&c��c������BF�e k��KbAQè)(�Mo�J]s> `m�,C*>�t�A0��8	s,Q�l5�rFi](ޏ�&/�	,)�U���&>�b�#LNAs�9B}XЧ��S}��d�(�.1n_�0j$L�����1/z�	>���J�B�����oɡ��f�/.�r��p��iZY��T��q2z-��b7|���Yj�'�9�X�繽=T��a.�z�#.�|M�k��N��|�5F���X�)���l��\���$�!#i�C��"�����V0$C�"�C2iBg���]�J��5�Q�&EŦ?x��7#ں�
�F{C,m`���fj��y�Rsl ��8؀>�J�
t�/�[��z���-3R֣L>�x��£"���7]%��Qa��֔fr�H\�
v=3��:qM)x�(�F�d�����~tu�p~z����8� ��ˤ+=d:C&xWw�*K��n{��ey�m�j2�w � rB��0�/r�ݥ(Px	N��a�Gw������@D��H
а�1()�g%����`s6p�����'jT$<;���y�ڧ"�t�!���ݶ�)p�����KD�4h�A4�E"�5x�<�LB���PLA���aI2-
�!#�"�(�m@��TP�jJ8�h���c�M��H�c��d6c���S�e�Y�-9�b^�8���S�1�B�,F�.lg ��}\+��2���m��� %��$��ENs�P�_M�|�y��bv"@�h�4`�^���� ���%(� Q00jE��~����3'|���(�
6g�zx�a8qH�iwco�X�׫�����rn��7r�چ�a"��vinKe%6:�ZkO]^K�h��LCh�Z�*# 6/3� jx�w�P�f�e���8/1b���W���91��!�Z�1<�����%"jE�X�s4�hF��+�?$V�����e�s)$2����sp$b��uYh�K����	�~{�uV|�����7`e�f+h��k�L!��
��	gnoj8�á��b�!���"x�{;޲�Va�0�3n�u �* ~�1x���~`�
s|I�0������n�t�wm�(�h�l<G�Ufr} ����v	��/���S����0������!�2��$B���:A���+'��Hd�Hp�>��@C��ר�hB�CC X>i{|"�<3.$!%:L�c=��?2�
%"7���,�r�WK��Z�[��Y��Q�d,wJ�WgbK�q������|-u#hXc�`��"TmH0i"�]g)�4�6RQPwkvID�}��`9(k�Pd��&��&��,k.cKI�g(B�+�hls~��>�
x!���LZ~�F,C��te:�fm!T�w}�}%��V��H`�J��?����g����$���Gr&#(� �?�M_0�u�l!� {$����i�Co$�d��.��,S4[1f�Ǖ�1�Z"mft�'o��bRD�0Ha}
N�Fm�/�v!1�#E���-5����(|L3Uf�.��u $��$���w��"ڃ^�@Qe��PJ��#3�Fc�ޤ�>i��!s:'�|wu�R?�?��
�o}��#5	l�u�
4 '��;%WyO��	pG*AL-W"��Eo�#�	�b j�/\R,}C?��t����r4>���bTm��`�%|�
s�NU)�+!��%3%N
4G ^�&s�,�\Z/#Rgr(�Is������ː1h1�x �]�L|.�&�۾u�%����b��f�K�vf)0�%�]�)U2J�*�� ��؀�1�h�|�q�)r�x w#f�c�Y>o�kk�@�g�2@ek�CHfu0'�	~�[/�R~�tw�Q�y 	H4g -UWj�H �rB�(H*5"Ll7�_P�1�gKdJ���3l=l�0�~f�ƶ3�Gnu�udr�{qm	�um^mC�C1�kb���7��K�U%�.��e��\�!4yE-[�QP��s e��7i,p#F��]|��3����y��L�A�}�A�i
G��4T�*�����{�t�wq��,�nndL�)Z~'���*!3��HJX1�7Q*�Q5sIw�$n
�i^tߍ��b~����v�d��x�V.�C�S$~P�D�FiR96X�i�6� 4�U2�·�R��6O������8�mG�21�v�.�H0)�qY& =@Y!��q%L�R5Lgט80)f+q� j <�
�@6G�>0*�nv����>g��mJ�)b ��._( ���Yl�0�nkR)n�5{���}.�/E�;Aty�q{[E��{�0���D6�xvc��:�h񌐡�#x��cT`�j���ҮsG���35�W/)�?�Mv�D��M=* B��A)j�HVr�S6ጧtQY�q�$L�uK�z�_�L�m�f�.w͹}窕B��~y�&��Y<q#�h�t&�qQhZc�FXT�0j�1wS;;rG�x[+Đ��%g��� VR�)[��&̡QR�w��I+j�D�
le�1�����M�SW�c�S;�)|�T�n�A� *
� �����
���p�nf�CG�6c��j*7���N�hf��[xh��=�#gLd/hg�vG���o�dd��^����x=�(� ���Mt� �xq>oOq��F���^yf�4W?�Zp2uho��i�}�4�/Rg��ah':8$�-�b@h���Q$�	�$y�u����H�`�@����=k��Rj�eMk��AS��O������.�B��~Ch0R���ex�b�h�B$�Z�7>��N�Fe�
�tb��fd�`Kܔ�(��1L~��(���"E��׉kbD0n9�!��NFv��]M��SJ���Q$��xap�68>5&�8>A��*�a�`��"R40��
4#��a�p�4}�+�37!���#�|�6vG�����6:[�(�,�y֫�Zc�(>��Xl�`'�q��[ A�	W��g9���-58b��&�������#g+,/������l��e��S=r\t`�a��;(��j��v_���:��6pT�.hkn���(~/$>��q0X.Յp%��ȣ�b���gh!s�J�[|���
2�d& �@`2�%%j���)z5nF}2agZ���#��p/&8µ���۪��t��a��Hl��]��r]��Ϫ���z�"z��.���#4�}�(����q�Z��4�aQ�n~�I��=`���<�y`F��uɾ�pϧ<~�ǣ���^�%���5�=�
�&�U,�8e�{>fv��+lw����4}_V^{:��d���O6�FO�iD.�
n�J��q1_��qig@��}w3 Wyg�VNRh-�cݏ��R�$h��OaC�����&�q[a�a2pߧ$_� �ZM,�x����ɢq�x)h�&�W���au�x����_�]�IS&��(di��z��`�Z.cJ�B��kD�.�\;�8A@5 �L%P��%wI
�E�(�0z�l�~�]����r��ј�H���"�K-^��D�l+u9�l!�\�
�^�a�-?h���#��:�`%��8ʈ
g�1�8ܗ���S���Hy��c~��#��RRL/�n6h˲L�)�,&8��<���
�!����<x|��5��r���a��2�;�vj���k�`�56�}l�x��y\��o^�ר�%���1��k��8"0�fmP�2�4��y=�h~�h��0:_�bTlNH"4����K���$�w��$���P9J��M2�q�/YB�3s��+7,�>Y�#�Ld�yU�o�<�V�S�l��V���t$2��ro]-.��fsC��c�AP-ua�iK~A��g��%�`-{�V
�∱�~�?a$�����e>�<0#F�d_k8��JC?��ad�H�Or�b�Ȟzr��*P��=s�,0*�2�3�?�`	GD�>k�?y�;>:A�h%�Ɗ�/�4!�����|I��c-�)�p�x-���mE��j:Ps�a/� �:^ o�ggs_V�g�T/f�k�N��P'En�8�A�u��&�R`�)�|��x�
h")=N�am�t)�������$bED��Z�`�[ y�Ҩ�š!/�N����;���sf9pk(�,�0�
����t��w8�c���O6�PU���TR~7d��h?.6���Z-p���MQx*p�NEMq�K��f�g�
a�б���t��Y�[`\i"�Q L�`�#㹬������6lp1�� �Fo�j&�%��n��d�b1k��/6n�w�y��õT��6�j�� h�in�{'�p 'v�g:W�=�%g���~4|vmef��O`y���p�c_,�����w��P��C7��`�}�Gw�>
IҼ�41�t��T�G��Oq3v�2$��L䯡��J- ��M�@@n��F� �o�K�92��l1�J���`s����[�џlܸtw��ȵn��M�p.�)�&�})�+mH~8"V]lw���DWk鳩����� �:�� �&=��`�3
f�2{^��?{'mEzcDxT�ˏ3c�`X~�&x�#(y��1�s))���(�"�Udɤ9ga��dO�;Cj5*Y히�W&8B!AY�`0g"bOO��־.k_?�`�Mg���o�AtATw~��d����B �b>�y���� -5
���lqjǆ>�����H?`R A�`�u�b���]ce2�4�Ōu�h�g̰Y]�FR2tB�
%g:0��j��AjL)x"eF�憤�%Q-\�
LA� �?��"����@8s�w�rWJ-��a�><h*6��^��TU@J=�/biA�
�s��6`E7-���u�?OT��u��H,��M�m�%�1���4\�X&N�}*3E�
�/06$��0�2V���M�O!J�"g2�~2��5G$���~	Ҝ0�;89\�g )Ż����i^��t�U,� 4huͭ@9G�Sj%\@|գ��d�c3xo8:.��?>5$�'�dg~\��o��l}0f�݀6g�	�c�ܶ��>V�kcC
A�.+���]=}�3�����-O�zQ��`�y�=���>Ti�S7})q/��|�X0`$�d]bc$@�%�an2��`���qdBud�"Ap��#�y��M��%�fqۄłk.yC�	V�__n|l��{Of� �r����%�����l�
'9f)+�8�d$�d��e�g���Ϻ0��嬤5�{�+@�il���x��[<̦��bb�8و�0z�՜MAn�9%ﰂ�����N��s��oN#9E�P9l7��&���!l��a�ur�N(["��$I�EaA*1@�9�v66i������wj���u��@��R�'���V�o��� !�� �p�83z0�;$f��1��S�+�P�]B�c9��x"��C�Z�I�]5H����HrJ�F�%v�&݇�i���`�� lqj%d,��D�'��#F.*A-�gZFvEK,}�S4j�b>*�)�}7n�L����*�*ጰ3@�5J�%.��I|�76mu9
�14H� `�9<9mBK	�O7eI��g��osez"X5!&|i*�jo�$��5�>���Q|@h�k6�1�"�l�P�iuf�W���oasct�H��b�'��a� "dq���Sof�8쮿yu=\n����8�(<x��P
j�%�89�aq@[M�*" �!6�0c��-��l~�:s����x�VI�0�`�-���b:ۥy
L��%e�n�#S?�[���"d�E���V1���� d���%����o��:��A�Rx6�
��KP��d��� mb.���k�u"Eq{%��f�	AW-�dy�)�	��|����@�"%��|�N��06�/�)� ���^](��Tx�$.�Rw���>�{�烌{Ev]��s#cr���3�_T�g]i���v��魍b�}�C$[��Wyq3�q}���*BpdlC:R[#�c����wHaz� Z �'��.�9�yf'��<:�@��� �R,\�xV�p��%(e/
���ǻt��f� ����k�)Z�}�5�I��%�8Ҩ�hK�<Mm(��$1��fk�'gtq0�g(mxk�	
���0V(=�D��uo#�9Wwdi�#
��V�mc�`i!-��t�,&��,������c2���H=�PHL ����#��p��n��VV�tI��ägi�0+h�0f9��*�6�o(���u$+c>S8�cT:��J=�E�*�o��Q4�'BO�!(��&(%^��&�O���QwZ�
�m+S`3�#7�y�=�i��D�'� `!h\�$@١H$����d^4*S�tU7���!q� �k���� p�Ǉ��,&���&�,��si�t�h �&�7r�紺2r0�!�������y��\ҏYAXg��V��e�g��C KH#I�׃��"z�f��J����-N��T�$ q�)���Ty[�0y��KR(�-�`�� ��,s	y���%,(Q)%,ΰu0p�h��*����V�v��E5e�ֱ-lz�
�^3���6�a�n`�����l͠	]�	;�<i>bͷ�;/Dc�%u��i�M�"0楈f ��J��&:�0)��,M"RdA����
],��@jлI+
��n9o�#7$`�"�>B� t�&0��=��7�u��-�Gf1tf�4��j��}��m1����;rj�d�H*� j-�uB����`�t��Z��GJ������@�_�dY�o0��3/w;e  ]�����"q�MoQ�3
rei恃L0�$_�>U���1 �|1g�+V
uVh{Kf+���Oc�䅄����BDBeE)%�K:aUUê�+��M��r^ `m�,C*z�t�@FV�	q,[�L5��2FiM(���'-�	,+����&�v�#LMAs�=N�XЧ��}��d�(�("�_�0k$N����N/ٌv���IJ�J�����oá��v��,�q��p�=a�Yο���q2r%���cu|�Ђj�&��"X��{�=���a.�{�!�Ll�k��^��\O�w���@�i#���h��L����
�!i�;C1�"��d��� ^$STzb�Qig���]�J���5���MŤ}(��7!���JH�G{E(ma��H�djS�9�+�hm
�
41.�Z�ł
��$,3Sҧ\>�x��£f^�N0�_'��U�8wƀ&p hH�
v=0��:9)x�wSV�C��N�� ��ULc�QwC2ΑX����7��xU�w�>\C�����gx �f��6�g *�rB�n|��p�ߣPplqO��a�A{�椢�x <��K
�~���La �����ib#T-$]���������-O�e���ǵ�0���Kh�=��<�a �x �4�,B���PmI��aE3/�i�!#� ��H�iB��UC{i[i�h<tO��\�a��$k���S�d�]��9�w[�3�	��W�|s�@�<F�.h�/h��|_�GԳ��o��	�%�5���*AD��@�^l�����jw"@�h�4`�Z*��� �� �%)�\Q0r~D,�n����+g|�2���6'�yX�a0]Higb+�L�B�����2+��62���jb��vykJt5:�*X+,�h��DBh�rL�*# 6/�gny�wRP�F�d���8'yc)���go��91��!�^�<��� ��/)ZU�|�s6��n��+�#$V�����e�s)����{p$b��uqx�ɢ���)ri�4^|�=���7de��;h���;L!���I'|OgYQ2é�c�"��a"y�;+�߲�^g�b1q~�T$̀:)r�&����da�r(�I� ������n�f�%~)�(�`�n,F�U
;*�0	o�TY`�'K���C�$c�Y�}������w����7�u�QUf ��ؕ�vdo���#��Q�8��>:�&7p :��.�Fl�?NG�	?��B�7�Q�ɿ�qN��̉`$��)*�흌��9$��<��fQ^ىe*�qb����y%H�B(7���s:�\�h�X�`�u1#�"�^��c�y &8�zu��e(�ngb+�3�������&�ur6Pj�!��]g+R$C{dZg9��6�{05�	v!@�}��Ju`��|�1�:���C��(Z'>{AN%}z��t�`&vT`W"(h-9�kf!�Pd(YC�E�te2��-�!�W}�@\���\�l�)A�4��򷏐'����H]�b#B��T'�:hV��"}��z$0��bm�g&�$ą/�9�l�t%fۇEU�5
�Rp}b4�&Y�e`��
F�F�C�&gI��q����K���$� <�#f�.��}`5���5���u��!ˑX�Bg;��P��a1Xcݞ�;�:i��!�8:7J�xw5�r>u����o���1%�l�u�t"���8%C}_�	pG!DM}�z��Eo�F��b *�.TR�uC?��𙇻r4 4���RTl�����,�
�Fi��Δ�Pf4�h.HjkR���d����DT��sk.R 
}���63s�n7�����v`c��bӿ.<���QMUp%���b&>Xh�d3����S
qx��n:xcI�XY��bE��`*�)�U.c<�`�Q@���E'5��0��p�r@cP9X�dj�>1Na��n<��a#*)q*Ͽ'�q �b�i��F�-*�u½Iڹnip1�o�8͝�%JN�?P�<@qI6��{ �s@9���[�V���I$(8��7�Ӡ�}�QTJ-|Wm�N
^Gv)vgv(��{�1��7m�ې1x1�I�6!L\*'%�t������gft4N�wN3�5�]�)Q"Z�.�� ��� �1�j�^d�a�)r�9x!w#f�sP�S>��kb�@�w�7@$k�SH fu "�~�[/�F~�|uJQ1;`	�td: w-W�Vj�H^ n�BrC�(H(5"$l7�\@�3�`k*\��3l-,� �~d�Υ2��CnE�vep�{qO9�ul�^i��0�hh��<5�R[��G.��e��}�!4yElf[�QT[��#���;((�#Ƣn]~��<3{��+wyD�@�E�u�a�K	it�G��"[.�9kl'��(+H p�jf@ļ2ȸF	R�� �781kz�(��tQ�p�[��`:i>�x׈L��:��$cu��X�Bi�����7,j�mPEd=�Pc�p�<����ke�2R�X9�z*f@����WDg�4K)���l ��{u�s7p�v�E�N0X6���(3�>l��v�����s�(Y��|T
����Fw���v�)��C�H��3We2L%*o�"mA���ktD4!���!4��4m.�A�WҐI_���GP�]�,i�{����O��3��A<�s�t#O��!_jn����:�v�t�����nK{~ndN�y~�zG��j��*!#0�݀Z9�5S,r�����nλ�'�%��1d
���^p2��� �j��S�(��E�7�<����@$�nwԛ�~�{B�>��>E�O��U�^}��4��B!6y�i���Lo6�� �
��yC�-E��ZA|y�s_{[E�a��N>Y{Bc��{�Siyܲ���x�� �`�c���R�?����34E/i־��mv�E�N9 B��Ga��J^r�R6ጧtQY�q� D�d��z�O�D�m�n,�>W�}犕B��~i�&w�H}y#�h�d$�pY@J���XU�0jr�1g�;�r�\X�+���ee�1�� VB�)s��L�qR���I+�DC
ze��A��ǲqSWP'g�S;6�!�tT�b�A� *Ku ��������1�,f�C�K6k���*5���Fe�d�[zh��="#�"]l)df�E���Gn%dl��\����X=�)� ����l�,�z{~o53�f���^:��C4W?�Zt�Wl?��i�U�4�Nwg��ehc:0W$�)�bh���Pd�(*,x��5<'�Q�X���@����=n�;Rj�eMk��AS��O���e��.82B��^1[[X.0b2��ul���h�i�/�5.�W�m�+�V`1�f`0��L��,��1n)�h��BǢ&�Ӊ}wu8f9�1��,&6��]��Nn
ôQ4�Z�u���y���E5����`�B�n
�=��m��9��P�D��(\l�d:[�;�i��Ѵ}i�la> ����ft}�o���t7)��aP���"%9T=�p ��9�4�(��i'K�q�	��@1��
�c����y
Gʊm��ƗҠ�u1�v$/����>��fe��R7pLfj�&��(�C���v_��/(��o�P�O�Tv	��y�
�F�"W�p	jk��p,���X��d���g` 7sJ�Qi����a�%�����)P���`dC�#�a����#"��kK�c���`+�.�`&y<�#
�o,{����=|_z{2�}8d���R �?cO�iF'�Y����;�zS� ��h����p b2�^b1v� ����汬���X�Ro�	3�O
��{h�k�3Y����Vv��=2��0U;�_�`BgZ,K�hU�م\ voi�]@�g����P­cj:"�a�@o��r��@u;H�u{j�N�/gi��2y.7�� �Q��7*.� t��0h�c�6Ȩ�-i(� k��Q#��
L:'���cD�7�U�x�$�sЦ>4_2D�7��)`����r��Ϲ�H��#h���A��cV#�xRkf�Q!Y��yKg��}61 W)g��0i$3Am���"����!G7��d �>HaOa4�!O� �
�\��=@ 5�I�S���sHr5iEau|�#��r��=PT�G%�#6�*h�$�U�E�7��̚�Apȼ ���"�]�$��������{�p}GNMc�l��bH��|n�0�Q�v�0J`sk�X�hmћ��<b����5k��M6:y���  5��tE{%*�i-pb��,�8t �b��k��j��|7@���D�sUJ�NbmO�<�W��a,���p�p2�H�l  �uric�*bu9Ř���]�#� (�%bHGϒ�3Ȋb�Q@+!�a��)�tC�(������5a"a�2��`��c��x��;h��!��z�O;}�m �:������f����8X3�a�-�5�&t�S�P	�f03p�-94S���e1��3M�"˸���z22oF�a{�v���Z0A���6c{�=?���]t#�e:/<a�!��q-0�)4��ô�qt�!��t��/��ҖT140M�c(0QE�<w���d�_,rfB"M�n���])����5���ش�Or�fDS|!�p���'%'!�L�:<Wyn��E|$pD;nML�&�V)�Xf31�pd$Jz���'Tx6�d�c�i$���=3*\��8�DA 8Z��g�s�'�a��u��{[�m�<�K{~2,�+``�'b01vS"E^A��$��^u�o���/T�,OP�VL6w �<PN�t"�x.�{5
�,��0hxl�~�V�C��J�Sg|>]���B?�)?o�4_'P����d`he��*��`ji�3�#�Ɂ:}��( ���in�7}�ټ��,��R�4���@��e�	��RL�nsN�J�-
=&;��P����@!F}�/	b�{���K��Q0��isJ�Z�QFK'�`pkS���
L�@C��v�s���=F���dRX���b�	LC	r��q�7i���c���0@B(TBk]u�ѹ0���r�!�<q����1��I�~ 
�7 {\b��'.^�ǈ�%���q��{�:vp�|�/A�2��y-�ib��֋��:_�bf�NH0Ϙ��K���$�w��$���PaVB��O&��w��IFZ�=p��[
7(�;��3�nl�y�zG<�ZUQ�m]�b�U���0@p2��0-]�.�RgwC��a�@=4 �hEzQ��vwD�e��`)o�F

�∰�g$��>���}7�<0#F��d�o<��SC?�� `�ȖKb�b�Ȗzv�*6��Z=1�Npn�b�6�s�`-�GD>n�=9�<~2ARk(�Ċ�'�4 ���l�n�}�c}�+�PP�x=����,�ZjzT�A.�@97(o|oos[��b�t)'N��
��P%En�=�@�5��.�R`d}i;�|����
}xpTl�S�c�~3*�
���z0_�����l�f��7X�Eo�_���CW�إ@���[��L 3%=��c��*<
a"Ա�$��t��[`Lx'�3�
*���H�!*$�qe�X�qr���C؉đ��`�n��t@pD����E�-���k����!����c(#�G{@�	i�=��v5Y���
D4?�t��T�O��N1+f�4�g\����J
�?mu2}@��j�+4�'.t[��	0]v���`vp�ocfhXiG�\ (�73$�'2�3��:*�_h�i�} ��)&i1��Gx��Ny��@�[_����%�h��li�b(S:�#�U$��(2+p�a�x/d4ŗ.M,N������ ��o�T�K9f�ap�(u�2�\m�
�h�P9Q
dqi��4�9_Ԑ�f�� �ə
��a�{��b�eAv��,�|9��76.{Y�u�"��8{el�\ZV#ȣ� "XK6�b?���C4Q��E!�|�t�Q�R�x�a�^�d~:�u�?�u�u�>P�$)���@������`.P+bt�)@2�P�Ģv]]�[p�ϴR�UC!!�-}��7�;�A=�f�k+�f�vTh��Jj%�-���}�H��6���x�({��C�a !�r��w�"$���De�(}���`�L�7)4t4�̆
z����%�0�y)i@�5�;b��>kD1c"��u���GB6@��!�PY��u��J-��&����<��1��
yx����!���K��m�b�U�g
�mI�Ja���L��
�R�%u�z߇�Q������2
l�XHP�ҷ�+#a"���Gqe(_#V�5`�;�+B�@�
����#�.���0&�FJ��q��8x�7m,$�G6>�`J�g9x9lJ	�[0�*��#��os}r#h%!Nt)/�o�$y�5�>���Yؖ+�a6銰��d�Q� t&�<"�t0pt�I�M�b�6'4�F�C6)$*d1��"�@�&�(���{�=] ��"%�:�",x�P
�F�hq,z	A5��gW��!��A"t Q+���z*��4�ࡿEA�E�l�BU��J.ݧ$��1)A��gq�BuhI�Ue�)�0-"���!�00���	 ��	�_b�c�~L�p�%D�
Kw����Zn��yMA!V!=Ea#�dԟ:�z��2 3�/�!aly��8�B��ᄪ��M& ��F�VY�m�f�J�l*Opz��=�H��|�p1�dp��f%J��q�*���;@#N�2���_Q���]�Mi���i��zg�&W��`��"*�1�y�� 7����B�2)�2�C1gU�5ͽh+���G�(F* � 
�RAh4��ޒl��-w�?7Q\��37D�Y9��N��BtM�H����it`�"pCL��́��5�����8��})\W�'�þ%.*H�@�HB�0�1�-�_z9�Q�̂ͩ I �9S �{���RG�fq�$P�2LAB�1!b�Y�2.�q,K\bP,f.؜#~a�at#t&���<2�
� �#��"��c[شj��	DC�]�-)�����n,<py(!(��?�3�+b1�ꌂظ̔(���Ec[���c��!�'�_�g"�'f�(Z��{
"�'��8�ayPJm�*"#�!�c��=�l~˺z��ꥎ9�fI�0�$�-��R��&:��y*�!�'gÐO_#3����%�t��:�
������wH`j��zD]#��.�)�V,'���*�@��w%\D�0 HVs,��9e/J\����&&�|1!u�˼�+��S�$6F/hx�����3 $�
M���)�pI��-�l��g�n�C ���=Q�D����g4�	ʒ�`:�K��o�&�n�7a ��.��;W��,�v�LVx4����(EO'3����J �#�>F�"�Q�W�9��Y�;�< @��vY �T�,c8��($!�b�H�j7a.���P��OZP&8v�+@�M�M}�����b88;����a���4��@Q�'\`ܿ
�/`�H��m��e1c*J�3mVW���,�0z'�7ߒc#�Xe *�3ô+2�{��%#�d�J��-���l��4�nj3e�S��+;���!�|���1c\���8ׂùBi�8L+���g�*�.G���ù%)mICC�?�"EPa�rͭa8H� ��#g$$�Ƙ�y�ܕ@K/cZ"
3�-�K/�/��l6�$'N�aOԯ(x7^y��&�OE��][Z��+Wa
����)d��T�� �)��5Ty)"y��KR�
�W`�Oo�ʎԅ`�~��c��2g��(�ƶ��q�:f��8϶S���8Y��-H8��/���H�{kj�r�7빳d%�+jڸRm a�ԎR
L��yE,l 0�(�fj���y�>f�����y�4�.�[�������u2SңX>�x��£�&�N�U,'��U�� Ɣ6r�dH�
v=3��~9Mix�o�FT�A?2<k4�GKQ�2����XepnR�f��(��7#XW�s;]K����(ey�b�䲹u 
�rB�W�t�q�ߓ(`ql	?(�A�a������<��H���Mz�-,6#*��[j�&�~HG�=����`�S�z��)@�z��J9n��'n�@&N8��Q$��'��6��[�^���������g0<���k�hw�aP�UYoQ��1ى��Y��o�����uI�[��fІ6��xܵ�>] �d�o4��
z!#�F�5�k!�;�+���1���1*�g����G�hc6Г����'�WT <;���y�ڣ*�t�/���������0�����Kl����4�M"��l �4�lJ���P,Y���aE3/�J�!#�"�� �)��t�:*8��������M�c��d�k޻�R�e�]�����c^�8�(��W�?q�b�<F�*l�. ��=^��Ա���0o��	V$�!��$��AL�� �NL%�����dw*@�y�`�^���� ��A�%)�Y�PjA�Xn����#g|����6'�z�a�h�Iub+�ةƽ��(��2n��2�҄��c ��vik�u�6>�n+\k
��褡W�>
�N��8�i�TY��'K���F�&g��Y����������W�t�YWF p��M�r|q��$�3m$z"�@���;��P��ػ�
*L"�U9�6�G�Ptv	R�}��nybk��<��\z�C��-Z��F]�);+	�U�V�gi�7��8N��A�j�,(B�,�te*�b�e�f\� \��9R��H	�4��7ҧ���̓�����W&B-� �?�Ih �}��<	��r$����m�Fk&�f��?��l�$O�f����5#��ped4�.Y��dR`#�M`x
V�Bl��gC �ަ):`b���� 8�#�F�.��} u��,���w��'���@Qg��P��a3Fc�ބ[�:k��!�"7[�xw5�p>c>��
�o���;5�|�u�Kw"��;�C}W��	pG(��-Ej��ao�F���c *�/^r�}?���;�r� >��e�RTm��䦥,�	
!/W�wn@�� �rB�,H(�*Dl7�\BΑ7�g	K$J���3l=,� �~d���;��Gnu�ep�seO�um�^mC��1�jh��<%�J[��-�.��E���]�!4�E-[�Q��s���3ix�#²�]~��3����x��ЍE�}���O�yt�g��"[.�9
kt';���HPaJj�L��2��B���@�981i��,��dP���O��`:+>̟xӀ���{�$fu���BA���7�7,j��pEd��b�0��翝�iYw�3R��1��*If@���X��Eg�0K�9���l���{d�c3p�v��A�N�X6��Ӫ3�> ���b�����s�)�Y��4�T
À�����{	�v�
+�Ԃ
$[�c�a��ٴ�(+�;m'���Ə�Ê
�!B,�Y"�;e.!��۰�%!j���n?�Agҽ��#��u�29B�����:��L��!��Hu��E��]������z�"z��.�P������(���l)�Rި�4��1aQ�.?�I���b����y F��uɾ�p�%�<:�"���V�m�服7�=w
��U, 0g�{&fn�\
�ol{����4}\Z{2��d���^0��K�i@"�%P�����;�z.W�0�{�����t�"2�^r 1v�@����淬;��|�_-�	3�J���{,��k��]����R6L�u=2���U{�]� J'Z,��iU��
A�W5���[�U��DY�yN=ibl
�-U*��A1}�̻qKg ǿ]71 g)g��}@PRk/�c������h��@iZ�����&�U��K�:H(ߢ N� �	(�x��ђɪp�CqiFh�'MF���s��x�¶$\M�IR�%W�(d8���z�`E�&��Y��zD
�X9�8� 5�L�Q���gHrwkE�w��#��r��,�YGa�#4�jhX���E�7��Κ�Apȼ���"�]� ��Ϲ���"�[�Px�N	#�l�B
i?�l�C��a,��P�t2S�H�,@ �urI#�*jXuy�����c��-�bJ���3Ȉb�Y+!�e����pQ��(����5c�!�2t�`��cۮp��+h���g��~z�N3��m �z������f�����8X63֡�e�1�f4�K�r�vt28�-9�R���EQ.�L�3M�"�����Z22NF�d{�w���[Aݾ�6s{�<�7��y|#�ez/<s�!���q-8�k���C��q|�!��p9�?��ҖD144]�c(2QU�<��2ģd�R_,rbB O���߷�]+���5�����Oz�fTS\!�x���$!�L�2>�so��""y17�8iB�&�F*�Z_0���gBz���x4�d�b�Ix��?�(����T�`Z��g�s�/�)M��m���F����5��/F��[?,�[D��aa�� �
�Ns�k����/T�$ЌQV7��PN�u�</�k:�-�q�4h�d�~�TuE��RùP��X���ĆsF
sޅ���h���y���~~��3���R?L�^2�2
�)�=&:�x8���	��!���/	{��70���-x7��Ҁ�!�3h�)�v��nm�a�R����PA��w�34��F%���4C����|gm�fkz_���D��	��זo!�"$�N�u�ѹ�W0�:�0�<q����3��I��	 ƕIIՑ�%���/,h�E[izdpR��F(�B^v?�+
�g^ȝǈY%���q��k��:60��-p��$����y=)~�������:�bV�N!I0Ϲ��K��0�7��$���!
B�
��2�s������u��L[6,#٠1�Nd�{��W��^�S�m]�R�Wې�4�:��"oI9���gwC쁋c�A-t`�m	M~Q��V7��e�8@)k�N
���v��$��>���}�<0#Fкd�k8��SC���AA+ȖKs���ɞzr��*P���=13�,0.�P�7�?�`�FD��.K�=�?~6��o/�D��'�4 ����>�ݝcm�+�PP�<9����
�?)�|���
t�#m�Od����$�F��p�t�:N�I�it�~�a(P�u�(�pA֩
%q	�[��ei޺�h��@U�S⼗�u���ސ�Ǻn8�J�~���t�ouۦ���
� �&��saL/�{��d甼C
���"�>��w!�闸2��P9B尽�r��:�(Ń�g��-�({Q�l�<
C54l�h�ۛp�[o�� 8d� C�fI�F ]�g�G�v��8�c���O��@T���DR~'d(�*^;� %���X-p�4�M�h>��DM��K��c�'��.�P�C������Q��-�޴�E8�!]T�$8� �1\���s5�t[���Usn߹b�P'���$�ƫ��0O^h(x:�(Dd\T ��:��!V5���`�����1����
1��gI�dD1�l���2E�Ts�)s`�ԅ��x}҂�0Ѱ�_zm�nʱmW<��4G��cԎ�
/=է�>�"��R=�"����}�F�enk,����ǘi��ʍ$t��Q1���Z�h��Ȣ('q	&���}�e|{�_-Y7�W�=/;v�o-��fs�9��^���C�,��"�1��������aZq�(��՘v�@8ʈ��IiK���.�1�*�*k�{n	�e0D�/91 �/pf���宔[zYSy|�Tl�Ӂ%-~3��
���{�W�����l�f��7J�T�V���H]h��M���X�� XM�z2%=��c��*<i����$t�[�SaLi2��M�`�3��S�ʇ�6tT�O�{̻��-E��NAy�Ն����X҅��:�W�y��ӵD���'�:��h��%�{�p('t~��=�5ǖ��x�4<6m?�*��@!����������bB���a�{�yT��/����'0�r3����cI|ibB؉đ��@� v@Pd����E�6���
�jδ��I����?Op�zJi�w��f4dY��E��X�E���` ̾�����ikm(�MTy�\���%��Z&�O�*�іO�m�����$��2Y�;�Z����A}v��h�_z0~�6&�=9yJ{�~EEȻ��k�7�}"�H�����\��+�X�
`�	���Gȃ�	N��`B_�'A����L�\i�T4���t�,"8���&�	K!����a���?0�ܼ��dq9���1Af��K+�kF�5� [��.�խ���=.qE�7-��c��v��8˧�{E3����,`MYb
YЬ�49�|��T�O��N�+v�$��N����J	�;�]�R@n���F���f�	�8"��,a�@���`c����[�Q�lЙt'�p��*��Lp/�)�6�e!ëmH~8&Riw���TWci��!����֠���K4�&?�Ah�;j�6[N�7{'-laDXT�ˋs�c�`�X~�>p���x]��5�s�!���(� �u$˥9gc��dO�";Cb(N]힉�_*�B)��?h�Gb&M_�����;�`�Lc��o�I0CVfo���α�uB,� �p����Am=��Ġ�\ @.������-��ub*;��[%I���"��J���lf)0��
آ��~�5�����|�o%/F�5p㝇cDh~Dj���+���*�bf��	@�o�E�QʛmgL���K�|�}��o0�<xHڍh|4�"��
ڤI�k�|\}!��-
���`i�?�qq7�ia����&Wn-n0@
��6�m4v��M8����o2o��s���wW#�.�s�ck����#ޱS��'[Fa�Pqpb)�2�w�� w���P� ZZt�h4mn����%��1���F�y8�)G�H?�(D��KnZ�"�ؐ/*kE��.�Zo���h�-ߍ ��8��\�R��@�]2[���~�7o$<��$.+ Hj�\���V]^0�nzcGj�� j{e�R��2��|�J�c�"e*9ܔ90J�B�~�>iAE�hX���6P7��6+a�
����q�ł>�����L>`@E�`�q�b���Yca2�=&�Ōu�x�g̴Y]��R�T¨��x��T�u`A����S�LS��2�5௖����+ٞ6��7�DR��͜�^/���!f�b���q���bT�wlg�0��j��%jL�p \ef�f���%U
�,0�4��(��V���T��iJ�c*�����<���y��9�ʜ�,t[ے�Z�͕@��� �¼���Z��t�U-�C 4#t��D8�S:�����d��#Kx:=>��5mn�}��on����o�0�ի��P����AF)�s�č���e�P!䂪� �F��qr1�?ap; h2@�[��"Au���e	����^���:���a������G����T�z��F���9�v�|}�)fۤ�K���D�#���~N����_ƈ�p7��WO��Q���{ԑ,���-�b�Qh`>+�P��K=q�"�٭��<L�SQ��,�y�5���:A$Sw})u.��|�\�5H �g\`k�A�$�an������qeRBef�b	��b+�i�V����T�_�����W�=>O��^n�l��{f� ���%�����m�
�T0[O8qy8�b���ED�K. !�
�{��ٸ�u�:"�󪭂 a&�K�[x�%�Mt���C�Oʆ ���>v�j/�k�D�e�1:�j0�����=-�Ki�<�?�R�uL͇H�0�m4L��n%��2O-�!��$]={eG"�E���9�t��F�E��8� �z����x 	��tAzz���@$M+�"{v|HD/��6j�t�$�IXc��v>Y���7� 9�Ɨ����F�d��%<w��ɮΪ0�ߥ���{#�6��MT�| {ZH�o�_K���gdpg��r��$��A��K>�s��nE#y]N�P1l�vKW���!H��v
|�7emr|F�h4M�	
�(<��6I2 1�H��b�'�&�A��4*pq��)S.f�(���s�<Y�����:�h$x��p
:V��d��2.�1�{�� '��_��Cк�)�:�C�$�����h+M��a�N�s�abR��(�`X�#��+7�/4Y\��#3L�Y�=��N��@t	�(6���xu�� pC\����+�s5�����P��y�^S�">C�*#X�@��B�4�'1�L��z15�S�S��ͩ i �9S �{��RD�&q���"dAB�!b�I�2��-[X"@�2.؜aka�&at#v&ݩ-<b�
�*#܋jJ��kY��j���TG�_�)�U���n�=p}�5(��6�2��/h1�⌀ؼL�����IC_1����!�#�_��#�f�(z��{	G�|jȣC|<���h�ri��X#;�S��ʐXE�sV+gb�	ڥPB����ɱ�I�We��'����&X���X�a�Ļ�d_����� ��c��r�
*��&��5�aqP[M�*"3�-�0c�=��l~�8{����9�VI�0���-<���b:ہY
͡�'aÄo�#S��[����,�d�Ų��V5����`���ۤ��蟋E!�*��ˤR�6���OP��T��j }c����*��*Aq�K}._���	�Wm�a=$m��x8��H� =����W��Lzߏ��f�-���Zd�;U)�$,�Vu���,�[��E��%K�qR#c���2�;�_4�
r!8��"N�������:��b��i�uĚmW_��(lKõ�Q4�dEZ�I�B6��)X@�
@�j#�0ƪ��E�W�����:� @��nk T�,�8��*&���H�H�j�e���P��OZP&v�+C�
��KB��=,t�*�p�P��ŏO�����@�"���%��;�9Y�y	��k�ㆳ��@�w++!e��^�M��P�P�{��Ҕ���v`H��C��f~��n9��.�;� =nP-���gy~���h^S T�;�V�"�sR<�;�_����%�^��s*Rc�p��"�.�o����ɠok`o�b#c��:���r,M���8�a�'�<I�#v�/Ά��.-�A5<��(`�ESo	^p�%�>~,�F5�9--&�*/d�к&	�����yT%�5�t5�H[��ie����M���yܾ0�$���@)@g��~��������rΌ[:(nﺨ�j9s-�!�it�R4}�

��n��7s�uyuh`1�E�T��lM��K1D�4H6`Ͷ�+oDa��aڤh�M�"-���F!�pJ"��**�("-<]#ge��K���S�m
\��`ꐫY)
��o=M�#�&끢�2� T�f8
wVH�[fk���c��
v=3��:9M)0�/�^Q��`B�VԮ1��b],���+eT��P�`U�yw�w�3]K�����ei
�f�q 
�rb�W=�v}�^�(P@l	G��A�c��#����T��֍;?p�z�--'�C��`B�R6�G8(�%���a�IV�;���ӯ��H9N.��'n�`&nx#��$��mӿ�l�<�;鴿�v��8�g�����czicP�EZOq��G��
�FޖB��������� �J�͹kT����� �#L8�=�\$���}����XkhZ�Q�� Q�t��b0�2-�2�Hs���ѹm$�P��<#I��a�E�"m3�#���l�[n�
�	��'y�^A�q�w� [���N-�����݂@��'������ra�!�s�~m�R���&qAs ��"a�(�߂�6��`�(6ķ4s}Xw�c|到�GO�6�:���+E�l�R8��).{JS��e���:�ѨH��'���ҷ ��X%Y[�G�/�5BfnjO?�{�?�4cX�
h8�H�5YW�W�p�#)�uۃ�
T|���g��%յ4��� �<�5��u!��
Sg�����藨*�d�h7y�d��N"�_ Kr �BX����.���Q*����2�4�����)���$V���@ߘ��o�rHd�Hp�<��DA��ר�hB��S�yi[|"�>�$!52>H�C���?t��`�
c#��oc��ꧢV�F2:J�~3	k�PUb�'C���G�"cfv&��ܥ������7�a�QUF 𻕕|v|o��&��@AA�({&�81�|XI��W�.�>���7�Q�I��aN��͂a$��)*���n��
�9)��<ÿ0Fًg�q;��)�Y,HT	(L���w���&�Ȃ�B��޴�����zYШ�/��o���A�d,��WgBK��������r�1p0�j���'iR{Da�	{~�6�O`qI�	Pb}��Ny`���<��:]:^8��(Z�AXE�6��<P�2��P~`m�掚A@�N���XC��te:��-�!�w}� |���V�4�%	�$`�J����������F5B��S�I�.h �}��|���:l2���m�Fj&�&�/� �(�Wi�F���y�)�&P�Q.f�an�"*O*2f�i_�:�G5I�`D��
@B���,#���"dZ��YF���-���C��_'�@sJ*�1�a"��A�uQd��u�^� �A&)j�E0�DQ>Ė#�OH��=
)m�'�i%W���NwYf��8c���b�qW�~÷�R(�lf�Hn�����0F *��P7 y_��ц��!`ۃ!δ}9�����p�������)>H����-&�A���ha˦�X�~b�`�y ْ6�λ�����G��# ދDX֣?-��a;�i 	)���J3���d���|�3��'��ˡ8�?�+���D0�B �!��� �.�S�u����d�h��,^�A��J�dI�C&+�=���N�=��,NdA%K𻦨�����ge02f6�e^��@��I��~�D��T�����[^vL)PA����v��ܘ�i�E9���|��@5��E�'���(�}�~�eH�@h'$/!���=���1ew�pS(wJ�*0�<)D�_�y �$_(��ʺ<Y��?<���Iup�\�"�60Th�d3���\���
�༜Hh��t5A�XX��b��`��y(�Q.c<�P��H�`'�R`��p�r@kj)X�j��Oe���t�a�
tږ�z4q�TV�B�H�P
�?{	$n<�d��䠇ǽ�:ͽ�%JN�?PD�<B3Fu��; �s@9��Ъ���I(��'�Ӡ�k��WAdlX*Jl�G�x=�:J�M�%1��r����6�tk/H4��/��>���f$�'����b �H�(�}%�v>����ő?�@���eXXK�1}4��0(����(n�����,4�6P`�e��$�-��q4.�.�,Z��e�����`��ЀB�d��	Jo0�G-��ߙ��.�8��#�e�����K�1����R �z�G*n��W?��K�S?DIu��1����a�� �l�x�%6FtO0�$�$ xF6%fr(��
{����m���2h��H%�{�L\*ao�aD��:E[�ڱ�f�	3��]�)]r[���� ن� �1�n�\�a�+p�9x!w#f�s�Y>��#*���w�7@ek�SHf� "�	^�[/�B>�|u�Y0;5 	�8g
 ~-U�Wb H� n2C�(H(�"d,7�LBΑ1�g	KJ���sn?,�0�l$�Υz��GlU�_ez�{qO	7um�^mC��1��h���5e�HK�
i '{���X@`�j�L��3@�B���@�=81i��,��`P���I��`+>��xӀD�����5$vw��\�Ra���3��5,j��PEe��b�p4㿕�os�"R��1�Z:	f@���X}�Db�0K9���l���{d�c7p����A�N�P6��Ӫ3�> l��r�����s�(Y��|�T
À������	�w�
+s�B mL�ճW~%0^%(j��A����kTM$!���-����->�Q�W��	��W����2�_���SA����@��P�m"=�J��5\�l��{�~�\��
�7	�lndN�y^�j����+#�JJ9��Il�z�l�5yM>�e�̈́��Rՠ�5������2Vz�s�)H�P��k�Zޅ0'O��O1�W�8���n��t�j��Z�{�(��	��8�΀��<$�\�C|��n`�f	C�i 0��?"k�:(&,%�`g�z�y�戨O�(��3�%�Cc~p-oA�lW=�
g5�Q���=!B"�?�ˮ�td+)��	���$�+��IB�<�5}h>U	~-.]�!��-� d��ɔ��$O(}+�2Bn�o\7�� 2I���H�8K�f㖎 ���.b �r�\+cx�^
����E~���C�nD�!�o�Ó�$,�MA�A92�i�6ף5�G3���R��6
|e��A����]�(#S�g�S;�)��T�f�� "B� �����
���1�,F�sG�2c��**7���F��&��[rh��=�#�n,�jf �e�q�5`d��^����X=�-� ����m�,�:i~oOu3�F���^x�C4S�Zp�S`;�i�u�4�.VcZ�#�(3;0 �)�bhh���Pd��,x�5���X���@����=j��rj�eM+��AS��O���ͨ�"�"R��~1K.0b2��ux.��h�h�!�Kv�e�F@��Vc��&X0`���(��1|��(��Fǧ��m!�fW<n9�1��,FV��]K���OJS*V�۰	>�._�`(�jNP�j
,[��a��ٽ��+�0?Z���k����6Ë%�=|9(�5�\�Pk�e�_���
$@����boZ`'F��q�	�[@1��3�
�g	����hy/O��6m� ̠�e4�O,h,���>��T�e��7rLd`�{6��(�C0�z
�B���r��b�e�{3����.�n-D���dkn`��W7N/ �0�8�\��;��+ȹ�,���55l �;�[�2�5Id�)$@�0Q�D/��c!?u�:�(ؽ
�`�0?�+o+5Ldo7I�!Fl�[$6�l. ��߳�$%j��	�jF7�Eeҿ��#����"8B��������D��!��H5��`��r]��Ϯ����Z#"z��.�0���4#��(����9�:��4��aP�.?�I��}`���4�y`F��uɾ�pϧ�<z��#�ÈV�-�服5��=�
��U,�8e�{"fv�T
�/l����4}|Z{2�s�d���N ��K�i@#�
��;�x.W� �x©����t�""�^r1v� ����淬���T��_o�	7�O���s,��k�3]���R6L��=2��U;�_� B'Z,O�iU��� v�i�]B����x�ʬc*>L"�a�@oc�`�� u;I��ij!&�7$���)?2Y/'��� qP��7b,�|��4x�f�2����+ih��/���#��}w.B<�NH�ܺM�p�z�u����R��O�:*q����ҭu�Q��Y�X��}?70'�</!�#�M�p֕�
�nd%���p7I>�4&�K�Ѳ*v�
�	y���
�(H�p�7��56�B�_!8�$������Q!M�λqkf ��| W)g�155M�I����"��E�!7E��@�6��au;ddߢ(^ �<A,��y��њ��9q�Kq)&h�"�f���q��x��4N�]�IV�$�\�(d��
i�l�S�;`,���P�t20�H�,  �erIcd*jXuy�ؓ��]�c�K��bH�ϒ3Ȉb�Y +!�e����pS��(������a�a�2��`��cٮ8��+h���é�~z�O3��m �:�%�����f�����8X73֡�d�1Qft�C�r�vt38�-1�R���Eq.�N�#M�"���P�k22nf�d{�v���[I���6s+�<��'�}|#�Er/p�!���q-8�)����4�Q
<�1���p��>��҂T104]�c("QU�<��Ƌd�B_.2bB"M���5�Y)����5�����COz�fT[\)�x/��!�Lm*4�qo��$*x8B+����,�+�V;�X]1��e�Jz���?Tx4�d�c�I���5�(����� yZ��'�S�/�)$��mؠ���9� �44�H�za!(�U(i�fcy�Zpj��*�Ns�k����/T�lOЌQM)�@�P���'e ��k 
�-�)�4h|`�>�U����RۡQ�>"�a�����Ġ�?$�X��+#$ps)�{��T���S��#�Ɂ8��A퀠��)g�3�}�˸��=֥:G�6-��LPοj�Z�P�RL�~ʲ��)�8&:��,���
)��=��lЀ��ud��ԇ���[L�PG��w�2���9f$���0CY�`��r��1�1��fe���0B�T
�=]������p�r]f1�-q�͐�3���H�� �ƕM	���%���?�h�E[h2c[dpR��-
B^G8&�Qh�F 3�%c�=ya��r�z�vj���k�/`�54�y����{��f^�ǈ�%���q��k5�:60��,T	�����y=�i~�������8_�bV�Nh0͙��C��� �'��$���!B���2��s`������q��[7#٠2�Nl�{��U�^�SbmQ�C�W���r$2�n"mI(.�Rgwc큋c�A=�!�hMzQ��v7��g�@-o�N

�B���v�?�$�,�>���}>�$0#GQ�d�k8��SC��Q�d�H�Ks���ɞjv��*P���=1�0*��7�1�`�DL�o�=�=<:Abh'�Ă�'�4 ��,�~�ٝc=�+�`P�|-���i
�?)�|����ܪ��/e�3-���贪��F�- ;w��.���E�\|He�15_&��0`���u0�~������4�j�p���O��)���xb�xhw�5m ��x=�!l�Od�gÅ �F��q�p䇮9�L�$Ow@/aPl8P�d�*�Hyר��&s
Igޠ�v�D�g�w�x�L-�1ⴼCR��"�>��t!�鷺9:��U8B尽��.��;� ų�g��U�({Q�l�<��f%6��+"u�0���N�9. ��:AgCUGs�п�S:u׫�Ylo;q"ʳ��>b ���l|I}���T[q�Ù�R�3U�~��,
� �g7�=%`��B�`i��"�6J=k��)ho�Gq�C�JA��0�E�> �cz�����b�$�+� qU�C85�6�j�����L��8�3���.�8���3�ʸ/I���A�"낑�`�������B+�����$�&Q�/@F�uZ8e�h�d���#�M�d#�l�8�B f`[�M���$+�L'j�)G�3S����$��,=B�23EL��Z� ��y�Ҩ����.������:��pd1rc�(�l#��
C5<n�8�"ۛp�[o͟ 8d^ C�dJ�F ]����t��_0�c���_���P��dDP~'d��*^9�%���Z/`���M�h>x�DE��K��o�f#�,�p�S������Q��-5̴�8�!]p�&8� �1T���S5�t[���Uan޹b�pe���d��A�0O�H(z:�(d$\V ��2��1V5���b���Ϊ�1����"1��gI�dA3�,���0�Tc�(s`�ԅ��x}�0A�_zi�n�lG<��4���aԏ}
?=է�~�6��B<�����m���dn{,�9��͘h3�ʍ�d)�A3(	���z�`�ʢ(Q���}�e|{�O-'�W�5o3V�o-��dwr�1�^���A�,��"��9���Ĩ���O�aZq�(
K�{n
���{�W�����l�r��7Z� �C�ƭC^�.���Y��!M�2S$=��g��*<`"���$|󎙛Y�[`Li"�Q�-�`�3��Ӕ���2lD�O{��\��$�S�}�r�, ��\�
���`Mb
I���4?�4��T�O��oy;v�"$��L����J,��]�R@n(��F��f�	�8p��l!�@�ڸ`s������lЙto��h��L�p/�)c��m!ëm@z8&R,i7��$TWk㳻��������z��4�&� �`�3b�2q��7{#|!DxT�ˏs2c�`�X~�>�x��(]��5��)!��q�"�Pd�%9ga��dO�;Cb(NY���_8b)C�h�G'"&
a�f�W{1v_�}�#!Pc���i!l�4a��d� ��5K����� ѷ���;������F��D���
]4$��4 sz[�|�!��:dmn�_JJeײƠ01[RW�(rÃ�09��J �}�V�WO��,v�1�	�4U``�b.�x�w/R���N>'o��E� ���\��ތ�"Sl!2�s�\�*���*YNQh�� ��"c�70�;�1��x�!�;z�$�([z����7�3���q�P�������N���p���u+<���_�t���J'�ge�1gO<�F�$+�3AG�ɕP�K��
 REb#�K/��%U�2��	A�%�5�q"�����83�g�rWJ-=�i�>>h*6����^��TW@�5�+bj�DH6S��q���D����-}���u�UJ,��4&mA���9��5�4Y�Xp�mƊM,�-0&4��(@�V�k��T��)B�+6���=Dqo���̩���� j��Rd� x=+�����������t�U-�C 4#T��P8�:?�	����d��SKxFD
*��w}i=.��|�T�!H �dTbc�A�$�an������qeSBu4�bI����)�y�V���9�T�n"ˌΓ��Q=>O�ŽX[n~l��{of� �L�ޥ�%�����l�
!T0_Oxa{z�⠾�TDf�K, !�.@s��߼�u�:#�s뭒 `�I�[x�%�Lt���C�O�)��F&>v6j'�kD�D�d�):�j0�����=-�K��|�;�R�u\݃Y�8�m|L���n'��"O-T��%M}{�S"�U���9�$��F�E�/8�B�zL����p$���A:L~���!H"�/>�ɶT/��4�u��$H\G��V?]���7�5=�0+����F�m��%,g����κ0"�ۭ��]�{;�6ig��I��[�P�r��V0�b.z8H�����-8����A���k�e]s�l"yUN�P=l�v�7���!���}�2�N�8[1�C�,�͢Y�@5��>;�Ǟn��'��+�a'	O=|��:-�<p�`~.���$#���p78Z�4���������Q�0��R�����68����H���R�%t�Z݅�S�������YlQ�B��d��A��˺ �(+(�����6�=x*�*�">(�+�y5f�����#&�*�͵6&�F
��qi��>�^r$�fW6�pc`�Q|\mh��-����#�E�jq=z3yac?})/�m�$��5�>��P�Yؖ�k�y6Ɋ����l�S�q4&_�>3��[at�H.~l�4n`)��A�)4"d1��#�S�f�8���s�<��� $�8� ,x��p	��6���&���I��3�и�RĻ�@��9���U�����ih��#0U��9��)9곊�N�;B)nn)V--zC0�>>QS|�"���b'K<��ԣp!�/>��j�D%7-e�hH�$"@t�|�7�TL<��kƀ�sU��w�X)TY!�/���jr?�Ń7��N&	� $�N*!�E
��lp,?�~c�gW��S���``+���x*��4����I�Ӟ1�
���+(����HV2-$\A�i�t%Ur������! �1����(��!�N�����N�@Ɂe<�f�Mۏ���:X*#'j����6<�QY��Y,'��fd�,P��n�s>�-Q�6Z�Z�7"qd89�1�8A�@rD��'�,����h�&{~��?�rw��Z����M6AYV=Eq��d'֟e�$>�2 ����%any{�Q�Y\(cR^d�k>0-�\���DO/!�(�T[�^P:��=jH��|�p0�tp��f(K�l�:���`<N����_4���i�;j�
$�)���rf�>V��r��3*�1�{��07���A�B�)�;�C�$W��ͼh*]�b�N�q�8d�9Pȫ(��X��H�/7�?$Y\��'3L�Y�y��N��@d
́�'EÔ�#�������d�Ų��F5����@`����/�蟋!�:��C�R�6���OP��DX��� mb�����+�q*Ay�m�_��@Wo�gu&m��|8��H� = ���u��	s���
�d�`j,����dp��u1p�r�X #%�R8$�h.��6�v#J_]8�n��&�D�m�%���"N���������b��m�}Úi?W_��(,c�5��$�dEX�I�B6��(X@:�tD��dX(�D$�ȵR�`��1�U8�8z��� W�R^��<z�,FjM�A�}6�B!����p�_tkoq�� ��	`��l)�U��������*`���pd�4���1�*T؁�@���y�k'��u�T�$��=)�����)�8I��
 RZF�4"�QW�9��ٕ;�  ��~i �T�,�8��(&�%��X�H�j7e.���p,�MZP&v�+A/�M<�l�G����88;����M�'�4��PQ�X@ܾ�/`�X��m��e1c*^�3mRW���,�0hG7�7ߒC+�Y4� k�;R�/��k��%!�d�j��i���m��T�ln3e����{{���7�|���3G"��ׂ�B�,D+���c�
�*C����� )mI5CC�?�"EPd�s�-a�H� �D"'"$����Y�ܔRK/kZ#���M:�$�of5U�5�r���X�@p���T��s�F+|J*;�A�H��,g]`�2��4#�	Zfƥg�x���C�g#M68��n�J�1+�q��)I�p1
���zd(��r�r�_��0�i�2 w�޷�sj-���[�A�������UY�$�0�0u��(9��BU��  ��ex���Z�zRGrd�'Q����@7�G�mGZ��b(��5`�8;l(�$�����V�ܩ�8?�D�of��Z�id������*�80���

���V =�H�Ñ[!�C�Z4�r?��a�;�y%�w���&C�,������c��O�h=�@ �ŷ����<im�m��k�o�{�}:Dr���ht��d>�&�.���5$���<��r`:��J3�-�K�/Іd6�''O�!_���(8r9�s&�OE0��YoZ�
�}*Wa;����9�?�i��H�c� j!h�@�١H+����d�(ƠE'A���2q� �t00���甛��l/Gߑ6�<R�gi�t�h ��$b�e��1r��%��{����y��5��_@[g����a�a� ��A BL!a����#z�f��J����)d��D�$ �)��5Ty_�!"{��I0�
�� "�6�2a�H` ���\��LV͐_�J!B�4i>bͶ��*#����h�M�"%�$�f!;�p, ��G?+O� 2-�M"Rp@���	�Ap0g*�]=�bꐫY**���=O�"5& ���4"^�4tA�&��,�5%�u��)�C&0t"6��*��}�lm��{��:vj�e�L*�@J%�7B�����P�{��GK��
wVH�Ifk���c�������@D�e%i��KbUèI+�M��q< `m�,QC*~�t�CF�� x	q,S�l5�2I](>�'-�	-;���+��f�L]As�=F�XФ��Q}��l"�(�,0f_�0k,L�����1-���6���HB�K�����o�������,�20�p��iYħ�Հ�16r%[s�kw����Xj�'��������=D��A.�x�".�\M�j��N��ML�F�� ;H�i!���h��L�������i�?C��"(��f� $C�b� xc���]�J���5m��&MŦ?Uz��7����
��{A,m 8��fj��
{#S~&A��w�ԦO�5p�.�Z�ĺ��� ,#�ӣ\>�x��£�&�@�M%��E�8� Ƅ&r G��
v}3��~)E(x�oCF��g!�u��{8�Gd3%���2� 1=��/���T8w�;]C���o��ey �f��2�r � r@�S=�+f�^�(P`\	O��A�c���/���Dv��Η><�E�
d�<l��4�i �!x �6.l���P,����a`3+�B�!��"�`�m���T�*J	���@c$2 p�n�p��(d�cλ�s�e�}��-��#^�8���W�q�@�<F�.h�& ��|Z+�ճ��0m�� �$�%�� �k`Lr�@�M��Py��bs*@�)�4`�(��� ��@�%(�S�pnM �n6���g~G2���
6g�z�a��Iw"+�ت�?��
���2n��60�����c"Րfyk�u��>�fX>j)�h�
��	'lNo8PR����bi �X�i�;�Բ�^g�b!!n�u$̀*)rs!z�9�v`�s(�H� ������n�t� v-�(�`�l<G�U,f7r} ����v)��/��Q(����0��#����)�#ԭ 5V橥:@ߘ��g�0J`�HP�>��DA����xB�GS`YaJt"�<.$!%>H�B�"?d�G c�>S� OL΃j��W�D"J��k�Ty"�'J���F�fbb��n����������7�CaF"𻅕�:|k"�"�$� ���c��Rې��7���:gf)_�����R�7�Q�ɿ�
aj��͉k%��!*���n{T�8!��ïn)&�g+�q(���1y4PU�5���3�\��Id�a��+p�e�5�)
���n}�r�2M(���%��$�Q�wCK�s�������|�v!pP#�e�� ,
uPr �Y?)��2�&OP�)vq@�U��ny@k�Pb�2�^;��߀-J� �`5 �ݩ`���x2E4yx�'�d�6�L�[��B�G�de(��%�!�w}� \%�96��Xz�Y�(7���+��#़��F9*1-� �?�I� �=Â|	��2$���bi�Gj&�&�/�8�m�d_�f��U�!�`mb+�&Y�`rW��
�o}��;%�l�q�
7"%��)!CyW��	pC(AL-Uj��Uo�Fs��b "�/\R�5C?l䉆��24 .��a�rUm��d�%$�	
�Fi������Qf4~`&HJKRŀ� Ꮱ@DTY��sk.�!
=��I	�6�3c�.w�����⃦bӾ$i��QMUp��� 460h�d3���\Ǽ�*���q)8kQ'��UX%�cu��X���Q.#<�P��H�
��Oe���<��!� |ڳ��?Fk6�b#U��=�x�Fd>B�c�n�h*�:��%JN��PT24@1Fw��: �s@9�s��ԯ���K(8��cī��o���go TJl�^���F��$k�!4(F1tVFkXv�p��#+���1�ө�o$'����b &�H�(2�p+�k�p��QM����Aks��΋3N^C�1}4�I (�"��(j������.R�'P`�qe�$�-O;`1F�h$
�ղ1'h��jKW%��.}E��1Jo0�G��ޑ��.����eK����&�Hah��~b��+D�y^��/�4�D:�ԒB��[���U�Hrx�%"FtO �&�<@XW'!Tgs*�B{�m��m%ːlk�$�6�MA	q5%��4�F�������f��wf�	2�$�]�)�"���� ٖ��71�n�z�a�)�8x!w>#f�#�H>/�k���w�7@e i�CHfe &�	^�[/�B>�<wY�yq+	@<a
 v/W�Wj�H nrB�,X(�"d,6�B�1�cI
ct#{j��h a�jvD��2��B	��� �?81i��,��`@�����`+:ĞhӀ��2��$wu��\�BA㽌6�',h��PE$�� b�0�8����kYs�3r�P1��*f@���X}Wb:0K�9�u�l��k$�g>`���A�N�X2��Ө3�6 ���r�����s�)[��|T
À�F��{�v�
)s�BaoK��3W?$2N$(k���A����k�\$!���)�,�4o~Q�WҐ	�E�G��_��"�P��� �B�Y���@u�P�l"%�O��eU+:�㗥{�~�t����k�{~l$J�y^�z����+#0��`JXp��Qt�|m�
��te/,y��a��9�ĉg4�2%a�#+R�)H�T����_R�0'O��D;�S�(��n7���j�aS�X��E�35�<��Ҁ� 
�*�0|��;͹�d ;׼R ^H�I*���&�X��hMs2*�(o"��
\�9ž��	!T�p������~aۧ	��� � �.ib�8��mhU	z-*=ma��T-� d�>Ȕ���.t+�;Bo�<'��g"32��!L�E��0▎ �{�~bA�Z�(+a"h�^����E>���C�nD#�!Fm�Ó�$4 `�MA�A=2Xdi�4��5�Ws����B��&M��X3����i)�61f�,hJ0)�Q�&04!+@!����!�o��09b+q�j$�b#CG�?�:�j֊����g� %H�	x"��� W(,��n�t�nnb-n�5��}G�-e��t9�3_{[�>ᰎ��>�2"q��;�Say��!��8��"���*�@�Ң7D����31�.)�>��m6�D��9* Bb�E)��JTb�RጇUAY�u� L�0I�z�O�D�m�&�>WŹ=⊕B��~I�&s�X<x#�h�d&�pY@Z���XU�0�r�1g�;�rÜ X[*䐊�%g��� VB�)S��LQR����I
*��
ze��*�C�]� sw�g�S?4�),�T�f�� *� 
��������1�,f�Cg�6c���*6���Fm�$p�[rl��?"+�&\d%ffvG��+?� d��\����X�)� ���Io�$�jy>o1�8F���^8� 437�Zp�Sh?�i�Q�4�.vc��#�(#:?0 �-�bh(���Pd�	:-y�5�IQ��XdĀD�@��?j��Rj�eLk��AS��O#��r͠�:�R��^1.4B2��dx�c�h�I�,�Rs.u���	��2b��d`a��L�(��5~��h��Fǲ���W�fU:n9�1�	<F6Ղ]���oJ
�ڰQ/�Y22�%v�Om�4v!��hQ����!`�eQ���
Ɲ�N�%���jY�9��h%< �9���TO�j@��%
\k�3��p�4�h� ;5 ��6��l1%vC�$�PP�W1(��� ;7��=�"҄�AxUư�"G��q�	�S@0���
$c$����|-5( ��&k������_W�z/�u��/��d(�g��S7zLvb�i6��;*�Y2��$��'��U�N_,;;
g ~:B,oE�,P�di�&$�7ܣ�d���e`s,juYi���r��KZ��q��A0���t��Y�����H"�6�[�{q���c�.�`"y<�a�'8
�iH�,_��5el �;l 2�4d�idI�:p|/�S!>e�:Ũ��
�$ �s;*�+=Ado47I
!�!B,�Z"��d&!���1�5"5j���nF>�Acҝ��'����29B�(��Һ��M��!���hu��E��r]��������X�"{��.�z��6�-�(ȍ��9���4��`A�.?�k�X�}b���(�i`N��uɾ�pϧ�<:�G�x�V�-�朅5�=�
l�]$�:e�z&&~�\
�+,{��>��6}}TZ{0��t���^ ��o�i@'�
yN%a<l	BU�M���a��P���`UO)ǘ��+b0t"�)>k�T^����D'�}��������xހ1?��� �)�ip�o���@��b����ZN����!RT�e�}"�k��s��>$��2�&���nU��`�j�i��5,�����}�A��xM�d�f_�B .%�ai��kR1��c*Aa#Ag��3Lb2�Q�Π��\��3I����p��ѩRg(+L��`��og��@�����ab�y#pG��f���sL�M���	h�Nu�*f��:�F"6��!�0�Xj��b!����[u�;�?��� %9��f���CIq)1Dv�g��q�D��))RD�w �'�Jh�,���a�6�`����qP��2����� � ��薑��1�|�{D�c` �s���x:��)n�"�0�C,�7yQ2��%�zQ�
j�T��Xic�-� �e�xoL�%�8��[-����P9�d��oD�Hh0�nK#5e����uz�f8$i��%.!&�|uT�O�e #!��ukVD	4��8������5a�a�2��`��g۬xĩ+h���ǩ�~z�N3�a �:�F����f�����Y63���}�1�fu�K�R	�vt30�-1�R�ف%Q.3N67M�"����{2nf�e{�r���[I���vs{�<�6�(y|#�ez/<S� ���s-0�)0��C�"�Qh'�!���q��?�	�҆D1$0Y�c(rQU�<����`(JO,2 B"o���޵�])����5���X��Or�wTS\)�x��
oJ�����N��m	��'�4�nr��.�D�cm��Nn�.	"�nL��\<$��i5��(�I�X��C=�+l�"�$��H�]N�:R䥵��4f�PnBx5{5w�7��c�¥4������ ?uf�?����^�>q��!�<\�� $/6"s�"�yL��H��b�-�c�n�Pi�w�u���P~��x[{�.��DYt�3�I�52�W�D=yP`��9i +��"HQN�uX�.|���m�I�,�L���H0�2$($�$T�@d&iv��p�alݾ�|H��x���b��Fs|���5_Z�iM�b��I�p�ce�S@~�J|�Po1 B���l�I�����̔7d���Y�h�	�H����b��)RXQa`�L�)o
{n��m� ���?�R`�=) |����
���"�.�v ���y1��T9B堼�폢�4� ���W��,�({q�l�8
��4g&��*�fI���_�<���DX�&ǿzpz# @	`�h�6/�1$=jp2�� �>`�}�lhA}���\�s��M|��Bl6'��B(�+�G�k-'�V�rc��#�, J�k��)`}�;&�0�3�W�W�w�K|��@ V+�y�!��_�P�l�� ^l0z��J1�ט��l��
�F��ݵwH���7�w�m+����d�n��S�Cġ�+����pY,��1o	�^}'s"p�)"@t*`�K�D���$ot�$�\ՊRv@p{+�|��	$<z"{*
$=qn�;K���]>
��u�4)6|��m�Q0)�5D�#����X*� �����)���Rf]�*(�Y�;=t���l�A�kk�� HacqTHy1�j=���n�a-��"����qA��f��7�c|Ae��i0V;F���k7^���c�E
�R>a|F�l��S��H
���j�����l&v��7Z�����s]����U�+�X��4t�#3�~��c��*<i�ѱ� t�u��]�[#RLi"�Q 
a�X̪�fЁ�	r��Cf�k^x|��Va�fan�k�|e��j+"6O�!���,sc�޹�Q���(�����FO���Xa���~�{u'y�/6��#��g�}��>@i�*��W���n�&�{�֫
t-(*�>�#��tt"i:y��uZ�?��f�e��nX�;$5��x1Ě���| .�*�,�z`Z�15i�v�(�U�r�]��Bq�y�g�OK���=(���^��L�h��̋x�D�	�
�Cc�!S	 *-@G犐bpR؃���Ԃ��$ʐ���c�2R�
K8��$q%-$l$3���U�V�p/w\�>h0�%e-��'*�B��>ɧNJ�bE��Y]��(�bO8n!7�<H�zuWz'	�i�zH)t$m=�c�o���z)c觐gb��Vl0 �▊�~�9 gEB������lM])����sf-�iG����
%���)�
X
&�{)4x?~�q�6��-xoa �lKPrϮ��41Co�8dق��;��M �i��
L4A�%�5�q �����83�gmrRJ-��i�<|h*6��^��VU@�=�/bcA�FL&S��}��RK���.~B��x�UJ,��
!T0O8qaz�⨖�UDf�k. !�*Hs�����1�*#��=���0e�I�_x%�wM$���C�Oʆ)��G'>v7j/�kD�D�e�9:�j0����--�K��lQ?�R�ud�	�9�ltL��o'��2O-���%\9{�]"�U���/�$^��V�E��8�R��H_���p$	)��ezj\�#�mG7�1=�l�\ T+	��6S�M����[C��r{]��n6�g9�+l����E�I��%�w����{J�R03�۩��%#[;�&�'�N4��򰎷*	��~�e8���u0��0�B�oK�lϕ����	�O~�]s�nND xUN�P9l)v�'p��`@��i�uJ�NT8[1�C�.I׍�@��Aq9���;��=���̨:��>�]����:��	e����K<�ߨ�$c��P�p�8Z�C4�������0��P�M5�oy�! ��?��^�I �QtX�Ȩ}X�O�$d�Z߆�"������>~��wm[�q�$,4�蘽HV�	i�ס���/Pf	G�8BHC�\IR�4�ȟ�2�����$:��9ܘH�1 */-5�n.�E S�69
Lzpr��yfPs4���s�.OM
bhdRN^7h:Y�fJ� �F��$�y�l�I3�]*��5�()�L/�c��[1��+"��=�{�/�K�  �>�t��D1L�-�qV����v�f���4�8�J�v*`=��!,\
���f䍇br��׃�u�j�)2��DV'�lg�5	��g'.ǭ��|�t=�b31:9c	�x?`wV@�q�v��K}^�Φ�0�L��Y�dF1�Zi$��*5.rY<�R��f`��v2p~f��Z�o�y����^\���!��{B,*9�' �l[�f(��#S���Du�����m?�Ub�
$6oph@�;`��(�rl4�D1�Ѱ >Q��b(m5�;�U�_q�W�a��';��%� �i2{е�\:���]��wa�
6��3M���.�jc�%b
+TF�E�)�Z��n�<py�u(��>�7	#�/j1�뎂Y�̞����HC_8u����!�#�W��#�'f�(z��{
*o'\;}�`q@YM�#&3�%w�g��9��l~˺��Ŏ��VI�0���-|��f:�%Y
̡�ge��N�#S��[����,"t�Ų�T�F5����`���%�{��Oi�:&��$R
���Rd*� U{����Rs-��,�[�囬RvD��r!q����3��t�ow])���R����r�t�`�:��I��Z�@v�0���"�Q�`Zq�C�J1JH�WHaz� JD�%��.�)}''��*�@��w��S�t��xVw<��Eaeoh�����t���$� Le����o�\�h^���v�<9y��C�e����/:�n~KtqcNDy���Gl�?�	-��:b�ozzy��%U�����̴-b��z%���4^|e`TM$Y%8�uO:��nA�tJ�khk{O}�hy���R�#��] 
Q�E���ֶ g4��ڄCg`:I2�e�&n�wc ��*��#V��,����V|4����IEO3����
 �Z#�4ƪ��Q3�9��Y�;� @��^y �T|,�8�,&�%q�Y�H�j7e��P�-OZP&v�+C�)�M=������89{����Mᥣ�4��PP�Xb���-`�X��m��u5k:N�3mPW���,�0jg7�7��C+�]� n�t;�0.��{��%!�d�j��m���o����O.3F����{{���1�|���G\���ׂ�A��<Nk�%�v��..G����� )mHCG�=�bEPe�rϭa�H�(��#g&$����Q�ܥPO/k^����M:�$�oF5U0r��xZ@`���Ԯ�s=�@+|H�V>�4�K�&p`�2��5!�1[f�Df�|���C�g#M4:�n�Nm]��w��!O�`!
��u�z`<��r�B����"�i�:nNd���k;*1ff�z�`:��lOm5nrGz!�0v��,9��BQ�� ��dr��!��/{V_XD1&Q���"�PO��æ�GO �
(v��-z`5�^ll٦��B���x�18���8->gx���E�d�m��<r�\��g*,�rv(9cI�Ɲp})يy�wpi���!4�5b�a/!j��tѬ&��.�����c���;j=�P��k頷<m| d@�uq��"���[��K��`j�C��mE-'�)��du$+g.<�cTX��J3-�[.2/��!4�'EO'al�ƨzx^O�&�f4��:[J���.r!D3����T{�:�i��@�/� b!h�@���Hj���v^��hCF}OF'Ľ[��c� �upA�z�'Ɨf�d�W�ϛ$@,�<wi�t�z!��5r��8�pr��!���b���y��]��]A'��T��c�e���K jL!I�Հ��'x�f�XJ����)f��T�$�k���Tz_�A"��KP(�-� �����$#9GN��m,,QUl����p�0��L:��DV�W�~�nEue�V�mdz�
l�t�h����W`gH�bA�)��39�� �@(!hk;��hĤ���ړ ��%Sf���i-�|z�M2	n����w���9�c�J5��2n�~q:��&C�@�1���v6�eͽ�H�r��z�$H�tB�6�c)�_���<�P���a��ѩ�����K�RQ2�f�x�F�Z�l��@�{O�oR�5[��ţtfS�e@)|_S�%��I"��Qu��aIQON{��{��<}�5&Ňv��Q1���+����>Ӷ�@V}6]HCJ |��:G誃bZGֻp&w���OF���t�*<qK�X��Q�O�����m�*�;6��9�9�i	��k�c⣮�H�s"+hm��F�P�I�kg�7@�J���)��1 �N(�yP"�HȲ�[�0=n@=��wy��~1�#�N�S Tۻ��*lR<�y�m�"v�7%�V��p*I'�h�&w~�����p/q�׫����/llaL��v�s�g�#p4x��\N&"<)X�#v�=��c?-�Q'5���� Q�S0k!J&�f+v~c/�Vrҹ-�T& �,��U�&	���4�yT%�5�4�[�|�ҍ�6StR�B��� ��C��sq�)���<fI���0%5v/�H:F*����8k9s�	b�xi<���OF�GkI�1��.X Z�E���k6�"$e�{`1���T��l͈�3R�<)>`ʹ�++�!�%��h�M�#-�&�f%�Q, ��{.o�Ar-<_#Rq@�Z���Ap0f*�
Y��@lлY+��k=6#�&� �> ^�0|cCf0��,��	�,u��-�G�
�d�L*�dN �wB������@��Z��"DK������D�_��X{n���2
q\ `m�,C*~t�AF� 	q,S�L7ǋ2FiM(�/�&%�)�����&�v�#L\Ac�=N�PЧ��Su��l�,�,0�_�0k$L��º��-��6���XB�K5����oɡ��f��,�p��p��i�Yƽ����92z'�{�bu��Ђj�/�8����y�=���A.�z�#�l_�j��\��\�W���;I�i������L�Ԧ%�!��C�2�r���pN$C�b�Sxg���]nJ襕5��]Ŧ=(��7#���	H��{Cll ;��dbס��;�.`C�br�p�Lu�q#�Z�������/2S
�\>�|�����&�L3]%��U�e ��6r���*v-r��:9O(y�'�F�g�V+��Y��=֡(�^v�u�I�yS7�y�9�ÑW�6]C��n��ey
�f�l
� rb�R=�/ �в)Pxl9O��9�g��'����<��̍-�A�-)4�ms�O�e!s�f�[xw=�PAI�'�<�L&2MI-L.D�g.�BFnz*��Qd��N���<�9I���w��#���4�J���#zhv�ah�EZ/Q�h1ѩ�gY��1�&��j��:�K�4vЉ=�m�W�۾�9|<�ˤn����E9k�|ѕ�bb�zЅ�1��\�1*-�g����F��:cV!�y��&�3tp<�����"�t�./���������0����Kl�<��4�M$�h �4�lN���@.ɕ��a� /J�c] �(�)��J{c:J8�)wr�'����M�c��0l�cǩ�s�d�]���=�b�8� ]�W�s� �<f�*h�/(��|X+W԰���m�	�$�%�� �+alr�X�_l���{�"cwjD�y�`�[*,�� ���$)�Y�tzEpn6=��#g|O�(���
6g�X�ay�"�iwbo�\�F������r.��6���a"ՁtyJJp��?�

X+
V�w��6�'C$fi�)nlni`r�R�$� =�s�bAn�y t��$4���w��7ȕ^W�@Sg�A�p*�a3�Fkg���Z�:k��'9:&[�|w5�r>'���
�o}�a?5|�p�
v2'��Y%W}Vq�	pCh�Mm�j#�`o* sA�g2h�*^R�?�h􉆻�r4<��gRT~�����m�=��3&�@Q���Ү�ǟ������:}�
s��+�;+��%�4NM燱x�FB�r�@ "������%����a��Uc�vv�9/0�W�!K;n #��g�(�)S���E���>�k�T�����Pk�uC�e��0��9N��~,�S���:�����	�/���=&s�c`Y�X���}���UnleK%���M�ͷ��NG{@F6E9�H0Ğ0�,�_�n��<���y[�:[hA$5��LQ�q���jfq� q���Qh�>ε`|�X~6ӊ�Vi�Y�´�Qnt�h.@JSRE��p����DtX�}sk/�!
|�ȉ	>�1s{�.w�����c��bӿ<��QmUqe���"�>\p�d3���|ǽ�
?�)Քt Y��eD:bY��b|��`���)R.cx�P�QH���E'���㠨0�r1PH9X�lje��Oe���<��i+(dڳ� *Y�ah��ေ@Y���m���h�@
����!JN��PU�=@3Fw��0�sA9�����do�H(8��'����)��R�gl 
d�F�{d���C�+���|�� 7db}yh�;	t������Bt�$�'��3��b!'<5H�8<q'�\,��?�ʅ�?�A���3XX��1}���0(�2��(o�%���,r�*#pe@���W�/'/Oy�@���e��Q�]��5��Pi.��S���swI�v=�����0�$�o���`X����e��=\��aH�$�6��b~Ҋd�h5rd�"��O?��zu&ye�N�/p Y~FY�7�#&0d(*g`ID��~@��#[��[��<�yh0Y!,pSuIǒ���2&�k��k�8��d��Dw���a���4��X�p2�e�\�IRC2WK�G $�
��	^�$�C�AmG1e$�"~d!HG �$f�a�v^�k[>t	M{\)�HH8 Gf�y[`�h �#:4Rr�	,$t6�(d���G3r=x��s�G[��Fr���ɻ�e�1Qr�DW{�AYq�l]g�s
L$rLݓKm��~�˹oA2�I!F�1��Sl�a7!%#��pe�h2�(n2�(��?�e��CC~p-�A'o"=�*G5q�^޷\(["�?�����t)+a��)��$� ���YR�<��}h>'�~
cֈ�E�$�{f{�K p6G�>2;�j�����g��H�)z �����W(,��n�q�lo�-o�5+��x��mE��t=�q{[�>a���6Y:s��j�SI�ܰ�>�}��c���*�@R�#E���cu4�')ҿ�mv����O9 @r�G!��HTr�RጇtQI�p� N� i�z�O���m�n5�.wŹ}���b��~	�&w�X<y#���t&�pQ @Rk�FXU԰jr*1S;�b��P�;Đ�75f�1�� NR�T)[���QZ���I+*�C~e豀��²�Sw�g�Q?�)�TF�f�� "K� ��÷�)���1�,f�C��6c�M�jw���F�lf��[rh��=�+��d-jO�re��.�`d��\����x�)�"���Ie�,�za~gO1��f���^x�C4[3�Zp�sh;��i�u�4x.vcۅa(3:8 �-�bhh���Pd�(:<y�%'IĖH�Ą ���?k��Pj�eMk��AS��O'���o��*�"B��}K[*$B��Zux���h�H�j�EzKE�R����Tb��fbpY�ɔ�g�1Z~(�(��FǢ���׉i2U0m=��$V6��]O�%�NnSJ^z��%'�Yf5�-Z��l��@$�H�~.I��Y��qrőh���N�e��_hky^�9��m��y��P�uO�+���e
%[�S�a��дm��;?"-��s�^}��ˋ1�ǿ-:�S|���
��"����!5˱7��T�6!�awfp-�FO� l.M,��}��Q�ɪ1p�x)vz�"�sϩ�q����+ ml=�gy�R�+�Hlb��'�q�O$Ӿ2i���i/�3�l߄<p1'*:�~n��tiiO3&q�g\���%E��)#DY'a�&�Kl�?�b�h�E�&�੶rb�2�����%�4�������0�kK_Kg}�j��v�.���|�p�g�T-�-e-�`�He�d���\����qC��*CH��@�no�+a;��`$,AY�L+]f+���~W?gIĵ\�F����ZH��2
Oz�vD[|!�h/��!�L�28gao��$j43JV�
C"d�V)�@V12��e'J��+?Tx4�f�c�!����'_ ����DV� �Z��e�s�/�)��%ب��,3)�;5��s&G
�@�C`��(�A7V@g"�KaY��Ns�k���OT5,AЬU]&w�PN���'tUv�uJ�-�8�6�h�~�U�Y��[�q��Y�e���a�����DBVfX�� `�.��1���e�&�#�Ɂx���� ���o%�1�o\A���-ԧE�76��PԺ�]�A�sL�n2
��J뭪}&;��,���i��  }|��rs���ڻ���)JT��`;Q5�#ƹ.H�}("��ELnV�_LlQC��~�30��9F%���tB���`��H#r��o�m���es���8 B	@A!`����7��!b� �=�q����2�t�I�f" *q��/q��q����K�S�j,dKriJ �#�71h`+'�RXf-GD��ߙ��G��T3�P�E�>ܬp�H�+t�1'�@�h�%6U[�8�[v~���1��1�'��#A�Q�s�M 4�"$��:��M�]f�$r�陥�q�Bd�`t|2
o�.��s�ħ C������ (9'd�($�`.�\@�i�q�lD��$m7&=-*��K�{T�%C��o+*�{�a�Ch�v�z���Dt�W��4Oy���b!aз#T�qs��D1sSh��G,�WQ�{` y�`/'��&�J�2�<j8�4���+:�Tw�)Vo��nnmo��K�di���$��I���h��Pr4���=�n	�4��^�x�)!��b�Ii�P9'<*��|�Z�
c������Ki:��B
���c�>�w!L��y��T8B������#D�#ų�g��%�`{Q�l�>
��25r��2�$3���^�rc�f���}1`L0I�>��`���`i�u1r�(-#�ӌ�>A`�y�|hA~oА\�s��K\�m�M�v��BJ� rO��$'��b��i����* (�k��)FhN�ge�[�j�{�4�E�8 ]�q2eo� �MwK��8�vGǉ0�!
ұ���l	4�`��*��L�#2�!T��WĤ*�?N���:���s$Pqc(�l0�%JA54v��h�*?�5g��:5�tC�dK1�*F ].gԧ�t��~q�k���O6��(��$DP0��L�80V
j�:`,|q���G��Qx��ӵt���g�z�� )��.�kL�p('vn��*�s�Bǖ@�x~|6e�*��
�-&�#Ix?�Z�����yT!|�_~0|�6f�=yJLk�>EQȳ��i7�4 �H�����\�+�X�
a�I>ϼ��X��	V��dB?�'+A�꤀���"\j�D4�)t26m28)��&�	[!����`�� -<�\���lq}���0@f���K;�{F�%�7 ْ�>��%��=q�7'.t�c��vf�8��zM
 pT��f2j�l��cd娓�J�ep��xo]n�krV$�,j�ˍ��:GKX�lWY7�u%b�KY+��6�m�O��T^v��J�op*�4˂���e��������{�h1u���;��K�<Dy��B�bP��tOqPM�}�#�oe�8a�b��Y�:�@�T��{3��=r\�z)��jV�E����ADb�ox�5`/��Ji��AFp?'��~u'�$>sh���9��\-q$F/�(;�rr�8 �6H�f��>'h�5U��`�%^%i����#HA����<D����oԱ��>���Mg���ug��J��*Y鏃}tLw�sb��@qk�E�q�ae\���*�ty�u��*0�=Jt�n�M��O\T ��'uݷiȖ +Gqm�f{�F�j8�v��"A�k�|\w%�p--�&D�m�i)44���`i�7�Q}2Typ��O���h�mJ��8si�b�]Ef�\M^y}]��}2|�%Uw�QH�	�7�m4vP�
��h4|~��b%i�7���N�x��)C`Ch_4(E��J~Z�'�ؠ&(i28`��,�zo���i�-߽��8��|�R���Y"[���~8�'o$=5�"&* Hk\���V]^1�ozfWl�� kke�R����2��|�J�c�"e�=^�98H�B��6iAM�h����6H7�ز#a�
��lq��>ş���L?` E�`�q�bx��Yaq2�=�Ōt�8�cL�A]��T�t��(��|[�T�Gluq�ü�W�A��2�9�����+؞�!�㣅6dR3?�͜�/ϧ�!f�r�� q��bbT+�
�eb0� j��AjH{x$ed�f�<%U
�.0�6Y��8��V�k��T��iJ�cS6��9K���>.gs-�1�
��CrU��Cu�{Հ,���,�b���>*MP��/-�����Y%�=2��"�8�G���8�%wip.��%|,\�9�$�gU`c�@� �an�/����qeR@ud�"A�4�O#�i���m�@�n­���WQ�a�o �n �L*e}nf� �X3���R%"��:��lH	0����E�?��"���r�1brA;wa���R���
������7
�dR,����=�I�4wxZ���H:S���$��RÅ�c�����sOc��g6B� c�./6����PQ�,������=C#�P�u��YR� �ȍ�:�k���W"Hz��D:��:Y�2T,
$�w0�C,J�3!�pz#;�cg�(!�2�k�%[ScteU/6�&T�I�1���*�!���R/�Zi��6�0`�@$�s��4(��})#�� �\�i�9�K� !�w�p��[le��*8S�H�8���M�h���s�8�
`$ıl�2�n�$u�x>^���" ��W�^[��zl��07�qp�8�gaROG�i�0-�����}1|_@��0��#�b�:t��3f謢rb)*aԌng�%�V*�#3�,�aV�av�ivd8�SCF���l�ۆ���b+&����f�f���w� 1n�(�g�<@s1q�Ec;��2x�F5J��b5�]<�r��3
�F�mt,:�~��y"�c�W���bbS+���x*�6�`��D[E�˞eFb�"�Qw��m+�"[��/2S�-h�����H�p-Qs���"��!H
5����
	��	�Z���µ8A�P��-!uMӇ��6b#SX0+?z��6�hO��Q2La��r8QH��L2;d8�I�Mdhn�\}s~FDP�xd89�0�8�`zF��+�r��*��h�&i~/�alw���-��Yl�amWi=�q#�dv�g�x��:8�.n�8/y��@C�r����3m5�{�`��h��U�� Dj8D5�z��4jH��|p9�tP>�f*[���>�d�`bF����_ U�l��v�8jm�
�Z���y��
>���.��2+�1�{��07��`�J�8�;�>�1$W��Gͬh+]�a�/n�q� e�uAL�(�`i�F	�+7 �ɦO��&3F�X�y��N�qBd)��6�:�uu��"aAL����#��1�����0��a�\W�&�ä"#\�D��0+1�D��:1�Ag���٩ I �9W �{��RD�.q�aP�S"gA@�1ac�Yf2r�q7KH"P�4,X� �a�ad#YT݉-<"/
�*�#��cʸ�z_Z�j��TG�]�)��X�mn�<ry-4(��>�3m�I+h1�뎀ڼΞ,���KcZ8����!�'�_�k#�f�h^��{
*'&88�aqP[M*"3�'�0c�+�=��lvʺ{�������vIP0��m<��":��y
H��7eB�o�#s�[��5"t�Ų��V%�I��`��%���G��zv�ˤr�6:.��P��dH��z }c�����/�c*Ay�I
}��|�u�� i���>j�:�C���Zg)�8iz�$l�Rq�8�*�I�皬sEvOã3"a����;b�P�	]	��oFvҊ��b���p;��~���:�%��|���vlI&�d��*cjvvhazܠ[D�'��*�9�7'��7+�@��w��U\�$xV�(��E/
��XF�t���< u}��\��]�k_7�H Z�З����0bj��ڦX`9l�O"ʉ�y���g l8]�	-��RS�nz�yW�cP��㩛��\%)gcɈ:`���s�<D`Ew�%@+vM::���B�(�kp�j��h�%
50�;���[�9`���i#�Ja˰�R�;����;�u t��u	Cp�pfY=W "5��8z� X�5�}2S#t_i0m5�0�&��#�o
b%Lcn��b���-٬��ԗb��iR}Ę)?w_�F|lKഁ�<�tEz�I �� %�)XA:`-vT�
�Z#�u¦��U�w������#�| @��VY"�t�(�8�� $�0b�H�b7e��p�>MZP&v�+C�
:\~�VVjgW6s� !Jyg�8B�b
���
5A�%-�rY�������H3�fmrWJ-����>h"�����^�UAqx������IqlS���L���[$Oط�buo|bT��5��>�W�"lI���2a֭��4��`p��Z���*�=4Hd�����w�kqT��	�cb�>�ԧ=ʌ�^�z����ѓ��u5�/��>�]
w?Ap�<�k^�~�E;J15��ո���(��ܻL����g���[|B<II/��y&}Ǉ��r-�pGn��gz�� �F�n!�z�ZK�%w_��ߦ �Ч���F��`p�? r; (�3p�[���E�}�a
/;�]Z�B[�}���7
���A0s�Y�� 7T_D8qZ�b㰋.En�k!k�^�{����eM8������� a�TK�U�g�O��i�`Z?'�V�o�1��|�K�0�l9D���3�����뱡�Y��J�|h�����'\��g�g��|��~`i�3O
(����Y����EB)�W�dI���g�N�{$�j.�w�HGgMثd�b����2J�����m�M�L�I!
@����&��b���uq.C)"M�q�wȥ�2���
���籢�CW�7fr?.�)�Z7o�N����⨥�2&������.��>8&'Zm�&�&4<�tAj�Y�=JR�I��T�K���"�U�V�r=L

�d�S��4b�=:
ɶ �v�d��JV���Kި���{���@�|(�����~X�����;��lh��z
�5t�L^��]!|[���#�ɞ"Kf¸v��2o��rj)[+�0���d?��v�'�Z@A�`-��mY�9�\�w�f����M�����7oyE���H��1]%g���b[Ņ0��{g�!{�F^5Z#�"�9|j2��qA��(��5�м������,q��Ej�h�I���3��%����L
���t0A��*��;��j�$��^j�x����4�
Wa�X���aFЄ{�/&~�L@�r��я
V�l8�2��|,�6��^�ǲ�MrtS+�u�x+��T����Ku��eb�d�C���f���\��Z��F�ɾ�_�MY�i�x�sb����9��Q���������u�������@��1<��
�9�')��A�Ƃa�;��ڢ�q��XV&�$��>
\f��Z����H�qP�a9aa�C�#����: c?}e�o��[��Ta���������<զc���*�(i�o�k_Q2��?
���<�w����xߔ�h�}�<�^#�mH+n��ީ_q���=�:�M��%򩻦�8�����r��5� �O��26s�1�RX��(�2�A�<���e�h#-��y��q�)u�Za��a�@L�#b��u�w&Yn�5܀}�h��B�UsT/�WH�獾}Q�p�<�����9q;�����r�^C�/&.�#jH@�B0>%�1����z  �s�SΊݫ@ �wA�	��VD�s���#`�B�%"�{�X���<_X"t�&'НX�c����29b�����z���.�!���Hp�iY��j[��������)�V]��n�8�pi5)�
�ۍ)�+^��|���!P�%�^x��uZ�����jD\� ���_p���6~,V��{�B�-����|:���HU��}��xg���@X�\A"�l��{��	;���zc"���!�V�SV�h<����̛�|2^����� ��{��r��
(��'��=�!��UH�"��'��`���tf��+����h��%�0_�����~z7
^�L��`
mwx���D5P��s���x��r!4����	u<�FxD�U��q�Kp��`"r���5#�	f��G�h���B�'+����OF� ���c�+M�d!��
g����sV:�g
s�-�K�F?�v��w	�aT��N�������O���P�^��o��Wa�1I�<�� y�=�m�����b5-LI�D�ٱl�*��B�sN��.BªG źۑy�0�w�@���������.�Λ�o�>B�w-�u��9��Kd���G>�9������t��Zy�8T��g��Z-���a}�4�	 ڊ��SC�fgjL�����)F���  7�� ���x])*o�8K[
Ȉ��؊�]%s	9E^�5,;Y���t�x��֑��T�l�� �����$<ҵ)eI�����tK�ob���.}uw�\<8��M������pN0@�.�|������*���i>�0bNQ�KʫX�W�37 �C��F X63��@Y�^x��n�*�@��m�SxMh���g��1!��H��nG�-��~2��?njМ��!?&���ԭ�zyy�
j�d&ce5m���	Jr��>aJ]��	��g�g���+�a��HkFۚj2�#q&u�ʼ����)L�!'������YQ�O��6���$����c���`�|�FM���c�DV���d�2���-O�lfQLqt`s��e>`BiP��)[~�jy]�Ik�ssT�#u���5œ%!���8�ff#@�\����c�af��D[ ���"r�����G5�LXD��zdO
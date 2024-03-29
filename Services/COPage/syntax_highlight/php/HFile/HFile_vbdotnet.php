<?php

$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_vbdotnet extends HFile{
   function HFile_vbdotnet(){
     $this->HFile();
     
/*************************************/
// Beautifier Highlighting Configuration File 
// VB.NET
/*************************************/
// Flags

$this->nocase            	= "1";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "gray", "purple", "gray", "brown", "blue", "purple", "brown");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array("Public Sub", "Private Sub", "Sub", "Public Class", "Private Class", "Public Module", "Try");
$this->unindent          	= array("End Sub", "End Class", "End Module", "End Try");

// String characters and delimiters

$this->stringchars       	= array("\"");
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", "	", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("'");
$this->blockcommenton    	= array("");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"3DDKSHADOW" => "1", 
			"3DHIGHLIGHT" => "1", 
			"3DLIGHT" => "1", 
			"ABORT" => "1", 
			"ABORTRETRYIGNORE" => "1", 
			"ACTIVEBORDER" => "1", 
			"ACTIVETITLEBAR" => "1", 
			"ALIAS" => "1", 
			"APPLICATIONMODAL" => "1", 
			"APPLICATIONWORKSPACE" => "1", 
			"ARCHIVE" => "1", 
			"BACK" => "1", 
			"BINARYCOMPARE" => "1", 
			"BLACK" => "1", 
			"BLUE" => "1", 
			"BUTTONFACE" => "1", 
			"BUTTONSHADOW" => "1", 
			"BUTTONTEXT" => "1", 
			"CANCEL" => "1", 
			"CDROM" => "1", 
			"CR" => "1", 
			"CRITICAL" => "1", 
			"CRLF" => "1", 
			"CYAN" => "1", 
			"DEFAULT" => "1", 
			"DEFAULTBUTTON1" => "1", 
			"DEFAULTBUTTON2" => "1", 
			"DEFAULTBUTTON3" => "1", 
			"DESKTOP" => "1", 
			"DIRECTORY" => "1", 
			"EXCLAMATION" => "1", 
			"FALSE" => "1", 
			"FIXED" => "1", 
			"FORAPPENDING" => "1", 
			"FORMFEED" => "1", 
			"FORREADING" => "1", 
			"FORWRITING" => "1", 
			"FROMUNICODE" => "1", 
			"GRAYTEXT" => "1", 
			"GREEN" => "1", 
			"HIDDEN" => "1", 
			"HIDE" => "1", 
			"HIGHLIGHT" => "1", 
			"HIGHLIGHTTEXT" => "1", 
			"HIRAGANA" => "1", 
			"IGNORE" => "1", 
			"INACTIVEBORDER" => "1", 
			"INACTIVECAPTIONTEXT" => "1", 
			"INACTIVETITLEBAR" => "1", 
			"INFOBACKGROUND" => "1", 
			"INFORMATION" => "1", 
			"INFOTEXT" => "1", 
			"KATAKANA" => "1", 
			"LF" => "1", 
			"LOWERCASE" => "1", 
			"MAGENTA" => "1", 
			"MAXIMIZEDFOCUS" => "1", 
			"MENUBAR" => "1", 
			"MENUTEXT" => "1", 
			"METHOD" => "1", 
			"MINIMIZEDFOCUS" => "1", 
			"MINIMIZEDNOFOCUS" => "1", 
			"MSGBOXRIGHT" => "1", 
			"MSGBOXRTLREADING" => "1", 
			"MSGBOXSETFOREGROUND" => "1", 
			"NARROW" => "1", 
			"NEWLINE" => "1", 
			"NO" => "1", 
			"NORMAL" => "1", 
			"NORMALFOCUS" => "1", 
			"NORMALNOFOCUS" => "1", 
			"NULLSTRING" => "1", 
			"OBJECTERROR" => "1", 
			"OK" => "1", 
			"OKCANCEL" => "1", 
			"OKONLY" => "1", 
			"PROPERCASE" => "1", 
			"QUESTION" => "1", 
			"RAMDISK" => "1", 
			"READONLY" => "1", 
			"RED" => "1", 
			"REMOTE" => "1", 
			"REMOVABLE" => "1", 
			"RETRY" => "1", 
			"RETRYCANCEL" => "1", 
			"SCROLLBARS" => "1", 
			"SYSTEMFOLDER" => "1", 
			"SYSTEMMODAL" => "1", 
			"TAB" => "7", 
			"TEMPORARYFOLDER" => "1", 
			"TEXTCOMPARE" => "1", 
			"TITLEBARTEXT" => "1", 
			"TRUE" => "1", 
			"UNICODE" => "1", 
			"UNKNOWN" => "1", 
			"UPPERCASE" => "1", 
			"VERTICALTAB" => "1", 
			"VOLUME" => "1", 
			"WHITE" => "1", 
			"WIDE" => "1", 
			"WIN16" => "1", 
			"WIN32" => "1", 
			"WINDOWBACKGROUND" => "1", 
			"WINDOWFRAME" => "1", 
			"WINDOWSFOLDER" => "1", 
			"WINDOWTEXT" => "1", 
			"YELLOW" => "1", 
			"YES" => "1", 
			"YESNO" => "1", 
			"YESNOCANCEL" => "1", 
			"BOOLEAN" => "2", 
			"BYTE" => "2", 
			"DATE" => "2", 
			"DECIMIAL" => "2", 
			"DOUBLE" => "2", 
			"INTEGER" => "2", 
			"LONG" => "2", 
			"OBJECT" => "2", 
			"SINGLE" => "2", 
			"STRING" => "2", 
			"As" => "3", 
			"ADDHANDLER" => "3", 
			"ASSEMBLY" => "3", 
			"AUTO" => "3", 
			"Binary" => "3", 
			"ByRef" => "3", 
			"ByVal" => "3", 
			"BEGINEPILOGUE" => "3", 
			"Else" => "3", 
			"Empty" => "3", 
			"Error" => "3", 
			"ENDPROLOGUE" => "3", 
			"EXTERNALSOURCE" => "3", 
			"ENVIRON" => "3", 
			"For" => "3", 
			"Friend" => "3", 
			"GET" => "3", 
			"HANDLES" => "3", 
			"Input" => "3", 
			"Is" => "3", 
			"Len" => "3", 
			"Lock" => "3", 
			"Me" => "3", 
			"Mid" => "3", 
			"MUSTINHERIT" => "3", 
			"MYBASE" => "3", 
			"MYCLASS" => "3", 
			"New" => "3", 
			"Next" => "3", 
			"Nothing" => "3", 
			"Null" => "3", 
			"NOTINHERITABLE" => "3", 
			"NOTOVERRIDABLE" => "3", 
			"OFF" => "3", 
			"On" => "3", 
			"Option" => "3", 
			"Optional" => "3", 
			"OVERRIDABLE" => "3", 
			"ParamArray" => "3", 
			"Print" => "3", 
			"Private" => "3", 
			"Property" => "3", 
			"Public" => "3", 
			"Resume" => "3", 
			"Seek" => "3", 
			"Static" => "3", 
			"Step" => "3", 
			"String" => "3", 
			"SHELL" => "3", 
			"SENDKEYS" => "3", 
			"SET" => "3", 
			"Then" => "3", 
			"Time" => "3", 
			"To" => "3", 
			"THROW" => "3", 
			"WithEvents" => "3", 
			"COLLECTION" => "4", 
			"DEBUG" => "4", 
			"DICTIONARY" => "4", 
			"DRIVE" => "4", 
			"DRIVES" => "4", 
			"ERR" => "4", 
			"FILE" => "4", 
			"FILES" => "4", 
			"FILESYSTEMOBJECT" => "4", 
			"FOLDER" => "4", 
			"FOLDERS" => "4", 
			"TEXTSTREAM" => "4", 
			"&" => "5", 
			"&=" => "5", 
			"*" => "5", 
			"*=" => "5", 
			"+" => "5", 
			"+=" => "5", 
			"-" => "5", 
			"-=" => "5", 
			"//" => "5", 
			"/" => "5", 
			"/=" => "5", 
			"=" => "5", 
			"\\" => "5", 
			"\\=" => "5", 
			"^" => "5", 
			"^=" => "5", 
			"ADDRESSOF" => "5", 
			"AND" => "5", 
			"BITAND" => "5", 
			"BITNOT" => "5", 
			"BITOR" => "5", 
			"BITXOR" => "5", 
			"GETTYPE" => "5", 
			"LIKE" => "5", 
			"MOD" => "5", 
			"NOT" => "5", 
			"OR" => "5", 
			"XOR" => "5", 
			"APPACTIVATE" => "6", 
			"BEEP" => "6", 
			"CALL" => "6", 
			"CHDIR" => "6", 
			"CHDRIVE" => "6", 
			"CLASS" => "6", 
			"CASE" => "6", 
			"CATCH" => "6", 
			"DECLARE" => "6", 
			"DELEGATE" => "6", 
			"DELETESETTING" => "6", 
			"DIM" => "6", 
			"DO" => "6", 
			"DOEVENTS" => "6", 
			"END" => "6", 
			"ENUM" => "6", 
			"EVENT" => "6", 
			"EXIT" => "6", 
			"EACH" => "6", 
			"FUNCTION" => "6", 
			"FINALLY" => "6", 
			"IF" => "6", 
			"IMPORTS" => "6", 
			"INHERITS" => "6", 
			"INTERFACE" => "6", 
			"IMPLEMENTS" => "6", 
			"KILL" => "6", 
			"LOOP" => "6", 
			"MIDB" => "7", 
			"MODULE" => "6",
			"NAMESPACE" => "6", 
			"OPEN" => "6", 
			"PUT" => "6", 
			"RAISEEVENT" => "6", 
			"RANDOMIZE" => "6", 
			"REDIM" => "6", 
			"REM" => "6", 
			"RESET" => "6", 
			"SAVESETTING" => "6", 
			"SELECT" => "6", 
			"SETATTR" => "6", 
			"STOP" => "6", 
			"SUB" => "6", 
			"SYNCLOCK" => "6", 
			"STRUCTURE" => "6", 
			"SHADOWS" => "6", 
			"SWITCH" => "6", 
			"TIMEOFDAY" => "7", 
			"TODAY" => "7", 
			"TRY" => "6", 
			"WIDTH" => "6", 
			"WITH" => "6", 
			"WRITE" => "6", 
			"WHILE" => "6", 
			"ABS" => "7", 
			"ARRAY" => "7", 
			"ASC" => "7", 
			"ASCB" => "7", 
			"ASCW" => "7", 
			"CALLBYNAME" => "7", 
			"CBOOL" => "7", 
			"CBYTE" => "7", 
			"CCHAR" => "7", 
			"CCHR" => "7", 
			"CDATE" => "7", 
			"CDBL" => "7", 
			"CDEC" => "7", 
			"CHOOSE" => "7", 
			"CHR" => "7", 
			"CHR$" => "7", 
			"CHRB" => "7", 
			"CHRB$" => "7", 
			"CHRW" => "7", 
			"CINT" => "7", 
			"CLNG" => "7", 
			"CLNG8" => "7", 
			"CLOSE" => "7", 
			"COBJ" => "7", 
			"COMMAND" => "7", 
			"COMMAND$" => "7", 
			"CONVERSION" => "7", 
			"COS" => "7", 
			"CREATEOBJECT" => "7", 
			"CSHORT" => "7", 
			"CSTR" => "7", 
			"CURDIR" => "7", 
			"CTYPE" => "7", 
			"CVDATE" => "7", 
			"DATEADD" => "7", 
			"DATEDIFF" => "7", 
			"DATEPART" => "7", 
			"DATESERIAL" => "7", 
			"DATEVALUE" => "7", 
			"DAY" => "7", 
			"DDB" => "7", 
			"DIR" => "7", 
			"DIR$" => "7", 
			"EOF" => "7", 
			"ERROR$" => "7", 
			"EXP" => "7", 
			"FILEATTR" => "7", 
			"FILECOPY" => "7", 
			"FILEDATATIME" => "7", 
			"FILELEN" => "7", 
			"FILTER" => "7", 
			"FIX" => "7", 
			"FORMAT" => "7", 
			"FORMAT$" => "7", 
			"FORMATCURRENCY" => "7", 
			"FORMATDATETIME" => "7", 
			"FORMATNUMBER" => "7", 
			"FORMATPERCENT" => "7", 
			"FREEFILE" => "7", 
			"FV" => "7", 
			"GETALLSETTINGS" => "7", 
			"GETATTRGETOBJECT" => "7", 
			"GETSETTING" => "7", 
			"HEX" => "7", 
			"HEX$" => "7", 
			"HOUR" => "7", 
			"IIF" => "7", 
			"IMESTATUS" => "7", 
			"INPUT$" => "7", 
			"INPUTB" => "7", 
			"INPUTB$" => "7", 
			"INPUTBOX" => "7", 
			"INSTR" => "7", 
			"INSTRB" => "7", 
			"INSTRREV" => "7", 
			"INT" => "7", 
			"IPMT" => "7", 
			"IRR" => "7", 
			"ISARRAY" => "7", 
			"ISDATE" => "7", 
			"ISEMPTY" => "7", 
			"ISERROR" => "7", 
			"ISNULL" => "7", 
			"ISNUMERIC" => "7", 
			"ISOBJECT" => "7", 
			"JOIN" => "7", 
			"LBOUND" => "7", 
			"LCASE" => "7", 
			"LCASE$" => "7", 
			"LEFT" => "7", 
			"LEFT$" => "7", 
			"LEFTB" => "7", 
			"LEFTB$" => "7", 
			"LENB" => "7", 
			"LINEINPUT" => "7", 
			"LOC" => "7", 
			"LOF" => "7", 
			"LOG" => "7", 
			"LTRIM" => "7", 
			"LTRIM$" => "7", 
			"MID$" => "7", 
			"MIDB$" => "7", 
			"MINUTE" => "7", 
			"MIRR" => "7", 
			"MKDIR" => "7", 
			"MONTH" => "7", 
			"MONTHNAME" => "7", 
			"MSGBOX" => "7", 
			"NOW" => "7", 
			"NPER" => "7", 
			"NPV" => "7", 
			"OCT" => "7", 
			"OCT$" => "7", 
			"PARTITION" => "7", 
			"PMT" => "7", 
			"PPMT" => "7", 
			"PV" => "7", 
			"RATE" => "7", 
			"REPLACE" => "7", 
			"RIGHT" => "7", 
			"RIGHT$" => "7", 
			"RIGHTB" => "7", 
			"RIGHTB$" => "7", 
			"RMDIR" => "7", 
			"RND" => "7", 
			"RTRIM" => "7", 
			"RTRIM$" => "7", 
			"SECOND" => "7", 
			"SIN" => "7", 
			"SLN" => "7", 
			"SPACE" => "7", 
			"SPACE$" => "7", 
			"SPC" => "7", 
			"SPLIT" => "7", 
			"STR" => "7", 
			"STR$" => "7", 
			"STRCOMP" => "7", 
			"STRCONV" => "7", 
			"STRING$" => "7", 
			"STRREVERSE" => "7", 
			"SYD" => "7", 
			"TAN" => "7", 
			"TIMER" => "7", 
			"TIMESERIAL" => "7", 
			"TIMEVALUE" => "7", 
			"TRIM" => "7", 
			"TRIM$" => "7", 
			"TYPENAME" => "7", 
			"UBOUND" => "7", 
			"UCASE" => "7", 
			"UCASE$" => "7", 
			"VAL" => "7", 
			"WEEKDAY" => "7", 
			"WEEKDAYNAME" => "7", 
			"YEAR" => "7", 
			"ANY" => "8", 
			"ATN" => "8", 
			"CALENDAR" => "8", 
			"CIRCLE" => "8", 
			"CURRENCY" => "8", 
			"DEFBOOL" => "8", 
			"DEFBYTE" => "8", 
			"DEFCUR" => "8", 
			"DEFDATE" => "8", 
			"DEFDBL" => "8", 
			"DEFDEC" => "8", 
			"DEFINT" => "8", 
			"DEFLNG" => "8", 
			"DEFOBJ" => "8", 
			"DEFSNG" => "8", 
			"DEFSTR" => "8", 
			"DEFVAR" => "8", 
			"EQV" => "8", 
			"GOSUB" => "8", 
			"IMP" => "8", 
			"INITIALIZE" => "8", 
			"ISMISSING" => "8", 
			"LET" => "8", 
			"LINE" => "8", 
			"LSET" => "8", 
			"RSET" => "8", 
			"SGN" => "8", 
			"SQR" => "8", 
			"TERMINATE" => "8", 
			"VARIANT" => "8", 
			"VARTYPE" => "8", 
			"WEND" => "8");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.



$this->linkscripts    	= array(
			"1" => "donothing", 
			"7" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing", 
			"4" => "donothing", 
			"5" => "donothing", 
			"6" => "donothing", 
			"8" => "donothing");
}



function donothing($keywordin)
{
	return $keywordin;
}

}

?>

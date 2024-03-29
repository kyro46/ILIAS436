<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_flisp extends HFile{
   function HFile_flisp(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// FLISP
/*************************************/
// Flags

$this->nocase            	= "1";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple", "gray", "brown", "blue");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array();
$this->unindent          	= array();

// String characters and delimiters

$this->stringchars       	= array();
$this->delimiters        	= array("~", "!", "$", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", ".", "?", "/");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array(":");
$this->blockcommenton    	= array(";|");
$this->blockcommentoff   	= array("|;");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"boxed_column" => "1", 
			"boxed_radio_column" => "1", 
			"boxed_radio_row" => "1", 
			"boxed_row" => "1", 
			"button" => "1", 
			"column" => "1", 
			"concatenation" => "1", 
			"dialog" => "1", 
			"edit_box" => "1", 
			"errtile" => "1", 
			"image" => "1", 
			"image_button" => "1", 
			"list_box" => "1", 
			"ok_only" => "1", 
			"ok_cancel" => "1", 
			"ok_cancel_help" => "1", 
			"ok_cancel_help_errtile" => "1", 
			"ok_cancel_help_info" => "1", 
			"paragraph" => "1", 
			"popup_list" => "1", 
			"radio_button" => "1", 
			"radio_column" => "1", 
			"radio_row" => "1", 
			"row" => "1", 
			"slider" => "1", 
			"spacer" => "1", 
			"spacer_0" => "1", 
			"spacer_1" => "1", 
			"text" => "1", 
			"text_part" => "1", 
			"toggle" => "1", 
			"@include" => "2", 
			"action" => "3", 
			"alignment" => "3", 
			"allow_accept" => "3", 
			"aspect_ratio" => "3", 
			"big_increment" => "3", 
			"children_alignment" => "3", 
			"children_fixed_height" => "3", 
			"children_fixed_width" => "3", 
			"color" => "3", 
			"edit_limit" => "3", 
			"edit_width" => "3", 
			"fixed_height" => "3", 
			"fixed_width" => "3", 
			"height" => "3", 
			"initial_focus" => "3", 
			"is_bold" => "3", 
			"is_cancel" => "3", 
			"is_default" => "3", 
			"is_enabled" => "3", 
			"is_tab_stop" => "3", 
			"key" => "3", 
			"label" => "3", 
			"layout" => "3", 
			"list" => "3", 
			"max_value" => "3", 
			"min_value" => "3", 
			"mnemonic" => "3", 
			"multiple_select" => "3", 
			"small_increment" => "3", 
			"tabs" => "3", 
			"value" => "3", 
			"width" => "3", 
			"abs" => "4", 
			"acad_colordlg" => "4", 
			"acad_helpdlg" => "4", 
			"acad_strlsort" => "4", 
			"action_tile" => "4", 
			"add_list" => "4", 
			"ads" => "4", 
			"alert" => "4", 
			"alloc" => "4", 
			"and" => "4", 
			"angle" => "4", 
			"angtof" => "4", 
			"angtos" => "4", 
			"append" => "4", 
			"apply" => "4", 
			"arx" => "4", 
			"arxload" => "4", 
			"arxunload" => "4", 
			"ascii" => "4", 
			"assoc" => "4", 
			"atan" => "4", 
			"atof" => "4", 
			"atoi" => "4", 
			"atom" => "4", 
			"atoms_family" => "4", 
			"autoarxload" => "4", 
			"autoload" => "4", 
			"autoxload" => "4", 
			"boole" => "4", 
			"boundp" => "4", 
			"car" => "4", 
			"cdr" => "4", 
			"caar" => "4", 
			"cadr" => "4", 
			"cddr" => "4", 
			"caaar" => "4", 
			"caadr" => "4", 
			"cadar" => "4", 
			"caddr" => "4", 
			"cdaar" => "4", 
			"cdadr" => "4", 
			"cddar" => "4", 
			"cdddr" => "4", 
			"caaadr" => "4", 
			"caadar" => "4", 
			"cadaar" => "4", 
			"cdaaar" => "4", 
			"caaddr" => "4", 
			"caddar" => "4", 
			"cddaar" => "4", 
			"cadadr" => "4", 
			"caaaar" => "4", 
			"cdadar" => "4", 
			"cdaadr" => "4", 
			"cadddr" => "4", 
			"cdaddr" => "4", 
			"cddadr" => "4", 
			"cdddar" => "4", 
			"cddddr" => "4", 
			"chr" => "4", 
			"client_data_tile" => "4", 
			"close" => "4", 
			"command" => "4", 
			"cond" => "4", 
			"cons" => "4", 
			"cos" => "4", 
			"cvunit" => "4", 
			"dictnext" => "4", 
			"dictsearch" => "4", 
			"dimx_tile" => "4", 
			"dimy_tile" => "4", 
			"distance" => "4", 
			"distof" => "4", 
			"done_dialog" => "4", 
			"end_image" => "4", 
			"end_list" => "4", 
			"entdel" => "4", 
			"entget" => "4", 
			"entlast" => "4", 
			"entmake" => "4", 
			"entmod" => "4", 
			"entnext" => "4", 
			"entsel" => "4", 
			"entupd" => "4", 
			"eq" => "4", 
			"equal" => "4", 
			"eval" => "4", 
			"exit" => "4", 
			"exp" => "4", 
			"expand" => "4", 
			"expt" => "4", 
			"fill_image" => "4", 
			"findfile" => "4", 
			"fix" => "4", 
			"float" => "4", 
			"foreach" => "4", 
			"gc" => "4", 
			"gcd" => "4", 
			"get_attr" => "4", 
			"get_tile" => "4", 
			"getangle" => "4", 
			"getcfg" => "4", 
			"getcorner" => "4", 
			"getdist" => "4", 
			"getenv" => "4", 
			"getfiled" => "4", 
			"getint" => "4", 
			"getkword" => "4", 
			"getorient" => "4", 
			"getpoint" => "4", 
			"getreal" => "4", 
			"getstring" => "4", 
			"getvar" => "4", 
			"graphscr" => "4", 
			"grclear" => "4", 
			"grdraw" => "4", 
			"grread" => "4", 
			"grtext" => "4", 
			"grvecs" => "4", 
			"handent" => "4", 
			"help" => "4", 
			"if" => "4", 
			"initget" => "4", 
			"inters" => "4", 
			"itoa" => "4", 
			"lambda" => "4", 
			"last" => "4", 
			"length" => "4", 
			"listp" => "4", 
			"load_dialog" => "4", 
			"load" => "4", 
			"log" => "4", 
			"logand" => "4", 
			"logior" => "4", 
			"lsh" => "4", 
			"mapcar" => "4", 
			"max" => "4", 
			"mem" => "4", 
			"member" => "4", 
			"menucmd" => "4", 
			"min" => "4", 
			"minusp" => "4", 
			"mode_tile" => "4", 
			"namedobjdict" => "4", 
			"nentsel" => "4", 
			"nentselp" => "4", 
			"new_dialog" => "4", 
			"not" => "4", 
			"nth" => "4", 
			"null" => "4", 
			"numberp" => "4", 
			"open" => "4", 
			"or" => "4", 
			"osnap" => "4", 
			"polar" => "4", 
			"prin1" => "4", 
			"princ" => "4", 
			"print" => "4", 
			"progn" => "4", 
			"prompt" => "4", 
			"quit" => "4", 
			"quote" => "4", 
			"read" => "4", 
			"read_char" => "4", 
			"read_line" => "4", 
			"redraw" => "4", 
			"regapp" => "4", 
			"rem" => "4", 
			"repeat" => "4", 
			"reverse" => "4", 
			"rtos" => "4", 
			"set" => "4", 
			"set_tile" => "4", 
			"setcfg" => "4", 
			"setfunhelp" => "4", 
			"setq" => "4", 
			"setvar" => "4", 
			"sin" => "4", 
			"slide_image" => "4", 
			"snvalid" => "4", 
			"sqrt" => "4", 
			"ssadd" => "4", 
			"ssdel" => "4", 
			"ssget" => "4", 
			"sslength" => "4", 
			"ssmemb" => "4", 
			"ssname" => "4", 
			"startapp" => "4", 
			"start_dialog" => "4", 
			"start_image" => "4", 
			"start_list" => "4", 
			"strcase" => "4", 
			"strcat" => "4", 
			"strlen" => "4", 
			"subst" => "4", 
			"substr" => "4", 
			"tablet" => "4", 
			"tblnext" => "4", 
			"tblobjname" => "4", 
			"tblsearch" => "4", 
			"term_dialog" => "4", 
			"terpri" => "4", 
			"textbox" => "4", 
			"textpage" => "4", 
			"textscr" => "4", 
			"trace" => "4", 
			"trans" => "4", 
			"type" => "4", 
			"unload_dialog" => "4", 
			"untrace" => "4", 
			"vector_image" => "4", 
			"ver" => "4", 
			"vmon" => "4", 
			"vports" => "4", 
			"wcmatch" => "4", 
			"while" => "4", 
			"write_char" => "4", 
			"write_line" => "4", 
			"xdroom" => "4", 
			"xdsize" => "4", 
			"xload" => "4", 
			"xunload" => "4", 
			"zerop" => "4", 
			"defun" => "5");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing", 
			"4" => "donothing", 
			"5" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>

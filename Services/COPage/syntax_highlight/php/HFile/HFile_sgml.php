<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_sgml extends HFile{
   function HFile_sgml(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// SGML
/*************************************/
// Flags

$this->nocase            	= "1";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array();
$this->unindent          	= array();

// String characters and delimiters

$this->stringchars       	= array("\"", "\"");
$this->delimiters        	= array("~", "!", "@", "$", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", " ", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("");
$this->blockcommenton    	= array("<!--");
$this->blockcommentoff   	= array("-->");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"<>" => "1", 
			"%;" => "2", 
			"&;" => "2");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>

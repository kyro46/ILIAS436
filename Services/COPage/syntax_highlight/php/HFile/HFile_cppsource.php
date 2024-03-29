<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_cppsource extends HFile{
   function HFile_cppsource(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// C++ Source
/*************************************/
// Flags

$this->nocase            	= "0";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple", "gray", "brown", "blue");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array("{");
$this->unindent          	= array("}");

// String characters and delimiters

$this->stringchars       	= array("\"", "'");
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "{", "}", "[", "]", "<", ">", ":", ";", "\"", "'", " ", ",", "	", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("//");
$this->blockcommenton    	= array("/*");
$this->blockcommentoff   	= array("*/");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"bool" => "1", 
			"char" => "1", 
			"class" => "1", 
			"const" => "1", 
			"case" => "1", 
			"catch" => "1", 
			"const_cast" => "1", 
			"double" => "1", 
			"default" => "1", 
			"do" => "1", 
			"delete" => "1", 
			"dynamic_cast" => "1", 
			"else" => "1", 
			"enum" => "1", 
			"explicit" => "1", 
			"export" => "1", 
			"extern" => "1", 
			"for" => "1", 
			"false" => "1", 
			"float" => "1", 
			"friend" => "1", 
			"if" => "1", 
			"inline" => "1", 
			"int" => "1", 
			"long" => "1", 
			"mutable" => "1", 
			"new" => "1", 
			"namespace" => "1", 
			"operator" => "1", 
			"protected" => "1", 
			"private" => "1", 
			"public" => "1", 
			"reinterpret_cast" => "1", 
			"return" => "1", 
			"short" => "1", 
			"signed" => "1", 
			"sizeof" => "1", 
			"static" => "1", 
			"struct" => "1", 
			"static_cast" => "1", 
			"switch" => "1", 
			"template" => "1", 
			"throw" => "1", 
			"true" => "1", 
			"typedef" => "1", 
			"typename" => "1", 
			"this" => "1", 
			"try" => "1", 
			"typeid" => "1", 
			"union" => "1", 
			"unsigned" => "1", 
			"using" => "1", 
			"virtual" => "1", 
			"void" => "1", 
			"volatile" => "1", 
			"wchar_t" => "1", 
			"while" => "1", 
			"asm" => "2", 
			"auto" => "2", 
			"break" => "2", 
			"continue" => "2", 
			"goto" => "2", 
			"register" => "2", 
			"#define" => "3", 
			"#error" => "3", 
			"#include" => "3", 
			"#elif" => "3", 
			"#if" => "3", 
			"#line" => "3", 
			"#else" => "3", 
			"#ifdef" => "3", 
			"#pragma" => "3", 
			"#endif" => "3", 
			"#ifndef" => "3", 
			"#undef" => "3", 
			"+" => "4", 
			"-" => "4", 
			"=" => "4", 
			"//" => "4", 
			"/" => "4", 
			"%" => "4", 
			"&" => "4", 
			">" => "4", 
			"<" => "4", 
			"^" => "4", 
			"!" => "4", 
			"|" => "4", 
			"*" => "4", 
			"{" => "5", 
			"}" => "5", 
			";" => "5", 
			"(" => "5", 
			")" => "5", 
			"," => "5");

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

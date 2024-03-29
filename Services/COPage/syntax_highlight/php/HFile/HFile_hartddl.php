<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_hartddl extends HFile{
   function HFile_hartddl(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// HART DDL
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
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", "	", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("//");
$this->blockcommenton    	= array("/*");
$this->blockcommentoff   	= array("*/");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"ARRAY" => "1", 
			"COMMAND" => "1", 
			"DEVICE" => "1", 
			"DEVICE_TYPE" => "1", 
			"DD_REVISION" => "1", 
			"DEVICE_REVISION" => "1", 
			"EDIT_DISPLAY" => "1", 
			"IMPORT" => "1", 
			"MANUFACTURER" => "1", 
			"MENU" => "1", 
			"METHOD" => "1", 
			"VARIABLE" => "1", 
			"WRITE_AS_ONE" => "1", 
			"ALL" => "2", 
			"AO" => "2", 
			"ARRAYS" => "2", 
			"AUTO" => "2", 
			"BAD" => "2", 
			"CASE" => "2", 
			"CLASS" => "2", 
			"COLLECTION" => "2", 
			"COMMANDS" => "2", 
			"COMM_ERROR" => "2", 
			"CONSTANT_UNIT" => "2", 
			"CORRECTABLE" => "2", 
			"DATA" => "2", 
			"DATA_ENTRY_ERROR" => "2", 
			"DATA_ENTRY_WARNING" => "2", 
			"DATE" => "2", 
			"DATE_AND_TIME" => "2", 
			"DEFAULT" => "2", 
			"DEFINITION" => "2", 
			"DETAIL" => "2", 
			"DISPLAY_ITEMS" => "2", 
			"DURATION" => "2", 
			"DV" => "2", 
			"EDIT_DISPLAYS" => "2", 
			"EDIT_ITEMS" => "2", 
			"ELEMENTS" => "2", 
			"ELSE" => "2", 
			"EVENT" => "2", 
			"EVERYTHING" => "2", 
			"FALSE" => "2", 
			"GOOD" => "2", 
			"HANDLING" => "2", 
			"HARDWARE" => "2", 
			"HELP" => "2", 
			"IF" => "2", 
			"IGNORE_IN_HANDHELD" => "2", 
			"INFO" => "2", 
			"ITEMS" => "2", 
			"LABEL" => "2", 
			"MANUAL" => "2", 
			"MEMBERS" => "2", 
			"MISC" => "2", 
			"MORE" => "2", 
			"NUMBER" => "2", 
			"OPERATION" => "2", 
			"POST_EDIT_ACTIONS" => "2", 
			"POST_READ_ACTIONS" => "2", 
			"POST_WRITE_ACTIONS" => "2", 
			"PRE_EDIT_ACTIONS" => "2", 
			"PRE_READ_ACTIONS" => "2", 
			"PRE_WRITE_ACTIONS" => "2", 
			"PROCESS" => "2", 
			"PROCESS_ERROR" => "2", 
			"READ_TIMEOUT" => "2", 
			"REDEFINITIONS" => "2", 
			"REFRESH" => "2", 
			"RELATIONS" => "2", 
			"RELEASED" => "2", 
			"RESPONSE_CODES" => "2", 
			"REVIEW" => "2", 
			"REVISION" => "2", 
			"SELECT" => "2", 
			"SELF_CORRECTING" => "2", 
			"SOFTWORE" => "2", 
			"STATE" => "2", 
			"SUMMARY" => "2", 
			"TIME" => "2", 
			"TRANSACTION" => "2", 
			"TRUE" => "2", 
			"TV" => "2", 
			"TYPE" => "2", 
			"UNCORRECTABLE" => "2", 
			"UNIT" => "2", 
			"VALIDITY" => "2", 
			"VARIABLES" => "2", 
			"WRITE_ITMEOUT" => "2", 
			"break" => "2", 
			"char" => "2", 
			"continue" => "2", 
			"default" => "2", 
			"do" => "2", 
			"double" => "2", 
			"else" => "2", 
			"float" => "2", 
			"for" => "2", 
			"if" => "2", 
			"int" => "2", 
			"long" => "2", 
			"return" => "2", 
			"short" => "2", 
			"signed" => "2", 
			"switch" => "2", 
			"unsigned" => "2", 
			"while" => "2", 
			"ABORT_ON_ALL_COMM_STATUS" => "3", 
			"ABORT_ON_ALL_DEVICE_STATUS" => "3", 
			"ABORT_ON_ALL_RESPONSE_CODES" => "3", 
			"ABORT_ON_COMM_ERROR" => "3", 
			"ABORT_ON_COMM_STATUS" => "3", 
			"ABORT_ON_DEVICE_STATUS" => "3", 
			"ABORT_ON_NO_DEVICE" => "3", 
			"ABORT_ON_RESPONSE_CODE" => "3", 
			"ACKNOWLEDGE" => "3", 
			"acknowledge" => "3", 
			"add_abort_method" => "3", 
			"assign_double" => "3", 
			"assign_float" => "3", 
			"assign_int" => "3", 
			"assign_var" => "3", 
			"DELAY" => "3", 
			"DELAY_TIME" => "3", 
			"dassign" => "3", 
			"delay" => "3", 
			"display" => "3", 
			"display_comm_status" => "3", 
			"display_device_status" => "3", 
			"display_response_status" => "3", 
			"display_xmtr_status" => "3", 
			"ext_send_command" => "3", 
			"ext_send_command_trans" => "3", 
			"fassign" => "3", 
			"fgetval" => "3", 
			"float_value" => "3", 
			"fsetval" => "3", 
			"fvar_value" => "3", 
			"GET_DEV_VAR_VALUE" => "3", 
			"GET_LOCAL_VAR_VALUE" => "3", 
			"get_dev_var_value" => "3", 
			"get_dictionary_string" => "3", 
			"get_local_var_value" => "3", 
			"get_more_status" => "3", 
			"get_status_code_string" => "3", 
			"IGNORE_ALL_COMM_STATUS" => "3", 
			"IGNORE_ALL_DEVICE_STATUS" => "3", 
			"IGNORE_ALL_RESPONSE_CODES" => "3", 
			"IGNORE_COMM_ERROR" => "3", 
			"IGNORE_COMM_STATUS" => "3", 
			"IGNORE_DEVICE_STATUS" => "3", 
			"IGNORE_NO_DEVICE" => "3", 
			"IGNORE_RESPONSE_CODE" => "3", 
			"iassign" => "3", 
			"igetval" => "3", 
			"int_value" => "3", 
			"isetval" => "3", 
			"ivar_value" => "3", 
			"lassign" => "3", 
			"lgetval" => "3", 
			"long_value" => "3", 
			"lsetval" => "3", 
			"lvar_value" => "3", 
			"PUT_MESSAGE" => "3", 
			"process_abort" => "3", 
			"put_message" => "3", 
			"RETRY_ON_ALL_COMM_STATUS" => "3", 
			"RETRY_ON_ALL_DEVICE_STATUS" => "3", 
			"RETRY_ON_ALL_RESPONSE_CODES" => "3", 
			"RETRY_ON_COMM_ERROR" => "3", 
			"RETRY_ON_COMM_STATUS" => "3", 
			"RETRY_ON_DEVICE_STATUS" => "3", 
			"RETRY_ON_NO_DEVICE" => "3", 
			"RETRY_ON_RESPONSE_CODE" => "3", 
			"remove_abort_method" => "3", 
			"remove_all_abort_methods" => "3", 
			"rspcode_string" => "3", 
			"SELECT_FROM_LIST" => "3", 
			"SET_NUMBER_OF_RETRIES" => "3", 
			"save_values" => "3", 
			"select_from_list" => "3", 
			"send" => "3", 
			"send_command" => "3", 
			"send_command_trans" => "3", 
			"send_trans" => "3", 
			"VARID" => "3", 
			"vassign" => "3", 
			"XMTR_ABORT_ON_ALL_COMM_STATUS" => "3", 
			"XMTR_ABORT_ON_ALL_DEVICE_STATUS" => "3", 
			"XMTR_ABORT_ON_ALL_RESPONSE_CODES" => "3", 
			"XMTR_ABORT_ON_COMM_ERROR" => "3", 
			"XMTR_ABORT_ON_COMM_STATUS" => "3", 
			"XMTR_ABORT_ON_DATA" => "3", 
			"XMTR_ABORT_ON_DEVICE_STATUS" => "3", 
			"XMTR_ABORT_ON_NO_DEVICE" => "3", 
			"XMTR_ABORT_ON_RESPONSE_CODE" => "3", 
			"XMTR_IGNORE_ALL_COMM_STATUS" => "3", 
			"XMTR_IGNORE_ALL_DEVICE_STATUS" => "3", 
			"XMTR_IGNORE_ALL_RESPONSE_CODES" => "3", 
			"XMTR_IGNORE_COMM_ERROR" => "3", 
			"XMTR_IGNORE_COMM_STATUS" => "3", 
			"XMTR_IGNORE_DEVICE_STATUS" => "3", 
			"XMTR_IGNORE_NO_DEVICE" => "3", 
			"XMTR_IGNORE_RESPONSE_CODE" => "3", 
			"XMTR_RETRY_ON_ALL_COMM_STATUS" => "3", 
			"XMTR_RETRY_ON_ALL_DEVICE_STATUS" => "3", 
			"XMTR_RETRY_ON_ALL_RESPONSE_CODES" => "3", 
			"XMTR_RETRY_ON_COMM_ERROR" => "3", 
			"XMTR_RETRY_ON_COMM_STATUS" => "3", 
			"XMTR_RETRY_ON_DATA" => "3", 
			"XMTR_RETRY_ON_DEVICE_STATUS" => "3", 
			"XMTR_RETRY_ON_NO_DEVICE" => "3", 
			"XMTR_RETRY_ON_RESPONSE_CODE" => "3", 
			"ANALOG_OUTPUT" => "4", 
			"ASCII" => "4", 
			"BIT_ENUMERATED" => "4", 
			"BITSTRING" => "4", 
			"CORRECTION" => "4", 
			"COMPUTATION" => "4", 
			"DISCRETE" => "4", 
			"DYNAMIC" => "4", 
			"DIAGNOSTIC" => "4", 
			"DISPLAY_FORMAT" => "4", 
			"DISPLAY_VALUE" => "4", 
			"DOUBLE" => "4", 
			"EDIT_FORMAT" => "4", 
			"ENUMERATED" => "4", 
			"FLOAT" => "4", 
			"FREQUENCY" => "4", 
			"HART" => "4", 
			"INDEX" => "4", 
			"INPUT" => "4", 
			"INTEGER" => "4", 
			"LOCAL" => "4", 
			"LOCAL_DISPLAY" => "4", 
			"MAX_VALUE" => "4", 
			"MIN_VALUE" => "4", 
			"MISC_ERROR" => "4", 
			"MISC_WARNING" => "4", 
			"MODE" => "4", 
			"MODE_ERROR" => "4", 
			"PACKED_ASCII" => "4", 
			"PASSWORD" => "4", 
			"READ" => "4", 
			"REPLY" => "4", 
			"REQUEST" => "4", 
			"READ_ONLY" => "4", 
			"SCALING_FACTOR" => "4", 
			"SERVICE" => "4", 
			"SUCCESS" => "4", 
			"UNSIGNED_INTEGER" => "4", 
			"USER_INTERFACE" => "4", 
			"WRITE" => "4", 
			"[]" => "5", 
			"ADD" => "5", 
			"DELETE" => "5", 
			"REDEFINE" => "5", 
			"+" => "5", 
			"-" => "5", 
			"=" => "5", 
			"//" => "5", 
			"/" => "5", 
			"%" => "5", 
			"&" => "5", 
			">" => "5", 
			"<" => "5", 
			"^" => "5", 
			"!" => "5", 
			"|" => "5");

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

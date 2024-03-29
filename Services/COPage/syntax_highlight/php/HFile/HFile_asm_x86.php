<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_asm_x86 extends HFile{
   function HFile_asm_x86(){
     $this->HFile();
/*************************************/
// Beautifier Highlighting Configuration File 
// Assembler x86
/*************************************/
// Flags

$this->nocase            	= "1";
$this->notrim            	= "1";
$this->perl              	= "0";

// Colours

$this->colours        		= array("blue", "purple", "gray");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array();
$this->unindent          	= array();

// String characters and delimiters

$this->stringchars       	= array();
$this->delimiters        	= array(" ", "	");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array(";");
$this->blockcommenton    	= array("");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"aaa" => "1", 
			"aad" => "1", 
			"aam" => "1", 
			"aas" => "1", 
			"adc" => "1", 
			"add" => "1", 
			"and" => "2", 
			"arpl" => "1", 
			"bound" => "1", 
			"bsf" => "1", 
			"bsr" => "1", 
			"bswap" => "1", 
			"bt" => "1", 
			"btc" => "1", 
			"btr" => "1", 
			"bts" => "1", 
			"call" => "1", 
			"cbw" => "1", 
			"cdq" => "1", 
			"clc" => "1", 
			"cld" => "1", 
			"cli" => "1", 
			"clts" => "1", 
			"cmc" => "1", 
			"cmov" => "1", 
			"cmp" => "1", 
			"cmps" => "1", 
			"cmpsb" => "1", 
			"cmpsd" => "1", 
			"cmpsw" => "1", 
			"cmpxchg" => "1", 
			"cmpxchg8b" => "1", 
			"cpuid" => "1", 
			"cwd" => "1", 
			"cwde" => "1", 
			"daa" => "1", 
			"das" => "1", 
			"dec" => "1", 
			"div" => "1", 
			"emms" => "1", 
			"enter" => "1", 
			"esc" => "1", 
			"fcmov" => "1", 
			"fcomi" => "1", 
			"fwait" => "1", 
			"hlt" => "1", 
			"idiv" => "1", 
			"imul" => "1", 
			"in" => "1", 
			"inc" => "1", 
			"ins" => "1", 
			"insb" => "1", 
			"insd" => "1", 
			"insw" => "1", 
			"int" => "1", 
			"into" => "1", 
			"invd" => "1", 
			"invlpg" => "1", 
			"iret" => "1", 
			"iretd" => "1", 
			"ja" => "1", 
			"jae" => "1", 
			"jb" => "1", 
			"jbe" => "1", 
			"jc" => "1", 
			"jcxz" => "1", 
			"je" => "1", 
			"jecxz" => "1", 
			"jg" => "1", 
			"jge" => "1", 
			"jl" => "1", 
			"jle" => "1", 
			"jmp" => "1", 
			"jna" => "1", 
			"jnae" => "1", 
			"jnb" => "1", 
			"jnbe" => "1", 
			"jnc" => "1", 
			"jne" => "1", 
			"jng" => "1", 
			"jnge" => "1", 
			"jnl" => "1", 
			"jnle" => "1", 
			"jno" => "1", 
			"jnp" => "1", 
			"jns" => "1", 
			"jnz" => "1", 
			"jo" => "1", 
			"jp" => "1", 
			"jpe" => "1", 
			"jpo" => "1", 
			"js" => "1", 
			"jz" => "1", 
			"lahf" => "1", 
			"lar" => "1", 
			"lds" => "1", 
			"lea" => "1", 
			"leave" => "1", 
			"les" => "1", 
			"lfs" => "1", 
			"lgdt" => "1", 
			"lgs" => "1", 
			"lidt" => "1", 
			"lldt" => "1", 
			"lmsw" => "1", 
			"lock" => "1", 
			"lods" => "1", 
			"lodsb" => "1", 
			"lodsd" => "1", 
			"lodsw" => "1", 
			"loop" => "1", 
			"loope" => "1", 
			"loopne" => "1", 
			"loopnz" => "1", 
			"loopz" => "1", 
			"lsl" => "1", 
			"lss" => "1", 
			"ltr" => "1", 
			"mov" => "1", 
			"movd" => "1", 
			"movq" => "1", 
			"movs" => "1", 
			"movsb" => "1", 
			"movsd" => "1", 
			"movsw" => "1", 
			"movsx" => "1", 
			"movzx" => "1", 
			"msw" => "1", 
			"mul" => "1", 
			"neg" => "1", 
			"nop" => "1", 
			"not" => "2", 
			"or" => "2", 
			"out" => "1", 
			"outs" => "1", 
			"outsb" => "1", 
			"outsd" => "1", 
			"outsw" => "1", 
			"packsswb" => "1", 
			"packssdw" => "1", 
			"paddb" => "1", 
			"paddw" => "1", 
			"paddd" => "1", 
			"paddsb" => "1", 
			"paddsw" => "1", 
			"paddusb" => "1", 
			"paddusw" => "1", 
			"pand" => "1", 
			"pandn" => "1", 
			"pcmpeqb" => "1", 
			"pcmpeqw" => "1", 
			"pcmpeqd" => "1", 
			"pcmpgtb" => "1", 
			"pcmpgtw" => "1", 
			"pcmpgtd" => "1", 
			"pmulhw" => "1", 
			"pmullw" => "1", 
			"pmaddwd" => "1", 
			"pop" => "1", 
			"popa" => "1", 
			"popad" => "1", 
			"popf" => "1", 
			"popfd" => "1", 
			"por" => "1", 
			"psllw" => "1", 
			"pslld" => "1", 
			"psllq" => "1", 
			"psrlw" => "1", 
			"psrld" => "1", 
			"psrlq" => "1", 
			"psraw" => "1", 
			"psrad" => "1", 
			"psubb" => "1", 
			"psubw" => "1", 
			"psubd" => "1", 
			"psubsb" => "1", 
			"psubsw" => "1", 
			"psubusb" => "1", 
			"psubusw" => "1", 
			"punpckhbw" => "1", 
			"punpckhwd" => "1", 
			"punpckhdq" => "1", 
			"punpcklbw" => "1", 
			"punpcklwd" => "1", 
			"punpckldq" => "1", 
			"push" => "1", 
			"pusha" => "1", 
			"pushad" => "1", 
			"pushf" => "1", 
			"pushfd" => "1", 
			"pxor" => "1", 
			"rcl" => "1", 
			"rcr" => "1", 
			"rdmsr" => "1", 
			"rdpmc" => "1", 
			"rdtsc" => "1", 
			"rep" => "1", 
			"repe" => "1", 
			"repne" => "1", 
			"repnz" => "1", 
			"repz" => "1", 
			"ret" => "1", 
			"retf" => "1", 
			"retn" => "1", 
			"rol" => "1", 
			"ror" => "1", 
			"rsm" => "1", 
			"sahf" => "1", 
			"sal" => "1", 
			"sar" => "1", 
			"sbb" => "1", 
			"scas" => "1", 
			"scasb" => "1", 
			"scasd" => "1", 
			"scasw" => "1", 
			"setae" => "1", 
			"setb" => "1", 
			"setbe" => "1", 
			"setc" => "1", 
			"sete" => "1", 
			"setg" => "1", 
			"setge" => "1", 
			"setl" => "1", 
			"setle" => "1", 
			"setna" => "1", 
			"setnae" => "1", 
			"setnb" => "1", 
			"setnc" => "1", 
			"setne" => "1", 
			"setng" => "1", 
			"setnge" => "1", 
			"setnl" => "1", 
			"setnle" => "1", 
			"setno" => "1", 
			"setnp" => "1", 
			"setns" => "1", 
			"setnz" => "1", 
			"seto" => "1", 
			"setp" => "1", 
			"setpe" => "1", 
			"setpo" => "1", 
			"sets" => "1", 
			"setz" => "1", 
			"sgdt" => "1", 
			"shl" => "2", 
			"shld" => "1", 
			"shr" => "2", 
			"shrd" => "1", 
			"sidt" => "1", 
			"sldt" => "1", 
			"smsw" => "1", 
			"stc" => "1", 
			"std" => "1", 
			"sti" => "1", 
			"stos" => "1", 
			"stosb" => "1", 
			"stosd" => "1", 
			"stosw" => "1", 
			"str" => "1", 
			"sub" => "1", 
			"test" => "1", 
			"verr" => "1", 
			"verw" => "1", 
			"wbinvd" => "1", 
			"wrmsr" => "1", 
			"xadd" => "1", 
			"xchg" => "1", 
			"xlat" => "1", 
			"xlatb" => "1", 
			"xor" => "2", 
			"%out" => "2", 
			".186" => "2", 
			".286" => "2", 
			".286c" => "2", 
			".286p" => "2", 
			".287" => "2", 
			".386" => "2", 
			".386p" => "2", 
			".387" => "2", 
			".8086" => "2", 
			".8087" => "2", 
			".alpha" => "2", 
			".seq" => "2", 
			".code" => "2", 
			".const" => "2", 
			".cref" => "2", 
			".data" => "2", 
			".data?" => "2", 
			".err" => "2", 
			".err1" => "2", 
			".err2" => "2", 
			".errb" => "2", 
			".errdef" => "2", 
			".errdif" => "2", 
			".erre" => "2", 
			".fardata" => "2", 
			".fardata?" => "2", 
			".lall" => "2", 
			".lfcond" => "2", 
			".list" => "2", 
			".model" => "2", 
			".msfloat" => "2", 
			".radix" => "2", 
			".sall" => "2", 
			".sfcond" => "2", 
			".stack" => "2", 
			".type" => "2", 
			".xall" => "2", 
			".xcref" => "2", 
			".xlist" => "2", 
			"@curseg" => "2", 
			"@filename" => "2", 
			"@code" => "2", 
			"@codesize" => "2", 
			"@datasize" => "2", 
			"@const" => "2", 
			"@data" => "2", 
			"@data?" => "2", 
			"@fardata" => "2", 
			"@fardata?" => "2", 
			"@stack" => "2", 
			"align" => "2", 
			"assume" => "2", 
			"at" => "2", 
			"b" => "2", 
			"byte" => "2", 
			"comm" => "2", 
			"comment" => "2", 
			"common" => "2", 
			"compact" => "2", 
			"d" => "2", 
			"db" => "2", 
			"dd" => "2", 
			"df" => "2", 
			"dosseg" => "2", 
			"dup" => "2", 
			"fq" => "2", 
			"dt" => "2", 
			"dw" => "2", 
			"dword" => "2", 
			"else" => "2", 
			"end" => "2", 
			"endif" => "2", 
			"endm" => "2", 
			"endp" => "2", 
			"ends" => "2", 
			"eq" => "2", 
			"equ" => "2", 
			"even" => "2", 
			"exitm" => "2", 
			"extrn" => "2", 
			"far" => "2", 
			"ge" => "2", 
			"group" => "2", 
			"h" => "2", 
			"high" => "2", 
			"huge" => "2", 
			"ifdef" => "2", 
			"include" => "2", 
			"includelib" => "2", 
			"irp" => "2", 
			"irpc" => "2", 
			"label" => "2", 
			"large" => "2", 
			"le" => "2", 
			"length" => "2", 
			"low" => "2", 
			"local" => "2", 
			"lt" => "2", 
			"macro" => "2", 
			"mask" => "2", 
			"medium" => "2", 
			"memory" => "2", 
			"name" => "2", 
			"near" => "2", 
			"o" => "2", 
			"offset" => "2", 
			"org" => "2", 
			"page" => "2", 
			"para" => "2", 
			"proc" => "2", 
			"public" => "2", 
			"purge" => "2", 
			"q" => "2", 
			"record" => "2", 
			"rept" => "2", 
			"seg" => "2", 
			"segment" => "2", 
			"short" => "2", 
			"size" => "2", 
			"small" => "2", 
			"stack" => "2", 
			"struc" => "2", 
			"subttl" => "2", 
			"this" => "2", 
			"tiny" => "2", 
			"title" => "2", 
			"type" => "2", 
			"use16" => "2", 
			"use32" => "2", 
			"width" => "2", 
			"word" => "2", 
			"ah" => "3", 
			"al" => "3", 
			"ax" => "3", 
			"bh" => "3", 
			"bl" => "3", 
			"bp" => "3", 
			"bx" => "3", 
			"ch" => "3", 
			"cl" => "3", 
			"cs" => "3", 
			"cx" => "3", 
			"dh" => "3", 
			"di" => "3", 
			"dl" => "3", 
			"ds" => "3", 
			"dx" => "3", 
			"eax" => "3", 
			"ebx" => "3", 
			"ecx" => "3", 
			"edi" => "3", 
			"edx" => "3", 
			"esi" => "3", 
			"es" => "3", 
			"ip" => "3", 
			"si" => "3", 
			"sp" => "3");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.



$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing");
}



function donothing($keywordin)
{
	return $keywordin;
}

}

?>

<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_lingo extends HFile{
   function HFile_lingo(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// Lingo
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

$this->indent            	= array("then", "else");
$this->unindent          	= array("end", "end if", "end repeat");

// String characters and delimiters

$this->stringchars       	= array("\"");
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", "	", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("--");
$this->blockcommenton    	= array("--");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"abort" => "1", 
			"after" => "1", 
			"and" => "1", 
			"before" => "1", 
			"case" => "1", 
			"do" => "1", 
			"else" => "1", 
			"end" => "1", 
			"FALSE" => "1", 
			"global" => "1", 
			"halt" => "1", 
			"if" => "1", 
			"ilk" => "1", 
			"into" => "1", 
			"me" => "1", 
			"new" => "1", 
			"of" => "1", 
			"on" => "1", 
			"or" => "1", 
			"otherwise" => "1", 
			"pass" => "1", 
			"property" => "1", 
			"put" => "1", 
			"repeat" => "1", 
			"result" => "1", 
			"RETURN" => "1", 
			"set" => "1", 
			"tell" => "1", 
			"the" => "1", 
			"then" => "1", 
			"to" => "1", 
			"TRUE" => "1", 
			"while" => "1", 
			"with" => "1", 
			"#" => "2", 
			"abbr" => "2", 
			"abbrev" => "2", 
			"abbreviated" => "2", 
			"abs" => "2", 
			"activateWindow" => "2", 
			"activeCastLib" => "2", 
			"activeWindow" => "2", 
			"actorList" => "2", 
			"add" => "2", 
			"addAt" => "2", 
			"addProp" => "2", 
			"alert" => "2", 
			"alertHook" => "2", 
			"alignment" => "2", 
			"allowCustomCaching" => "2", 
			"allowGraphicMenu" => "2", 
			"allowSaveLocal" => "2", 
			"allowTransportControl" => "2", 
			"allowVolumeControl" => "2", 
			"allowZooming" => "2", 
			"alphaThreshold" => "2", 
			"ancestor" => "2", 
			"antiAlias" => "2", 
			"append" => "2", 
			"applicationPath" => "2", 
			"atan" => "2", 
			"autoTab" => "2", 
			"backColor" => "2", 
			"BACKSPACE" => "2", 
			"beep" => "2", 
			"beepOn" => "2", 
			"beginRecording" => "2", 
			"beginSprite" => "2", 
			"bgColor" => "2", 
			"blend" => "2", 
			"blendLevel" => "2", 
			"border" => "2", 
			"bottom" => "2", 
			"boxDropShadow" => "2", 
			"boxType" => "2", 
			"buttonStyle" => "2", 
			"buttonType" => "2", 
			"call" => "2", 
			"callAncestor" => "2", 
			"cancelIdleLoad" => "2", 
			"castLib" => "2", 
			"castLibNum" => "2", 
			"center" => "2", 
			"centerRegPoint" => "2", 
			"centerStage" => "2", 
			"changeArea" => "2", 
			"channelCount" => "2", 
			"char" => "2", 
			"charPosToLoc" => "2", 
			"chars" => "2", 
			"charToNum" => "2", 
			"checkBoxAccess" => "2", 
			"checkBoxType" => "2", 
			"checkMark" => "2", 
			"chunkSize" => "2", 
			"clearFrame" => "2", 
			"clearGlobals" => "2", 
			"clickLoc" => "2", 
			"clickOn" => "2", 
			"close" => "2", 
			"closeWindow" => "2", 
			"closeXlib" => "2", 
			"color" => "2", 
			"colorDepth" => "2", 
			"commandDown" => "2", 
			"constrainH" => "2", 
			"constraint" => "2", 
			"constrainV" => "2", 
			"continue" => "2", 
			"controlDown" => "2", 
			"controller" => "2", 
			"copyToClipBoard" => "2", 
			"cos" => "2", 
			"count" => "2", 
			"cpuHogTicks" => "2", 
			"crop" => "2", 
			"cuePassed" => "2", 
			"cuePointNames" => "2", 
			"cuePointTimes" => "2", 
			"currentSpriteNum" => "2", 
			"currentTime" => "2", 
			"cursor" => "2", 
			"date" => "2", 
			"deactivateWindow" => "2", 
			"delay" => "2", 
			"delete" => "2", 
			"deleteAll" => "2", 
			"deleteAt" => "2", 
			"deleteFrame" => "2", 
			"deleteOne" => "2", 
			"deleteProp" => "2", 
			"depth" => "2", 
			"deskTopRectList" => "2", 
			"digitalVideoTimeScale" => "2", 
			"digitalVideoType" => "2", 
			"directToStage" => "2", 
			"dither" => "2", 
			"done" => "2", 
			"doubleClick" => "2", 
			"drawRect" => "2", 
			"dropShadow" => "2", 
			"duplicate" => "2", 
			"member" => "2", 
			"duplicateFrame" => "2", 
			"duration" => "2", 
			"editable" => "2", 
			"EMPTY" => "2", 
			"emulateMultiButtonMouse" => "2", 
			"enabled" => "2", 
			"endFrame" => "2", 
			"endRecording" => "2", 
			"endSprite" => "2", 
			"ENTER" => "2", 
			"enterFrame" => "2", 
			"environment" => "2", 
			"erase" => "2", 
			"exit" => "2", 
			"exitFrame" => "2", 
			"exitLock" => "2", 
			"exp" => "2", 
			"externalParamCount" => "2", 
			"externalParamName" => "2", 
			"externalParamValue" => "2", 
			"fadeIn" => "2", 
			"fadeOut" => "2", 
			"field" => "2", 
			"fileName" => "2", 
			"filled" => "2", 
			"findEmpty" => "2", 
			"findPos" => "2", 
			"findPosNear" => "2", 
			"finishIdleLoad" => "2", 
			"fixStageSize" => "2", 
			"flipH" => "2", 
			"flipV" => "2", 
			"float" => "2", 
			"floatP" => "2", 
			"floatPrecision" => "2", 
			"font" => "2", 
			"fontSize" => "2", 
			"fontStyle" => "2", 
			"foreColor" => "2", 
			"forget" => "2", 
			"frame" => "2", 
			"frameLabel" => "2", 
			"framePalette" => "2", 
			"frameRate" => "2", 
			"frameReady" => "2", 
			"frameScript" => "2", 
			"frameSound1" => "2", 
			"frameSound2" => "2", 
			"framesToHMS" => "2", 
			"frameTempo" => "2", 
			"frameTransition" => "2", 
			"freeBlock" => "2", 
			"freeBytes" => "2", 
			"frontWindow" => "2", 
			"getaProp" => "2", 
			"getAt" => "2", 
			"getBehaviorDescription" => "2", 
			"getBehaviorTooltip" => "2", 
			"getLast" => "2", 
			"getNthFileNameInFolder" => "2", 
			"getOne" => "2", 
			"getPos" => "2", 
			"getPref" => "2", 
			"getProp" => "2", 
			"getPropAt" => "2", 
			"getPropertyDescriptionList" => "2", 
			"globals" => "2", 
			"go" => "2", 
			"height" => "2", 
			"hilite" => "2", 
			"hitTest" => "2", 
			"HMStoFrames" => "2", 
			"idle" => "2", 
			"idleHandlerPeriod" => "2", 
			"idleLoadDone" => "2", 
			"idleLoadMode" => "2", 
			"idleLoadPeriod" => "2", 
			"idleLoadTag" => "2", 
			"idleReadChunkSize" => "2", 
			"importFileInto" => "2", 
			"in" => "2", 
			"inflate" => "2", 
			"ink" => "2", 
			"insertFrame" => "2", 
			"inside" => "2", 
			"installMenu" => "2", 
			"integer" => "2", 
			"integerP" => "2", 
			"interface" => "2", 
			"intersect" => "2", 
			"intersects" => "2", 
			"isPastCuePoint" => "2", 
			"item" => "2", 
			"itemDelimiter" => "2", 
			"key" => "2", 
			"keyboardFocusSprite" => "2", 
			"keyCode" => "2", 
			"keyDown" => "2", 
			"keyDownScript" => "2", 
			"keyPressed" => "2", 
			"keyUp" => "2", 
			"keyUpScript" => "2", 
			"label" => "2", 
			"labelList" => "2", 
			"last" => "2", 
			"lastChannel" => "2", 
			"lastClick" => "2", 
			"lastEvent" => "2", 
			"lastFrame" => "2", 
			"lastKey" => "2", 
			"lastRoll" => "2", 
			"left" => "2", 
			"length" => "2", 
			"line" => "2", 
			"lineCount" => "2", 
			"lineDirection" => "2", 
			"lineHeight" => "2", 
			"linePosToLocV" => "2", 
			"lines" => "2", 
			"lineSize" => "2", 
			"list" => "2", 
			"listP" => "2", 
			"loaded" => "2", 
			"loc" => "2", 
			"locH" => "2", 
			"locToCharPos" => "2", 
			"locV" => "2", 
			"locVToLinePos" => "2", 
			"locZ" => "2", 
			"log" => "2", 
			"long" => "2", 
			"loop" => "2", 
			"map" => "2", 
			"mapMemberToStage" => "2", 
			"mapStageToMember" => "2", 
			"margin" => "2", 
			"marker" => "2", 
			"max" => "2", 
			"maxInteger" => "2", 
			"mci" => "2", 
			"media" => "2", 
			"mediaReady" => "2", 
			"memberNum" => "2", 
			"members" => "2", 
			"memorySize" => "2", 
			"menu" => "2", 
			"milliseconds" => "2", 
			"min" => "2", 
			"modal" => "2", 
			"modified" => "2", 
			"mostRecentCuePoint" => "2", 
			"mouseChar" => "2", 
			"mouseDown" => "2", 
			"mouseDownScript" => "2", 
			"mouseItem" => "2", 
			"mouseLeave" => "2", 
			"mouseLine" => "2", 
			"mouseLoc" => "2", 
			"mouseMember" => "2", 
			"mouseUp" => "2", 
			"mouseUpOutside" => "2", 
			"mouseUpScript" => "2", 
			"mouseV" => "2", 
			"mouseWithin" => "2", 
			"mouseWord" => "2", 
			"move" => "2", 
			"moveableSprite" => "2", 
			"moveToBack" => "2", 
			"moveToFront" => "2", 
			"moveWindow" => "2", 
			"movie" => "2", 
			"movieAboutInfo" => "2", 
			"movieCopyrightInfo" => "2", 
			"movieFileFreeSize" => "2", 
			"movieFileSize" => "2", 
			"movieName" => "2", 
			"moviePath" => "2", 
			"movieRate" => "2", 
			"movieTime" => "2", 
			"movieXtraList" => "2", 
			"multiSound" => "2", 
			"name" => "2", 
			"netPresent" => "2", 
			"netThrottleTicks" => "2", 
			"next" => "2", 
			"nothing" => "2", 
			"number" => "2", 
			"numToChar" => "2", 
			"objectP" => "2", 
			"offset" => "2", 
			"open" => "2", 
			"openResFile" => "2", 
			"openWindow" => "2", 
			"openXlib" => "2", 
			"optionDown" => "2", 
			"organizationName" => "2", 
			"pageHeight" => "2", 
			"palette" => "2", 
			"paletteMapping" => "2", 
			"paletteRef" => "2", 
			"paragraph" => "2", 
			"param" => "2", 
			"paramCount" => "2", 
			"pasteClipBoardInto" => "2", 
			"pathName" => "2", 
			"pattern" => "2", 
			"pause" => "2", 
			"pausedAtStart" => "2", 
			"pauseState" => "2", 
			"PI" => "2", 
			"picture" => "2", 
			"pictureP" => "2", 
			"platform" => "2", 
			"play" => "2", 
			"playFile" => "2", 
			"playing" => "2", 
			"point" => "2", 
			"power" => "2", 
			"preLoad" => "2", 
			"preLoadEventAbort" => "2", 
			"preLoadMember" => "2", 
			"preLoadMode" => "2", 
			"preLoadMovie" => "2", 
			"preLoadRAM" => "2", 
			"prepareFrame" => "2", 
			"prepareMovie" => "2", 
			"previous" => "2", 
			"printFrom" => "2", 
			"puppet" => "2", 
			"puppetPalette" => "2", 
			"puppetSound" => "2", 
			"puppetSprite" => "2", 
			"puppetTempo" => "2", 
			"puppetTransition" => "2", 
			"purgePriority" => "2", 
			"quad" => "2", 
			"quit" => "2", 
			"QUOTE" => "2", 
			"ramNeeded" => "2", 
			"random" => "2", 
			"randomSeed" => "2", 
			"rect" => "2", 
			"regPoint" => "2", 
			"resizeWindow" => "2", 
			"restart" => "2", 
			"right" => "2", 
			"rightMouseDown" => "2", 
			"rightMouseUp" => "2", 
			"rollOver" => "2", 
			"romanLingo" => "2", 
			"rotation" => "2", 
			"runMode" => "2", 
			"runPropertyDialog" => "2", 
			"safePlayer" => "2", 
			"sampleRate" => "2", 
			"sampleSize" => "2", 
			"save" => "2", 
			"saveMovie" => "2", 
			"score" => "2", 
			"scoreColor" => "2", 
			"scoreSelection" => "2", 
			"script" => "2", 
			"scriptInstanceList" => "2", 
			"scriptNum" => "2", 
			"scriptsEnabled" => "2", 
			"scriptText" => "2", 
			"scriptType" => "2", 
			"scrollByLine" => "2", 
			"scrollByPage" => "2", 
			"scrollTop" => "2", 
			"searchCurrentFolder" => "2", 
			"searchPath" => "2", 
			"searchPaths" => "2", 
			"selection" => "2", 
			"selEnd" => "2", 
			"selStart" => "2", 
			"sendAllSprites" => "2", 
			"sendSprite" => "2", 
			"serialNumber" => "2", 
			"setaProp" => "2", 
			"setAt" => "2", 
			"setPref" => "2", 
			"setProp" => "2", 
			"setTrackEnabled" => "2", 
			"shapeType" => "2", 
			"shiftDown" => "2", 
			"short" => "2", 
			"showGlobals" => "2", 
			"showLocals" => "2", 
			"showResFile" => "2", 
			"showXlib" => "2", 
			"shutDown" => "2", 
			"sin" => "2", 
			"size" => "2", 
			"skew" => "2", 
			"sort" => "2", 
			"sound" => "2", 
			"soundBusy" => "2", 
			"soundDevice" => "2", 
			"soundDeviceList" => "2", 
			"soundEnabled" => "2", 
			"soundKeepDevice" => "2", 
			"soundLevel" => "2", 
			"sourceRect" => "2", 
			"SPACE" => "2", 
			"sprite" => "2", 
			"spriteNum" => "2", 
			"sqrt" => "2", 
			"stage" => "2", 
			"stageBottom" => "2", 
			"stageColor" => "2", 
			"stageLeft" => "2", 
			"stageRight" => "2", 
			"stageTop" => "2", 
			"startFrame" => "2", 
			"startMovie" => "2", 
			"startTime" => "2", 
			"startTimer" => "2", 
			"stepFrame" => "2", 
			"stillDown" => "2", 
			"stop" => "2", 
			"stopEvent" => "2", 
			"stopMovie" => "2", 
			"stopTime" => "2", 
			"string" => "2", 
			"stringP" => "2", 
			"switchColorDepth" => "2", 
			"symbol" => "2", 
			"symbolP" => "2", 
			"systemDate" => "2", 
			"TAB" => "2", 
			"tan" => "2", 
			"text" => "2", 
			"thumbnail" => "2", 
			"ticks" => "2", 
			"time" => "2", 
			"timeOut" => "2", 
			"timeoutKeyDown" => "2", 
			"timeoutLapsed" => "2", 
			"timeoutLength" => "2", 
			"timeoutMouse" => "2", 
			"timeoutPlay" => "2", 
			"timeoutScript" => "2", 
			"timer" => "2", 
			"timeScale" => "2", 
			"title" => "2", 
			"titleVisible" => "2", 
			"top" => "2", 
			"trace" => "2", 
			"traceLoad" => "2", 
			"traceLogFile" => "2", 
			"trackCount" => "2", 
			"trackEnabled" => "2", 
			"trackNextKeyTime" => "2", 
			"trackNextSampleTime" => "2", 
			"trackPreviousKeyTime" => "2", 
			"trackPreviousSampleTime" => "2", 
			"trackStartTime" => "2", 
			"trackStopTime" => "2", 
			"trackText" => "2", 
			"trails" => "2", 
			"transitionType" => "2", 
			"tweened" => "2", 
			"type" => "2", 
			"union" => "2", 
			"unLoad" => "2", 
			"unLoadMember" => "2", 
			"unloadMovie" => "2", 
			"updateFrame" => "2", 
			"updateLock" => "2", 
			"updateMovieEnabled" => "2", 
			"updateStage" => "2", 
			"useAlpha" => "2", 
			"userName" => "2", 
			"value" => "2", 
			"version" => "2", 
			"video" => "2", 
			"videoForWindowsPresent" => "2", 
			"visible" => "2", 
			"VOID" => "2", 
			"voidP" => "2", 
			"volume" => "2", 
			"width" => "2", 
			"window" => "2", 
			"windowList" => "2", 
			"windowPresent" => "2", 
			"windowType" => "2", 
			"word" => "2", 
			"wordWrap" => "2", 
			"xtra" => "2", 
			"xtraList" => "2", 
			"xtras" => "2", 
			"zoomBox" => "2", 
			"zoomWindow" => "2");

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

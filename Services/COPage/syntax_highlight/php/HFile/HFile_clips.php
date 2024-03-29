<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_clips extends HFile{
   function HFile_clips(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// 
/*************************************/
// Flags

$this->nocase            	= "0";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple", "gray", "brown");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array();
$this->unindent          	= array();

// String characters and delimiters

$this->stringchars       	= array();
$this->delimiters        	= array("~", "!", "@", "%", "^", "&", "*", "(", ")", "+", "=", "|", "\\", "/", "{", "}", "[", "]", ":", ";", "\"", "'", "<", ">", " ", ",", "	", ".", "?");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("");
$this->blockcommenton    	= array("");
$this->blockcommentoff   	= array("");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"/L10" => "", 
			"\"CLIPS\"" => "", 
			"Line" => "", 
			"Comment" => "", 
			"=" => "", 
			";" => "", 
			"File" => "", 
			"Extensions" => "", 
			"CLP" => "", 
			"and" => "1", 
			"bind" => "1", 
			"break" => "1", 
			"defglobal" => "1", 
			"deffunction" => "1", 
			"evenp" => "1", 
			"else" => "1", 
			"eq" => "1", 
			"floatpfloatp" => "1", 
			"FALSE" => "1", 
			"if" => "1", 
			"integerp" => "1", 
			"lexemep" => "1", 
			"loop-for-count" => "1", 
			"multifieldp" => "1", 
			"neq" => "1", 
			"not" => "1", 
			"numberp" => "1", 
			"or" => "1", 
			"oddp" => "1", 
			"pointerp" => "1", 
			"progn" => "1", 
			"progn$" => "1", 
			"return" => "1", 
			"stringp" => "1", 
			"switch" => "1", 
			"symbolp" => "1", 
			"then" => "1", 
			"TRUE" => "1", 
			"while" => "1", 
			"acos" => "2", 
			"acosh" => "2", 
			"acot" => "2", 
			"acoth" => "2", 
			"acsc" => "2", 
			"acsch" => "2", 
			"asec" => "2", 
			"asech" => "2", 
			"asin" => "2", 
			"asinh" => "2", 
			"atan" => "2", 
			"atanh" => "2", 
			"abs" => "2", 
			"assert" => "2", 
			"assert-string" => "2", 
			"build" => "2", 
			"create$" => "2", 
			"cos" => "2", 
			"cosh" => "2", 
			"cot" => "2", 
			"coth" => "2", 
			"csc" => "2", 
			"csch" => "2", 
			"close" => "2", 
			"call-next-method" => "2", 
			"call-specific-method" => "2", 
			"class" => "2", 
			"class-abstractp" => "2", 
			"class-existp" => "2", 
			"class-reactivep" => "2", 
			"class-slots" => "2", 
			"class-subclasses" => "2", 
			"class-superclasses" => "2", 
			"call-next-handler" => "2", 
			"delete$" => "2", 
			"deg-grad" => "2", 
			"deg-rad" => "2", 
			"div" => "2", 
			"defgeneric-module" => "2", 
			"duplicate" => "2", 
			"deftemplate-module" => "2", 
			"defrule-module" => "2", 
			"defglobal-module" => "2", 
			"deffunction-module" => "2", 
			"dynamic-get" => "2", 
			"dynamic-put" => "2", 
			"direct-slot-delete$" => "2", 
			"direct-slot-insert$" => "2", 
			"direct-slot-replace$" => "2", 
			"defclass-module" => "2", 
			"definstances-module" => "2", 
			"delete-instance" => "2", 
			"eval" => "2", 
			"explode$" => "2", 
			"exp" => "2", 
			"expand$" => "2", 
			"first$" => "2", 
			"float" => "2", 
			"format" => "2", 
			"fact-index" => "2", 
			"get-sequence-operator-recognition" => "2", 
			"gensym" => "2", 
			"get-function_restrictions" => "2", 
			"grad-deg" => "2", 
			"get-defgeneric-list" => "2", 
			"get-defmethod-list" => "2", 
			"get-method-restrictions" => "2", 
			"get-deftemplate-list" => "2", 
			"get-defrule-list" => "2", 
			"get-defmodule-list" => "2", 
			"get-defglobal-list" => "2", 
			"get-deffunction-list" => "2", 
			"get-focus" => "2", 
			"get-focus-stack" => "2", 
			"get-defclass-list" => "2", 
			"get-defmessage-handler-list" => "2", 
			"get-definstances-list" => "2", 
			"implode$" => "2", 
			"insert$" => "2", 
			"integer" => "2", 
			"instance-addressp" => "2", 
			"instance-existp" => "2", 
			"instance-namep" => "2", 
			"instancep" => "2", 
			"init-slots" => "2", 
			"instance-address" => "2", 
			"instance-name" => "2", 
			"instance-name-to-symbol" => "2", 
			"lowcase" => "2", 
			"length$" => "2", 
			"length" => "2", 
			"log" => "2", 
			"log10" => "2", 
			"member$" => "2", 
			"mod" => "2", 
			"max" => "2", 
			"min" => "2", 
			"modify" => "2", 
			"message-handler-existp" => "2", 
			"nth$" => "2", 
			"next-handlerp" => "2", 
			"next-methodp" => "2", 
			"open" => "2", 
			"override-next-handler" => "2", 
			"override-next-method" => "2", 
			"pi" => "2", 
			"printout" => "2", 
			"pop-focus" => "2", 
			"replace$" => "2", 
			"rest$" => "2", 
			"random" => "2", 
			"rad-deg" => "2", 
			"round" => "2", 
			"read" => "2", 
			"readline" => "2", 
			"remove" => "2", 
			"rename" => "2", 
			"retract" => "2", 
			"str-length" => "2", 
			"sub-string" => "2", 
			"sym-cat" => "2", 
			"str-cat" => "2", 
			"str-compare" => "2", 
			"str-index" => "2", 
			"set-sequence-operator-recognition" => "2", 
			"subseq$" => "2", 
			"subsetp" => "2", 
			"seed" => "2", 
			"setgen" => "2", 
			"sec" => "2", 
			"sech" => "2", 
			"sin" => "2", 
			"sinh" => "2", 
			"sqrt" => "2", 
			"slot-delete$" => "2", 
			"slot-insert$" => "2", 
			"slot-replace$" => "2", 
			"symbol-to-instance-name" => "2", 
			"slot-allowed-values" => "2", 
			"slot-cardinality" => "2", 
			"slot-direct-accessp" => "2", 
			"slot-existp" => "2", 
			"slot-facets" => "2", 
			"slot-initablep" => "2", 
			"slot-publicp" => "2", 
			"slot-range" => "2", 
			"slot-sources" => "2", 
			"slot-types" => "2", 
			"slot-writablep" => "2", 
			"subclassp" => "2", 
			"superclassp" => "2", 
			"time" => "2", 
			"tan" => "2", 
			"tanh" => "2", 
			"type" => "2", 
			"upcase" => "2", 
			"unmake-instance" => "2", 
			"app-create" => "3", 
			"app-get-show-frame-on-init" => "3", 
			"app-on-init" => "3", 
			"app-set-show-frame-on-init" => "3", 
			"arc-annotation-get-name" => "3", 
			"arc-image-change-attachment" => "3", 
			"arc-image-control-point-add" => "3", 
			"arc-image-control-point-count" => "3", 
			"arc-image-control-point-move" => "3", 
			"arc-image-control-point-remove" => "3", 
			"arc-image-control-point-x" => "3", 
			"arc-image-control-point-y" => "3", 
			"arc-image-create" => "3", 
			"arc-image-get-alignment-type" => "3", 
			"arc-image-get-attachment-from" => "3", 
			"arc-image-get-attachment-to" => "3", 
			"arc-image-get-image-from" => "3", 
			"arc-image-get-image-to" => "3", 
			"arc-image-is-leg" => "3", 
			"arc-image-is-spline" => "3", 
			"arc-image-is-stem" => "3", 
			"arc-image-set-alignment-type" => "3", 
			"arc-image-set-spline" => "3", 
			"batch" => "3", 
			"begin-busy-cursor" => "3", 
			"bell" => "3", 
			"brush-create" => "3", 
			"brush-delete" => "3", 
			"button-create" => "3", 
			"button-create-from-bitmap" => "3", 
			"bitmap-create" => "3", 
			"bitmap-delete" => "3", 
			"bitmap-get-colourmap" => "3", 
			"bitmap-get-height" => "3", 
			"bitmap-get-width" => "3", 
			"bitmap-load-from-file" => "3", 
			"canvas-scroll" => "3", 
			"canvas-set-scroll-page-y" => "3", 
			"canvas-set-scroll-pos-x" => "3", 
			"canvas-set-scroll-pos-y" => "3", 
			"canvas-set-scroll-range-x" => "3", 
			"canvas-set-scroll-range-y" => "3", 
			"canvas-view-start-x" => "3", 
			"canvas-view-start-y" => "3", 
			"card-create" => "3", 
			"card-delete" => "3", 
			"card-deselect-all" => "3", 
			"card-find-by-title" => "3", 
			"card-get-canvas" => "3", 
			"card-get-first-item" => "3", 
			"card-get-frame" => "3", 
			"card-get-height" => "3", 
			"card-get-next-item" => "3", 
			"card-get-special-item" => "3", 
			"card-get-string-attribute" => "3", 
			"card-get-toolbar" => "3", 
			"card-get-width" => "3", 
			"card-get-x" => "3", 
			"card-get-y" => "3", 
			"card-iconize" => "3", 
			"card-is-modified" => "3", 
			"card-is-shown" => "3", 
			"card-is-valid" => "3", 
			"card-move" => "3", 
			"card-quit" => "3", 
			"card-select-all" => "3", 
			"card-send-command" => "3", 
			"card-set-icon" => "3", 
			"card-set-modified" => "3", 
			"card-set-status-text" => "3", 
			"card-set-string-attribute" => "3", 
			"card-show" => "3", 
			"chdir" => "3", 
			"clean-windows" => "3", 
			"clear-ide-window" => "3", 
			"clear-resources" => "3", 
			"copy-file" => "3", 
			"cursor-create" => "3", 
			"cursor-delete" => "3", 
			"cursor-load-from-file" => "3", 
			"connection-advise" => "3", 
			"connection-execute" => "3", 
			"connection-disconnect" => "3", 
			"connection-poke" => "3", 
			"connection-request" => "3", 
			"connection-start-advise" => "3", 
			"connection-stop-advise" => "3", 
			"client-create" => "3", 
			"colour-create" => "3", 
			"colour-red" => "3", 
			"colour-green" => "3", 
			"colour-blue" => "3", 
			"client-make-connection" => "3", 
			"choice-create" => "3", 
			"choice-append" => "3", 
			"choice-find-string" => "3", 
			"choice-clear" => "3", 
			"choice-get-selection" => "3", 
			"choice-get-string-selection" => "3", 
			"choice-set-selection" => "3", 
			"choice-set-string-selection" => "3", 
			"choice-get-string" => "3", 
			"check-box-create" => "3", 
			"check-box-set-value" => "3", 
			"check-box-get-value" => "3", 
			"canvas-create" => "3", 
			"canvas-get-dc" => "3", 
			"canvas-get-scroll-page-x" => "3", 
			"canvas-get-scroll-page-y" => "3", 
			"canvas-get-scroll-pos-x" => "3", 
			"canvas-get-scroll-pos-y" => "3", 
			"canvas-get-scroll-range-x" => "3", 
			"canvas-get-scroll-range-y" => "3", 
			"canvas-get-scroll-pixels-per-unit-x" => "3", 
			"canvas-on-char" => "3", 
			"canvas-on-scroll" => "3", 
			"canvas-set-scrollbars" => "3", 
			"canvas-set-scroll-page-x" => "3", 
			"container-region-add-node-image" => "3", 
			"container-region-remove-node-image" => "3", 
			"convert-bitmap-to-rtf" => "3", 
			"convert-metafile-to-rtf" => "3", 
			"database-close" => "3", 
			"database-create" => "3", 
			"database-delete" => "3", 
			"database-error-occurred" => "3", 
			"database-get-data-source" => "3", 
			"database-get-database-name" => "3", 
			"database-get-error-code" => "3", 
			"database-get-error-message" => "3", 
			"database-get-error-number" => "3", 
			"database-is-open" => "3", 
			"database-open" => "3", 
			"date-add-days" => "3", 
			"date-add-months" => "3", 
			"date-add-self" => "3", 
			"date-add-weeks" => "3", 
			"date-add-years" => "3", 
			"date-create" => "3", 
			"date-create-julian" => "3", 
			"date-create-string" => "3", 
			"date-delete" => "3", 
			"date-eq" => "3", 
			"date-format" => "3", 
			"date-ge" => "3", 
			"date-geq" => "3", 
			"date-get-day" => "3", 
			"date-get-day-of-week" => "3", 
			"date-get-day-of-week-name" => "3", 
			"date-get-day-of-year" => "3", 
			"date-get-days-in-month" => "3", 
			"date-get-first-day-of-month" => "3", 
			"date-get-julian-date" => "3", 
			"date-get-month" => "3", 
			"date-get-month-end" => "3", 
			"date-get-month-name" => "3", 
			"date-get-month-start" => "3", 
			"date-get-week-of-month" => "3", 
			"date-get-week-of-year" => "3", 
			"date-get-year" => "3", 
			"date-get-year-end" => "3", 
			"date-get-year-start" => "3", 
			"date-is-leap-year" => "3", 
			"date-l" => "3", 
			"date-leq" => "3", 
			"date-neq" => "3", 
			"date-set-current-date" => "3", 
			"date-set-date" => "3", 
			"date-set-format" => "3", 
			"date-set-julian" => "3", 
			"date-set-option" => "3", 
			"date-subtract" => "3", 
			"date-subtract-days" => "3", 
			"date-subtract-self" => "3", 
			"dc-begin-drawing" => "3", 
			"dc-blit" => "3", 
			"dc-clear" => "3", 
			"dc-delete" => "3", 
			"dc-destroy-clipping-region" => "3", 
			"dc-draw-ellipse" => "3", 
			"dc-draw-line" => "3", 
			"dc-draw-lines" => "3", 
			"dc-draw-point" => "3", 
			"dc-draw-polygon" => "3", 
			"dc-draw-rectangle" => "3", 
			"dc-draw-rounded-rectangle" => "3", 
			"dc-draw-spline" => "3", 
			"dc-draw-text" => "3", 
			"dc-end-doc" => "3", 
			"dc-end-drawing" => "3", 
			"dc-end-page" => "3", 
			"dc-get-max-x" => "3", 
			"dc-get-max-y" => "3", 
			"dc-get-min-x" => "3", 
			"dc-get-min-y" => "3", 
			"dc-get-text-extent-height" => "3", 
			"dc-get-text-extent-width" => "3", 
			"dc-ok" => "3", 
			"dc-set-background" => "3", 
			"dc-set-background-mode" => "3", 
			"dc-set-brush" => "3", 
			"dc-set-clipping-region" => "3", 
			"dc-set-colourmap" => "3", 
			"dc-set-font" => "3", 
			"dc-set-logical-function" => "3", 
			"dc-set-pen" => "3", 
			"dc-set-text-background" => "3", 
			"dc-set-text-foreground" => "3", 
			"dc-start-doc" => "3", 
			"dc-start-page" => "3", 
			"dde-advise-global" => "3", 
			"debug-msg" => "3", 
			"diagram-card-clear-canvas" => "3", 
			"diagram-card-copy" => "3", 
			"diagram-card-create" => "3", 
			"diagram-card-create-expansion" => "3", 
			"diagram-card-cut" => "3", 
			"diagram-card-delete-all-images" => "3", 
			"diagram-card-find-root" => "3", 
			"diagram-card-get-first-arc-image" => "3", 
			"diagram-card-get-first-arc-object" => "3", 
			"diagram-card-get-first-descendant" => "3", 
			"diagram-card-get-first-node-image" => "3", 
			"diagram-card-get-first-node-object" => "3", 
			"diagram-card-get-grid-spacing" => "3", 
			"diagram-card-get-next-arc-image" => "3", 
			"diagram-card-get-next-arc-object" => "3", 
			"diagram-card-get-next-descendant" => "3", 
			"diagram-card-get-next-node-image" => "3", 
			"diagram-card-get-next-node-object" => "3", 
			"diagram-card-get-parent-card" => "3", 
			"diagram-card-get-parent-image" => "3", 
			"diagram-card-get-print-height" => "3", 
			"diagram-card-get-print-width" => "3", 
			"diagram-card-get-scale" => "3", 
			"diagram-card-layout-graph" => "3", 
			"diagram-card-layout-tree" => "3", 
			"diagram-card-load-file" => "3", 
			"diagram-card-paste" => "3", 
			"diagram-card-popup-menu" => "3", 
			"diagram-card-print-hierarchy" => "3", 
			"diagram-card-redraw" => "3", 
			"diagram-card-save-bitmap" => "3", 
			"diagram-card-save-file" => "3", 
			"diagram-card-save-metafile" => "3", 
			"diagram-card-set-grid-spacing" => "3", 
			"diagram-card-set-layout-parameters" => "3", 
			"diagram-card-set-scale" => "3", 
			"diagram-image-add-annotation" => "3", 
			"diagram-image-annotation-get-drop-site" => "3", 
			"diagram-image-annotation-get-logical-name" => "3", 
			"diagram-image-annotation-get-name" => "3", 
			"diagram-image-delete" => "3", 
			"diagram-image-delete-annotation" => "3", 
			"diagram-image-draw" => "3", 
			"diagram-image-draw-text" => "3", 
			"diagram-image-erase" => "3", 
			"diagram-image-get-brush-colour" => "3", 
			"diagram-image-get-card" => "3", 
			"diagram-image-get-first-annotation" => "3", 
			"diagram-image-get-first-expansion" => "3", 
			"diagram-image-get-height" => "3", 
			"diagram-image-get-item" => "3", 
			"diagram-image-get-next-annotation" => "3", 
			"diagram-image-get-next-expansion" => "3", 
			"diagram-image-get-object" => "3", 
			"diagram-image-get-pen-colour" => "3", 
			"diagram-image-get-text-colour" => "3", 
			"diagram-image-get-width" => "3", 
			"diagram-image-get-x" => "3", 
			"diagram-image-get-y" => "3", 
			"diagram-image-is-shown" => "3", 
			"diagram-image-move" => "3", 
			"diagram-image-pending-delete" => "3", 
			"diagram-image-put-to-front" => "3", 
			"diagram-image-resize" => "3", 
			"diagram-image-select" => "3", 
			"diagram-image-selected" => "3", 
			"diagram-image-set-brush-colour" => "3", 
			"diagram-image-set-pen-colour" => "3", 
			"diagram-image-set-shadow-mode" => "3", 
			"diagram-image-set-text-colour" => "3", 
			"diagram-image-show" => "3", 
			"diagram-item-get-image" => "3", 
			"diagram-object-add-attribute" => "3", 
			"diagram-object-delete-attribute" => "3", 
			"diagram-object-format-text" => "3", 
			"diagram-object-get-first-attribute" => "3", 
			"diagram-object-get-first-image" => "3", 
			"diagram-object-get-next-attribute" => "3", 
			"diagram-object-get-next-image" => "3", 
			"diagram-object-get-string-attribute" => "3", 
			"diagram-object-set-format-string" => "3", 
			"diagram-object-set-string-attribute" => "3", 
			"diagram-palette-get-arc-selection" => "3", 
			"diagram-palette-get-arc-selection-image" => "3", 
			"diagram-palette-get-first-annotation-selection" => "3", 
			"diagram-palette-get-next-annotation-selection" => "3", 
			"diagram-palette-get-node-selection" => "3", 
			"diagram-palette-set-annotation-selection" => "3", 
			"diagram-palette-set-arc-selection" => "3", 
			"diagram-palette-set-node-selection" => "3", 
			"diagram-palette-show" => "3", 
			"dialog-box-create" => "3", 
			"dialog-box-create-from-resource" => "3", 
			"dialog-box-is-modal" => "3", 
			"dialog-box-set-modal" => "3", 
			"dir-exists" => "3", 
			"end-busy-cursor" => "3", 
			"event-get-event-type" => "3", 
			"execute" => "3", 
			"file-exists" => "3", 
			"file-selector" => "3", 
			"find-window-by-label" => "3", 
			"find-window-by-name" => "3", 
			"float-to-string" => "3", 
			"font-create" => "3", 
			"font-delete" => "3", 
			"frame-create" => "3", 
			"frame-create-status-line" => "3", 
			"frame-iconize" => "3", 
			"frame-on-size" => "3", 
			"frame-set-icon" => "3", 
			"frame-set-menu-bar" => "3", 
			"frame-set-status-text" => "3", 
			"frame-set-title" => "3", 
			"frame-set-tool-bar" => "3", 
			"gauge-create" => "3", 
			"gauge-set-bezel-face" => "3", 
			"gauge-set-shadow-width" => "3", 
			"gauge-set-value" => "3", 
			"get-active-window" => "3", 
			"get-choice" => "3", 
			"get-elapsed-time" => "3", 
			"get-ide-window" => "3", 
			"get-os-version" => "3", 
			"get-platform" => "3", 
			"get-resource" => "3", 
			"get-text-from-user" => "3", 
			"grid-adjust-scrollbars" => "3", 
			"grid-append-cols" => "3", 
			"grid-append-rows" => "3", 
			"grid-clear-grid" => "3", 
			"grid-create" => "3", 
			"grid-create-grid" => "3", 
			"grid-delete-cols" => "3", 
			"grid-delete-rows" => "3", 
			"grid-get-cell-alignment" => "3", 
			"grid-get-cell-background-colour" => "3", 
			"grid-get-cell-bitmap" => "3", 
			"grid-get-cell-text-colour" => "3", 
			"grid-get-cell-value" => "3", 
			"grid-get-cols" => "3", 
			"grid-get-column-width" => "3", 
			"grid-get-cursor-column" => "3", 
			"grid-get-cursor-row" => "3", 
			"grid-get-editable" => "3", 
			"grid-get-label-alignment" => "3", 
			"grid-get-label-background-colour" => "3", 
			"grid-get-label-size" => "3", 
			"grid-get-label-text-colour" => "3", 
			"grid-get-label-value" => "3", 
			"grid-get-row-height" => "3", 
			"grid-get-rows" => "3", 
			"grid-get-scroll-pos-x" => "3", 
			"grid-get-scroll-pos-y" => "3", 
			"grid-get-text-item" => "3", 
			"grid-insert-cols" => "3", 
			"grid-insert-rows" => "3", 
			"grid-on-activate" => "3", 
			"grid-on-paint" => "3", 
			"grid-on-size" => "3", 
			"grid-set-cell-alignment" => "3", 
			"grid-set-cell-background-colour" => "3", 
			"grid-set-cell-bitmap" => "3", 
			"grid-set-cell-text-colour" => "3", 
			"grid-set-cell-text-font" => "3", 
			"grid-set-cell-value" => "3", 
			"grid-set-column-width" => "3", 
			"grid-set-divider-pen" => "3", 
			"grid-set-editable" => "3", 
			"grid-set-grid-cursor" => "3", 
			"grid-set-label-alignment" => "3", 
			"grid-set-label-background-colour" => "3", 
			"grid-set-label-size" => "3", 
			"grid-set-label-text-colour" => "3", 
			"grid-set-label-text-font" => "3", 
			"grid-set-label-value" => "3", 
			"grid-set-row-height" => "3", 
			"grid-update-dimensions" => "3", 
			"group-box-create" => "3", 
			"hardy-clear-index" => "3", 
			"hardy-command-int-to-string" => "3", 
			"hardy-command-string-to-int" => "3", 
			"hardy-diagram-definition-get-first-arc-type" => "3", 
			"hardy-diagram-definition-get-first-node-type" => "3", 
			"hardy-diagram-definition-get-next-arc-type" => "3", 
			"hardy-diagram-definition-get-next-node-type" => "3", 
			"hardy-get-browser-frame" => "3", 
			"hardy-get-first-card" => "3", 
			"hardy-get-first-diagram-definition" => "3", 
			"hardy-get-next-card" => "3", 
			"hardy-get-next-diagram-definition" => "3", 
			"hardy-get-top-card" => "3", 
			"hardy-get-top-level-frame" => "3", 
			"hardy-get-version" => "3", 
			"hardy-help-display-block" => "3", 
			"hardy-help-display-contents" => "3", 
			"hardy-help-display-section" => "3", 
			"hardy-help-keyword-search" => "3", 
			"hardy-help-load-file" => "3", 
			"hardy-load-index" => "3", 
			"hardy-path-search" => "3", 
			"hardy-preview-all" => "3", 
			"hardy-preview-diagram-card" => "3", 
			"hardy-print-all" => "3", 
			"hardy-print-diagram-card" => "3", 
			"hardy-print-diagram-in-box" => "3", 
			"hardy-print-diagram-page" => "3", 
			"hardy-print-get-header-footer" => "3", 
			"hardy-print-get-info" => "3", 
			"hardy-print-header-footer" => "3", 
			"hardy-print-set-header-footer" => "3", 
			"hardy-print-set-info" => "3", 
			"hardy-print-set-title" => "3", 
			"hardy-print-text-in-box" => "3", 
			"hardy-save-index" => "3", 
			"hardy-send-command" => "3", 
			"hardy-set-about-string" => "3", 
			"hardy-set-author" => "3", 
			"hardy-set-custom-help-file" => "3", 
			"hardy-set-help-file" => "3", 
			"hardy-set-name" => "3", 
			"hardy-set-title" => "3", 
			"help-create" => "3", 
			"help-delete" => "3", 
			"help-display-block" => "3", 
			"help-display-contents" => "3", 
			"help-display-section" => "3", 
			"help-keyword-search" => "3", 
			"help-load-file" => "3", 
			"html-back" => "3", 
			"html-cancel" => "3", 
			"html-clear-cache" => "3", 
			"html-create" => "3", 
			"html-get-current-url" => "3", 
			"html-on-size" => "3", 
			"html-open-file" => "3", 
			"html-open-url" => "3", 
			"html-resize" => "3", 
			"html-save-file" => "3", 
			"hwnd-find" => "3", 
			"hwnd-iconize" => "3", 
			"hwnd-move" => "3", 
			"hwnd-quit" => "3", 
			"hwnd-refresh" => "3", 
			"hwnd-send-message" => "3", 
			"hwnd-show" => "3", 
			"hypertext-block-add" => "3", 
			"hypertext-block-clear" => "3", 
			"hypertext-block-get-item" => "3", 
			"hypertext-block-get-text" => "3", 
			"hypertext-block-get-type" => "3", 
			"hypertext-block-selected" => "3", 
			"hypertext-block-set-type" => "3", 
			"hypertext-card-create" => "3", 
			"hypertext-card-get-current-char" => "3", 
			"hypertext-card-get-current-line" => "3", 
			"hypertext-card-get-first-selection" => "3", 
			"hypertext-card-get-line-length" => "3", 
			"hypertext-card-get-next-selection" => "3", 
			"hypertext-card-get-no-lines" => "3", 
			"hypertext-card-get-offset-position" => "3", 
			"hypertext-card-get-span-text" => "3", 
			"hypertext-card-insert-text" => "3", 
			"hypertext-card-load-file" => "3", 
			"hypertext-card-save-file" => "3", 
			"hypertext-card-string-search" => "3", 
			"hypertext-card-translate" => "3", 
			"hypertext-card-translator-close-file" => "3", 
			"hypertext-card-translator-open-file" => "3", 
			"hypertext-card-translator-output" => "3", 
			"hypertext-item-get-block" => "3", 
			"icon-create" => "3", 
			"icon-delete" => "3", 
			"icon-get-height" => "3", 
			"icon-get-width" => "3", 
			"icon-load-from-file" => "3", 
			"instance-table-add-entry" => "3", 
			"instance-table-delete-entry" => "3", 
			"instance-table-get-instance" => "3", 
			"item-get-first-link" => "3", 
			"item-get-kind" => "3", 
			"item-get-next-link" => "3", 
			"item-get-type" => "3", 
			"item-goto" => "3", 
			"item-set-kind" => "3", 
			"key-event-alt-down" => "3", 
			"key-event-control-down" => "3", 
			"key-event-get-key-code" => "3", 
			"key-event-position-x" => "3", 
			"key-event-position-y" => "3", 
			"key-event-shift-down" => "3", 
			"link-cards" => "3", 
			"link-get-card-from" => "3", 
			"link-get-card-to" => "3", 
			"link-get-item-from" => "3", 
			"link-get-item-to" => "3", 
			"link-get-kind" => "3", 
			"link-get-type" => "3", 
			"link-items" => "3", 
			"link-set-kind" => "3", 
			"list-box-append" => "3", 
			"list-box-clear" => "3", 
			"list-box-create" => "3", 
			"list-box-delete" => "3", 
			"list-box-find-string" => "3", 
			"list-box-get-first-selection" => "3", 
			"list-box-get-next-selection" => "3", 
			"list-box-get-selection" => "3", 
			"list-box-get-string" => "3", 
			"list-box-get-string-selection" => "3", 
			"list-box-number" => "3", 
			"list-box-set-selection" => "3", 
			"list-box-set-string-selection" => "3", 
			"load-resource-file" => "3", 
			"long-to-string" => "3", 
			"make-metafile-placeable" => "3", 
			"mci-send-string" => "3", 
			"media-block-create" => "3", 
			"media-block-get-item" => "3", 
			"media-block-get-position" => "3", 
			"media-block-get-type" => "3", 
			"media-block-set-type" => "3", 
			"media-card-append-text" => "3", 
			"media-card-apply-family" => "3", 
			"media-card-apply-foreground-colour" => "3", 
			"media-card-apply-point-size" => "3", 
			"media-card-apply-style" => "3", 
			"media-card-apply-underline" => "3", 
			"media-card-apply-weight" => "3", 
			"media-card-clear" => "3", 
			"media-card-clear-all-blocks" => "3", 
			"media-card-copy" => "3", 
			"media-card-create" => "3", 
			"media-card-cut" => "3", 
			"media-card-delete" => "3", 
			"media-card-find-string" => "3", 
			"media-card-get-character" => "3", 
			"media-card-get-first-block" => "3", 
			"media-card-get-last-position" => "3", 
			"media-card-get-line-for-position" => "3", 
			"media-card-get-line-length" => "3", 
			"media-card-get-next-block" => "3", 
			"media-card-get-number-of-lines" => "3", 
			"media-card-get-position-for-line" => "3", 
			"media-card-get-selection-end" => "3", 
			"media-card-get-selection-start" => "3", 
			"media-card-get-text" => "3", 
			"media-card-insert-image" => "3", 
			"media-card-insert-text" => "3", 
			"media-card-load-file" => "3", 
			"media-card-paste" => "3", 
			"media-card-redo" => "3", 
			"media-card-save-file" => "3", 
			"media-card-scroll-to-position" => "3", 
			"media-card-select-block" => "3", 
			"media-card-set-selection" => "3", 
			"media-card-undo" => "3", 
			"media-item-get-block" => "3", 
			"memory-dc-create" => "3", 
			"memory-dc-select-object" => "3", 
			"menu-append" => "3", 
			"menu-append-separator" => "3", 
			"menu-bar-append" => "3", 
			"menu-bar-check" => "3", 
			"menu-bar-checked" => "3", 
			"menu-bar-create" => "3", 
			"menu-bar-enable" => "3", 
			"menu-break" => "3", 
			"menu-check" => "3", 
			"menu-create" => "3", 
			"menu-enable" => "3", 
			"message-box" => "3", 
			"message-create" => "3", 
			"message-create-from-bitmap" => "3", 
			"metafile-dc-close" => "3", 
			"metafile-dc-create" => "3", 
			"metafile-delete" => "3", 
			"metafile-set-clipboard" => "3", 
			"mkdir" => "3", 
			"mouse-event-button" => "3", 
			"mouse-event-button-down" => "3", 
			"mouse-event-control-down" => "3", 
			"mouse-event-dragging" => "3", 
			"mouse-event-is-button" => "3", 
			"mouse-event-left-down" => "3", 
			"mouse-event-left-up" => "3", 
			"mouse-event-middle-down" => "3", 
			"mouse-event-middle-up" => "3", 
			"mouse-event-position-x" => "3", 
			"mouse-event-position-y" => "3", 
			"mouse-event-right-down" => "3", 
			"mouse-event-right-up" => "3", 
			"mouse-event-shift-down" => "3", 
			"multi-text-copy" => "3", 
			"multi-text-create" => "3", 
			"multi-text-cut" => "3", 
			"multi-text-get-insertion-point" => "3", 
			"multi-text-get-last-position" => "3", 
			"multi-text-get-line-length" => "3", 
			"multi-text-get-number-of-lines" => "3", 
			"multi-text-get-value" => "3", 
			"multi-text-paste" => "3", 
			"multi-text-position-to-char" => "3", 
			"multi-text-position-to-line" => "3", 
			"multi-text-remove" => "3", 
			"multi-text-replace" => "3", 
			"multi-text-set-insertion-point" => "3", 
			"multi-text-set-selection" => "3", 
			"multi-text-set-value" => "3", 
			"multi-text-show-position" => "3", 
			"multi-text-write" => "3", 
			"multi-text-xy-to-position" => "3", 
			"node-image-create" => "3", 
			"node-image-duplicate" => "3", 
			"node-image-get-container" => "3", 
			"node-image-get-container-parent" => "3", 
			"node-image-get-first-arc-image" => "3", 
			"node-image-get-first-child" => "3", 
			"node-image-get-first-container-region" => "3", 
			"node-image-get-next-arc-image" => "3", 
			"node-image-get-next-child" => "3", 
			"node-image-get-next-container-region" => "3", 
			"node-image-get-parent" => "3", 
			"node-image-is-composite" => "3", 
			"node-image-is-container" => "3", 
			"node-image-is-junction" => "3", 
			"node-image-order-arcs" => "3", 
			"node-object-get-first-arc-object" => "3", 
			"node-object-get-next-arc-object" => "3", 
			"now" => "3", 
			"object-get-type" => "3", 
			"object-is-valid" => "3", 
			"object-type-get-first-attribute-name" => "3", 
			"object-type-get-next-attribute-name" => "3", 
			"panel-create" => "3", 
			"panel-create-from-resource" => "3", 
			"panel-item-get-command-event" => "3", 
			"panel-item-get-label" => "3", 
			"panel-item-set-default" => "3", 
			"panel-item-set-label" => "3", 
			"panel-new-line" => "3", 
			"panel-set-button-font" => "3", 
			"panel-set-label-font" => "3", 
			"panel-set-label-position" => "3", 
			"pen-create" => "3", 
			"pen-delete" => "3", 
			"postscript-dc-create" => "3", 
			"printer-dc-create" => "3", 
			"quit" => "3", 
			"radio-box-create" => "3", 
			"radio-box-get-selection" => "3", 
			"radio-box-set-selection" => "3", 
			"read-string" => "3", 
			"recordset-create" => "3", 
			"recordset-delete" => "3", 
			"recordset-execute-sql" => "3", 
			"recordset-get-char-data" => "3", 
			"recordset-get-col-name" => "3", 
			"recordset-get-col-type" => "3", 
			"recordset-get-columns" => "3", 
			"recordset-get-data-sources" => "3", 
			"recordset-get-database" => "3", 
			"recordset-get-error-code" => "3", 
			"recordset-get-filter" => "3", 
			"recordset-get-float-data" => "3", 
			"recordset-get-foreign-keys" => "3", 
			"recordset-get-int-data" => "3", 
			"recordset-get-number-cols" => "3", 
			"recordset-get-number-fields" => "3", 
			"recordset-get-number-params" => "3", 
			"recordset-get-number-records" => "3", 
			"recordset-get-primary-keys" => "3", 
			"recordset-get-result-set" => "3", 
			"recordset-get-table-name" => "3", 
			"recordset-get-tables" => "3", 
			"recordset-goto" => "3", 
			"recordset-is-bof" => "3", 
			"recordset-is-col-nullable" => "3", 
			"recordset-is-eof" => "3", 
			"recordset-is-field-dirty" => "3", 
			"recordset-is-field-null" => "3", 
			"recordset-is-open" => "3", 
			"recordset-move" => "3", 
			"recordset-move-first" => "3", 
			"recordset-move-last" => "3", 
			"recordset-move-next" => "3", 
			"recordset-move-prev" => "3", 
			"recordset-query" => "3", 
			"recordset-set-table-name" => "3", 
			"register-event-handler" => "3", 
			"return-result" => "3", 
			"rmdir" => "3", 
			"server-create" => "3", 
			"set-work-proc" => "3", 
			"show-ide-window" => "3", 
			"sleep" => "3", 
			"slider-create" => "3", 
			"slider-get-value" => "3", 
			"slider-set-value" => "3", 
			"start-timer" => "3", 
			"string-sort" => "3", 
			"string-to-float" => "3", 
			"string-to-long" => "3", 
			"string-to-symbol" => "3", 
			"symbol-to-string" => "3", 
			"text-card-load-file" => "3", 
			"text-create" => "3", 
			"text-get-value" => "3", 
			"text-set-value" => "3", 
			"text-window-clear" => "3", 
			"text-window-copy" => "3", 
			"text-window-create" => "3", 
			"text-window-cut" => "3", 
			"text-window-discard-edits" => "3", 
			"text-window-get-contents" => "3", 
			"text-window-get-insertion-point" => "3", 
			"text-window-get-last-position" => "3", 
			"text-window-get-line-length" => "3", 
			"text-window-get-number-of-lines" => "3", 
			"text-window-load-file" => "3", 
			"text-window-modified" => "3", 
			"text-window-on-char" => "3", 
			"text-window-paste" => "3", 
			"text-window-position-to-char" => "3", 
			"text-window-position-to-line" => "3", 
			"text-window-remove" => "3", 
			"text-window-replace" => "3", 
			"text-window-save-file" => "3", 
			"text-window-set-editable" => "3", 
			"text-window-set-insertion-point" => "3", 
			"text-window-set-selection" => "3", 
			"text-window-show-position" => "3", 
			"text-window-write" => "3", 
			"text-window-xy-to-position" => "3", 
			"timer-create" => "3", 
			"timer-delete" => "3", 
			"timer-start" => "3", 
			"timer-stop" => "3", 
			"toolbar-add-separator" => "3", 
			"toolbar-add-tool" => "3", 
			"toolbar-clear-tools" => "3", 
			"toolbar-create" => "3", 
			"toolbar-create-tools" => "3", 
			"toolbar-enable-tool" => "3", 
			"toolbar-get-max-height" => "3", 
			"toolbar-get-max-width" => "3", 
			"toolbar-get-tool-client-data" => "3", 
			"toolbar-get-tool-enabled" => "3", 
			"toolbar-get-tool-long-help" => "3", 
			"toolbar-get-tool-short-help" => "3", 
			"toolbar-get-tool-state" => "3", 
			"toolbar-layout" => "3", 
			"toolbar-on-paint" => "3", 
			"toolbar-set-default-size" => "3", 
			"toolbar-set-margins" => "3", 
			"toolbar-set-tool-long-help" => "3", 
			"toolbar-set-tool-short-help" => "3", 
			"toolbar-toggle-tool" => "3", 
			"window-add-callback" => "3", 
			"window-centre" => "3", 
			"window-close" => "3", 
			"window-delete" => "3", 
			"window-enable" => "3", 
			"window-fit" => "3", 
			"window-get-client-height" => "3", 
			"window-get-client-width" => "3", 
			"window-get-height" => "3", 
			"window-get-name" => "3", 
			"window-get-next-child" => "3", 
			"window-get-parent" => "3", 
			"window-get-width" => "3", 
			"window-get-x" => "3", 
			"window-get-y" => "3", 
			"window-is-shown" => "3", 
			"window-make-modal" => "3", 
			"window-popup-menu" => "3", 
			"window-refresh" => "3", 
			"window-remove-callback" => "3", 
			"window-set-client-size" => "3", 
			"window-set-cursor" => "3", 
			"window-set-focus" => "3", 
			"window-set-size" => "3", 
			"window-set-size-hints" => "3", 
			"window-show" => "3", 
			"write-resource" => "3", 
			"wxclips-object-exists" => "3", 
			"yield" => "3", 
			"CardDelete" => "4", 
			"CardDeleteAllLinks" => "4", 
			"CardDeleteLink" => "4", 
			"CardEditFilename" => "4", 
			"CardEditTitle" => "4", 
			"CardGotoControlWindow" => "4", 
			"CardLinkNewCard" => "4", 
			"CardLinkToSelection" => "4", 
			"CardOpenFile" => "4", 
			"CardOrderLinks" => "4", 
			"CardQuit" => "4", 
			"CardSaveFile" => "4", 
			"CardSaveFileAs" => "4", 
			"CardSelectItem" => "4", 
			"CardToggleLinkPanel" => "4", 
			"DiagramAddAnnotation" => "4", 
			"DiagramAddControl" => "4", 
			"DiagramApplyDefinition" => "4", 
			"DiagramBrowse" => "4", 
			"DiagramChangeFont" => "4", 
			"DiagramClearAll" => "4", 
			"DiagramCopy" => "4", 
			"DiagramCopyDiagram" => "4", 
			"DiagramCopySelection" => "4", 
			"DiagramCopyToClipboard" => "4", 
			"DiagramCut" => "4", 
			"DiagramDeleteAnnotation" => "4", 
			"DiagramDeleteControl" => "4", 
			"DiagramDeselectAll" => "4", 
			"DiagramDuplicateSelection" => "4", 
			"DiagramEditOptions" => "4", 
			"DiagramFormatGraph" => "4", 
			"DiagramFormatText" => "4", 
			"DiagramFormatTree" => "4", 
			"DiagramGotoRoot" => "4", 
			"DiagramHelp" => "4", 
			"DiagramHorizontalAlign" => "4", 
			"DiagramHorizontalAlignBottom" => "4", 
			"DiagramHorizontalAlignTop" => "4", 
			"DiagramNewExpansion" => "4", 
			"DiagramPaste" => "4", 
			"DiagramPrint" => "4", 
			"DiagramPrintAll" => "4", 
			"DiagramPrintEPS" => "4", 
			"DiagramPrintPreview" => "4", 
			"DiagramRefresh" => "4", 
			"DiagramSaveBitmap" => "4", 
			"DiagramSaveMetafile" => "4", 
			"DiagramSelectAll" => "4", 
			"DiagramStraighten" => "4", 
			"DiagramToBack" => "4", 
			"DiagramToFront" => "4", 
			"DiagramTogglePalette" => "4", 
			"DiagramToggleToolbar" => "4", 
			"DiagramVerticalAlign" => "4", 
			"DiagramVerticalAlignLeft" => "4", 
			"DiagramVerticalAlignRight" => "4", 
			"DiagramZoom100" => "4", 
			"DiagramZoom30" => "4", 
			"DiagramZoom40" => "4", 
			"DiagramZoom50" => "4", 
			"DiagramZoom60" => "4", 
			"DiagramZoom70" => "4", 
			"DiagramZoom80" => "4", 
			"DiagramZoom90" => "4", 
			"HardyBrowseFiles" => "4", 
			"HardyClearIndex" => "4", 
			"HardyConfigure" => "4", 
			"HardyDeselectAllItems" => "4", 
			"HardyDrawTree" => "4", 
			"HardyExit" => "4", 
			"HardyFindOrphans" => "4", 
			"HardyHelpAbout" => "4", 
			"HardyHelpContents" => "4", 
			"HardyHelpSearch" => "4", 
			"HardyLoadApplication" => "4", 
			"HardyLoadFile" => "4", 
			"HardyPrint" => "4", 
			"HardyPrintPreview" => "4", 
			"HardyPrintSetup" => "4", 
			"HardySaveFile" => "4", 
			"HardySaveFileAs" => "4", 
			"HardySearchCards" => "4", 
			"HardyShowArcSymbolEditor" => "4", 
			"HardyShowDevelopmentWindow" => "4", 
			"HardyShowDiagramManager" => "4", 
			"HardyShowHypertextManager" => "4", 
			"HardyShowNodeSymbolEditor" => "4", 
			"HardyShowPackageTool" => "4", 
			"HardyShowSymbolLibrarian" => "4", 
			"HardyViewTopCard" => "4");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"" => "donothing", 
			"1" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing", 
			"4" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>

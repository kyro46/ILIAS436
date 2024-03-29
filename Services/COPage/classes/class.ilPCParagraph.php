<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/COPage/classes/class.ilPageContent.php");
require_once("./Services/COPage/classes/class.ilWysiwygUtil.php");

/**
* Class ilPCParagraph
*
* Paragraph of ilPageObject
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id: class.ilPCParagraph.php 41579 2013-04-21 12:31:21Z akill $
*
* @ingroup ServicesCOPage
*/
class ilPCParagraph extends ilPageContent
{
	var $dom;
	var $par_node;			// node of Paragraph element

	/**
	* Init page content component.
	*/
	function init()
	{
		$this->setType("par");
	}

	/**
	* Set Page Content Node
	*
	* @param	object	$a_node		Page Content DOM Node
	*/
	function setNode(&$a_node)
	{
		parent::setNode($a_node);		// this is the PageContent node
		
		$childs = $a_node->child_nodes();
		for ($i=0; $i<count($childs); $i++)
		{
			if ($childs[$i]->node_name() == "Paragraph")
			{
				$this->par_node = $childs[$i];		//... and this the Paragraph node
			}
		}
	}


	/**
	* Create new page content (incl. paragraph) node at node
	*
	* @param	object	$node		Parent Node for Page Content
	*/
	function createAtNode(&$node)
	{
		$this->node = $this->createPageContentNode();
		$this->par_node =& $this->dom->create_element("Paragraph");
		$this->par_node =& $this->node->append_child($this->par_node);
		$this->par_node->set_attribute("Language", "");
		$node->append_child ($this->node);
	}

	/**
	* Create new page content (incl. paragraph) node at node
	*
	* @param	object	$node		Parent Node for Page Content
	*/
	function createBeforeNode(&$node)
	{
		$this->node = $this->createPageContentNode();
		$this->par_node =& $this->dom->create_element("Paragraph");
		$this->par_node =& $this->node->append_child($this->par_node);
		$this->par_node->set_attribute("Language", "");
		$node->insert_before($this->node, $node);
	}

	/**
	* Create paragraph node (incl. page content node)
	* after given node.
	*
	* @param	object	$node		Predecessing node
	*/
	function createAfter($node)
	{
		$this->node = $this->createPageContentNode(false);
		if($succ_node = $node->next_sibling())
		{
			$this->node = $succ_node->insert_before($this->node, $succ_node);
		}
		else
		{
			$parent_node = $node->parent_node();
			$this->node = $parent_node->append_child($this->node);
		}
		$this->par_node = $this->dom->create_element("Paragraph");
		$this->par_node = $this->node->append_child($this->par_node);
		$this->par_node->set_attribute("Language", "");
	}
	
	/**
	* Create paragraph node (incl. page content node)
	* at given hierarchical ID.
	*
	* @param	object	$a_pg_obj		Page Object
	* @param	string	$a_hier_id		Hierarchical ID
	*/
	function create(&$a_pg_obj, $a_hier_id, $a_pc_id = "")
	{
//echo "-$a_pc_id-";
//echo "<br>-".htmlentities($a_pg_obj->getXMLFromDom())."-<br><br>"; mk();
		$this->node =& $this->dom->create_element("PageContent");
		
		// this next line kicks out placeholders, if something is inserted
		$a_pg_obj->insertContent($this, $a_hier_id, IL_INSERT_AFTER, $a_pc_id);
		
		$this->par_node =& $this->dom->create_element("Paragraph");
		$this->par_node =& $this->node->append_child($this->par_node);
		$this->par_node->set_attribute("Language", "");
	}

	/**
	* Set (xml) content of text paragraph.
	*
	* @param	string		$a_text			text content
	* @param	boolean		$a_auto_split	auto split paragraph at headlines true/false
	*/
	function setText($a_text, $a_auto_split = false)
	{

//var_dump($a_text);
		if (!is_array($a_text))
		{
			$text = array(array("level" => 0, "text" => $a_text));
		}
		else
		{
			$text = $a_text;
		}
		
		if ($a_auto_split)
		{
			$text = $this->autoSplit($a_text);
		}

		// DOMXML_LOAD_PARSING, DOMXML_LOAD_VALIDATING, DOMXML_LOAD_RECOVERING
		$check = "";
		foreach ($text as $t)
		{
			$check.= "<Paragraph>".$t["text"]."</Paragraph>"; 
		}
		/*$temp_dom = domxml_open_mem('<?xml version="1.0" encoding="UTF-8"?><Paragraph>'.$text[0]["text"].'</Paragraph>',
			DOMXML_LOAD_PARSING, $error);*/
		$temp_dom = domxml_open_mem('<?xml version="1.0" encoding="UTF-8"?><Paragraph>'.$check.'</Paragraph>',
			DOMXML_LOAD_PARSING, $error);
			
		//$this->text = $a_text;
		// remove all childs
		if(empty($error))
		{
			$temp_dom = domxml_open_mem('<?xml version="1.0" encoding="UTF-8"?><Paragraph>'.$text[0]["text"].'</Paragraph>',
				DOMXML_LOAD_PARSING, $error);
			
			// delete children of paragraph node
			$children = $this->par_node->child_nodes();
			for($i=0; $i<count($children); $i++)
			{
				$this->par_node->remove_child($children[$i]);
			}

			// copy new content children in paragraph node
			$xpc = xpath_new_context($temp_dom);
			$path = "//Paragraph";
			$res =& xpath_eval($xpc, $path);
			if (count($res->nodeset) == 1)
			{
				$new_par_node =& $res->nodeset[0];
				$new_childs = $new_par_node->child_nodes();
				
				for($i=0; $i<count($new_childs); $i++)
				{
					$cloned_child =& $new_childs[$i]->clone_node(true);
					$this->par_node->append_child($cloned_child);
				}
				$orig_characteristic = $this->getCharacteristic();

				// if headlines are entered and current characteristic is a headline
				// use no characteristic as standard
				if ((count($text) > 1) && (substr($orig_characteristic, 0, 8) == "Headline"))
				{
					$orig_characteristic = "";
				}
				if ($text[0]["level"] > 0)
				{
					$this->par_node->set_attribute("Characteristic", 'Headline'.$text[0]["level"]);
				}
			}
			
			$ok = true;
			
			$c_node = $this->node;
			// add other chunks afterwards
			for ($i=1; $i<count($text); $i++)
			{
				if ($ok)
				{
					$next_par = new ilPCParagraph($this->dom);
					$next_par->createAfter($c_node);
					$next_par->setLanguage($this->getLanguage());
					if ($text[$i]["level"] > 0)
					{
						$next_par->setCharacteristic("Headline".$text[$i]["level"]);
					}
					else
					{
						$next_par->setCharacteristic($orig_characteristic);
					}
					$ok = $next_par->setText($text[$i]["text"], false);
					$c_node = $next_par->node;
				}
			}
			
			return true;
		}
		else
		{
			// We want the correct number of \n here to have the real lines numbers
			$text = str_replace("<br>", "\n", $check);		// replace <br> with \n to get correct line
			$text = str_replace("<br/>", "\n", $text);
			$text = str_replace("<br />", "\n", $text);
			$text = str_replace("</SimpleListItem>", "</SimpleListItem>\n", $text);
			$text = str_replace("<SimpleBulletList>", "\n<SimpleBulletList>", $text);
			$text = str_replace("<SimpleNumberedList>", "\n<SimpleNumberedList>", $text);
			$text = str_replace("<Paragraph>\n", "<Paragraph>", $text);
			$text = str_replace("</Paragraph>", "</Paragraph>\n", $text);
			include_once("./Services/Dom/classes/class.ilDomDocument.php");
			$doc = new ilDOMDocument();
			$text = '<?xml version="1.0" encoding="UTF-8"?><Paragraph>'.$text.'</Paragraph>';
//echo htmlentities($text);
			$this->success = $doc->loadXML($text);
			$error = $doc->errors;
			$estr = "";
			foreach ($error as $e)
			{
				$e = str_replace(" in Entity", "", $e);
				$estr.= $e."<br />";
			}
			return $estr;
		}
	}

	/**
	* Get (xml) content of paragraph.
	*
	* @return	string		Paragraph Content.
	*/
	function getText($a_short_mode = false)
	{
		if (is_object($this->par_node))
		{
			$content = "";
			$childs = $this->par_node->child_nodes();
			for($i=0; $i<count($childs); $i++)
			{
				$content .= $this->dom->dump_node($childs[$i]);
			}
			return $content;
		}
		else
		{
			return "";
		}
	}
	
	/**
	 * Get paragraph sequenc of current paragraph
	 */
	function getParagraphSequenceContent($a_pg_obj)
	{
		$childs = $this->par_node->parent_node()->parent_node()->child_nodes();
		$seq = array();
		$cur_seq = array();
		$found = false;
		$pc_id = $this->readPCId();
		$hier_id = $this->readHierId();
		for($i=0; $i<count($childs); $i++)
		{
			$pchilds = $childs[$i]->child_nodes();
			if ($pchilds[0]->node_name() == "Paragraph" &&
				$pchilds[0]->get_attribute("Characteristic") != "Code")
			{
				$cur_seq[] = $childs[$i];
				
				// check whether this is the sequence of the current paragraph
				if ($childs[$i]->get_attribute("PCID") == $pc_id &&
					$childs[$i]->get_attribute("HierId") == $hier_id)
				{
					$found = true;
				}
				
				// if this is the current sequenc, get it
				if ($found)
				{
					$seq = $cur_seq;
				}
			}
			else
			{
				// non-paragraph element found -> init the current sequence
				$cur_seq = array();
				$found = false;
			}
		}
		
		$content = "";
		$ids = "###";
		$id_sep = "";
		foreach ($seq as $p_node)
		{
			$ids.= $id_sep.$p_node->get_attribute("HierId").":".$p_node->get_attribute("PCID");
			$po = $a_pg_obj->getContentObject($p_node->get_attribute("HierId"),
				$p_node->get_attribute("PCID"));
			$s_text = $po->getText();
			$s_text = $po->xml2output($s_text, true, false);
			$char = $po->getCharacteristic();
			if ($char == "")
			{
				$char = "Standard";
			}
			$s_text = ilPCParagraphGUI::xml2outputJS($s_text, $char, $po->readPCId());
			$content.= $s_text;
			$id_sep = ";";
		}
		$ids.= "###";
		
		return $ids.$content;
	}

	/**
	* Set Characteristic of paragraph
	*
	* @param	string	$a_char		Characteristic
	*/
	function setCharacteristic($a_char)
	{
		if (!empty($a_char))
		{
			$this->par_node->set_attribute("Characteristic", $a_char);
		}
		else
		{
			if ($this->par_node->has_attribute("Characteristic"))
			{
				$this->par_node->remove_attribute("Characteristic");
			}
		}
	}

	/**
	* Get characteristic of paragraph.
	*
	* @return	string		characteristic
	*/
	function getCharacteristic()
	{
		if (is_object($this->par_node))
		{
			return $this->par_node->get_attribute("Characteristic");
		}
	}


	/**
	* set attribute subcharacteristic
	*/
	function setSubCharacteristic($a_char)
	{
		if (!empty($a_char))
		{
			$this->par_node->set_attribute("SubCharacteristic", $a_char);
		}
		else
		{
			if ($this->par_node->has_attribute("SubCharacteristic"))
			{
				$this->par_node->remove_attribute("SubCharacteristic");
			}
		}
	}

	/**
	* Get AutoIndent (Code Paragraphs)
	*
	* @param	string		Auto Indent attribute
	*/
	function getAutoIndent()
	{
		return $this->par_node->get_attribute("AutoIndent");
	}
	
	function setAutoIndent($a_char)
	{
		if (!empty($a_char))
		{
			$this->par_node->set_attribute("AutoIndent", $a_char);
		}
		else
		{
			if ($this->par_node->has_attribute("AutoIndent"))
			{
				$this->par_node->remove_attribute("AutoIndent");
			}
		}
	}

	/**
	* get attribute subcharacteristic
	*/
	function getSubCharacteristic()
	{
		return $this->par_node->get_attribute("SubCharacteristic");
	}

	/**
	* set attribute download title
	*/

	function setDownloadTitle($a_char)
	{
		if (!empty($a_char))
		{
			$this->par_node->set_attribute("DownloadTitle", $a_char);
		}
		else
		{
			if ($this->par_node->has_attribute("DownloadTitle"))
			{
				$this->par_node->remove_attribute("DownloadTitle");
			}
		}
	}

	/**
	* get attribute download title
	*/
	function getDownloadTitle()
	{
		return $this->par_node->get_attribute("DownloadTitle");
	}
	
	/**
	* set attribute showlinenumbers
	*/
	
	function setShowLineNumbers($a_char)
	{
		$a_char = empty($a_char)?"n":$a_char;
		
		$this->par_node->set_attribute("ShowLineNumbers", $a_char);
	}

	/**
	* get attribute showlinenumbers
	* 
	*/
	function getShowLineNumbers()
	{
		return $this->par_node->get_attribute("ShowLineNumbers");
	}
	
	/**
	* set language
	*/
	function setLanguage($a_lang)
	{
		$this->par_node->set_attribute("Language", $a_lang);
	}

	/**
	* get language
	*/
	function getLanguage()
	{
		return $this->par_node->get_attribute("Language");
	}

	function input2xml($a_text, $a_wysiwyg = 0, $a_handle_lists = true)
	{
		return $this->_input2xml($a_text, $this->getLanguage(), $a_wysiwyg, $a_handle_lists);
	}
	
	/**
	* converts user input to xml
	*/
	static function _input2xml($a_text, $a_lang, $a_wysiwyg = 0, $a_handle_lists = true)
	{
		if (!$a_wysiwyg)
		{
			$a_text = ilUtil::stripSlashes($a_text, false);
		}
		
		if ($a_wysiwyg)
		{
			$a_text = str_replace("<br />", chr(10), $a_text);
		}

		// note: the order of the processing steps is crucial
		// and should be the same as in xml2output() in REVERSE order!
		$a_text = trim($a_text);

//echo "<br>between:".htmlentities($a_text);

		// mask html
if (!$a_wysiwyg)
{
		$a_text = str_replace("&","&amp;",$a_text);
}
		$a_text = str_replace("<","&lt;",$a_text);
		$a_text = str_replace(">","&gt;",$a_text);

		// Reconvert PageTurn and BibItemIdentifier
		$a_text = preg_replace('/&lt;([\s\/]*?PageTurn.*?)&gt;/i',"<$1>",$a_text);
		$a_text = preg_replace('/&lt;([\s\/]*?BibItemIdentifier.*?)&gt;/i',"<$1>",$a_text);

//echo "<br>second:".htmlentities($a_text);

		// mask curly brackets
/*
echo htmlentities($a_text);
		$a_text = str_replace("{", "&#123;", $a_text);
		$a_text = str_replace("}", "&#125;", $a_text);
echo htmlentities($a_text);*/
		// linefeed to br
		$a_text = str_replace(chr(13).chr(10),"<br />",$a_text);
		$a_text = str_replace(chr(13),"<br />", $a_text);
		$a_text = str_replace(chr(10),"<br />", $a_text);

		if ($a_handle_lists)
		{
			$a_text = ilPCParagraph::input2xmlReplaceLists($a_text);
		}
		
		// remove empty tags
		$atags = array("com", "emp", "str", "fn", "quot", "code", "acc", "imp", "kw");
		foreach ($atags as $at)
		{
			$a_text = str_replace("[".$at."][/".$at."]", "", $a_text);
		}
		
		// bb code to xml
		$a_text = eregi_replace("\[com\]","<Comment Language=\"".$a_lang."\">",$a_text);
		$a_text = eregi_replace("\[\/com\]","</Comment>",$a_text);
		$a_text = eregi_replace("\[emp\]","<Emph>",$a_text);
		$a_text = eregi_replace("\[\/emp\]","</Emph>",$a_text);
		$a_text = eregi_replace("\[str\]","<Strong>",$a_text);
		$a_text = eregi_replace("\[\/str\]","</Strong>",$a_text);
		$a_text = eregi_replace("\[fn\]","<Footnote>",$a_text);
		$a_text = eregi_replace("\[\/fn\]","</Footnote>",$a_text);
		$a_text = eregi_replace("\[quot\]","<Quotation Language=\"".$a_lang."\">",$a_text);
		$a_text = eregi_replace("\[\/quot\]","</Quotation>",$a_text);
		$a_text = eregi_replace("\[code\]","<Code>",$a_text);
		$a_text = eregi_replace("\[\/code\]","</Code>",$a_text);
		$a_text = eregi_replace("\[acc\]","<Accent>",$a_text);
		$a_text = eregi_replace("\[\/acc\]","</Accent>",$a_text);
		$a_text = eregi_replace("\[imp\]","<Important>",$a_text);
		$a_text = eregi_replace("\[\/imp\]","</Important>",$a_text);
		$a_text = eregi_replace("\[kw\]","<Keyw>",$a_text);
		$a_text = eregi_replace("\[\/kw\]","</Keyw>",$a_text);

		// internal links
		//$any = "[^\]]*";	// this doesn't work :-(
		$ws= "[ \t\r\f\v\n]*";
		$ltypes = "page|chap|term|media|htlm|lm|dbk|glo|frm|exc|tst|svy|webr|chat|cat|crs|grp|file|fold|mep|wiki|sahs|mcst|obj|dfile"; 
		// empty internal links
		while (eregi("\[(iln$ws((inst$ws=$ws([\"0-9])*)?$ws".
			"((".$ltypes.")$ws=$ws([\"0-9])*)$ws".
			"(target$ws=$ws(\"(New|FAQ|Media)\"))?$ws(anchor$ws=$ws(\"([^\"])*\"))?$ws))\]\[\/iln\]", $a_text, $found))
		{
			$a_text = str_replace($found[0], "",$a_text);
		}
		
		while (eregi("\[(iln$ws((inst$ws=$ws([\"0-9])*)?$ws".
			"((".$ltypes.")$ws=$ws([\"0-9])*)$ws".
			"(target$ws=$ws(\"(New|FAQ|Media)\"))?$ws(anchor$ws=$ws(\"([^\"])*\"))?$ws))\]", $a_text, $found))
		{
			$attribs = ilUtil::attribsToArray($found[2]);
			$inst_str = $attribs["inst"];
			// pages
			if (isset($attribs["page"]))
			{
				$tframestr = "";
				if (!empty($found[10]))
				{
					$tframestr = " TargetFrame=\"".$found[10]."\" ";
				}
				$ancstr = "";
				if ($attribs["anchor"] != "")
				{
					$ancstr = ' Anchor="'.$attribs["anchor"].'" ';
				}
				$a_text = eregi_replace("\[".$found[1]."\]",
					"<IntLink Target=\"il_".$inst_str."_pg_".$attribs[page]."\" Type=\"PageObject\"".$tframestr.$ancstr.">", $a_text);
			}
			// chapters
			else if (isset($attribs["chap"]))
			{
				if (!empty($found[10]))
				{
					$tframestr = " TargetFrame=\"".$found[10]."\" ";
				}
				else
				{
					$tframestr = "";
				}
				$a_text = eregi_replace("\[".$found[1]."\]",
					"<IntLink Target=\"il_".$inst_str."_st_".$attribs[chap]."\" Type=\"StructureObject\"".$tframestr.">", $a_text);
			}
			// glossary terms
			else if (isset($attribs["term"]))
			{
				switch ($found[10])
				{
					case "New":
						$tframestr = " TargetFrame=\"New\" ";
						break;

					default:
						$tframestr = " TargetFrame=\"Glossary\" ";
						break;
				}
				$a_text = eregi_replace("\[".$found[1]."\]",
					"<IntLink Target=\"il_".$inst_str."_git_".$attribs[term]."\" Type=\"GlossaryItem\" $tframestr>", $a_text);
			}
			// media object
			else if (isset($attribs["media"]))
			{
				if (!empty($found[10]))
				{
					$tframestr = " TargetFrame=\"".$found[10]."\" ";
					$a_text = eregi_replace("\[".$found[1]."\]",
						"<IntLink Target=\"il_".$inst_str."_mob_".$attribs[media]."\" Type=\"MediaObject\"".$tframestr.">", $a_text);
				}
				else
				{
					$a_text = eregi_replace("\[".$found[1]."\]",
						"<IntLink Target=\"il_".$inst_str."_mob_".$attribs[media]."\" Type=\"MediaObject\"/>", $a_text);
				}
			}
			// direct download file (no repository object)
			else if (isset($attribs["dfile"]))
			{
				$a_text = eregi_replace("\[".$found[1]."\]",
					"<IntLink Target=\"il_".$inst_str."_dfile_".$attribs[dfile]."\" Type=\"File\">", $a_text);
			}
			// repository items (id is ref_id (will be used internally but will
			// be replaced by object id for export purposes)
			else if (isset($attribs["lm"]) || isset($attribs["dbk"]) || isset($attribs["glo"])
					 || isset($attribs["frm"]) || isset($attribs["exc"]) || isset($attribs["tst"])
					 || isset($attribs["svy"]) || isset($attribs["obj"]) || isset($attribs['webr'])
					 || isset($attribs["htlm"]) || isset($attribs["chat"]) || isset($attribs["grp"])
					 || isset($attribs["fold"]) || isset($attribs["sahs"]) || isset($attribs["mcst"])
					 || isset($attribs["mep"]) || isset($attribs["wiki"])
					 || isset($attribs["cat"]) || isset($attribs["crs"]) || isset($attribs["file"]))
			{
				$obj_id = (isset($attribs["lm"])) ? $attribs["lm"] : $obj_id;
				$obj_id = (isset($attribs["dbk"])) ? $attribs["dbk"] : $obj_id;
				$obj_id = (isset($attribs["chat"])) ? $attribs["chat"] : $obj_id;
				$obj_id = (isset($attribs["glo"])) ? $attribs["glo"] : $obj_id;
				$obj_id = (isset($attribs["frm"])) ? $attribs["frm"] : $obj_id;
				$obj_id = (isset($attribs["exc"])) ? $attribs["exc"] : $obj_id;
				$obj_id = (isset($attribs["htlm"])) ? $attribs["htlm"] : $obj_id;
				$obj_id = (isset($attribs["tst"])) ? $attribs["tst"] : $obj_id;
				$obj_id = (isset($attribs["svy"])) ? $attribs["svy"] : $obj_id;
				$obj_id = (isset($attribs["obj"])) ? $attribs["obj"] : $obj_id;
				$obj_id = (isset($attribs["webr"])) ? $attribs["webr"] : $obj_id;
				$obj_id = (isset($attribs["fold"])) ? $attribs["fold"] : $obj_id;
				$obj_id = (isset($attribs["cat"])) ? $attribs["cat"] : $obj_id;
				$obj_id = (isset($attribs["crs"])) ? $attribs["crs"] : $obj_id;
				$obj_id = (isset($attribs["grp"])) ? $attribs["grp"] : $obj_id;
				$obj_id = (isset($attribs["file"])) ? $attribs["file"] : $obj_id;
				$obj_id = (isset($attribs["sahs"])) ? $attribs["sahs"] : $obj_id;
				$obj_id = (isset($attribs["mcst"])) ? $attribs["mcst"] : $obj_id;
				$obj_id = (isset($attribs["mep"])) ? $attribs["mep"] : $obj_id;
				$obj_id = (isset($attribs["wiki"])) ? $attribs["wiki"] : $obj_id;
				//$obj_id = (isset($attribs["obj"])) ? $attribs["obj"] : $obj_id;

				if ($inst_str == "")
				{
					$a_text = eregi_replace("\[".$found[1]."\]",
						"<IntLink Target=\"il_".$inst_str."_obj_".$obj_id."\" Type=\"RepositoryItem\">", $a_text);
				}
				else
				{
					$a_text = eregi_replace("\[".$found[1]."\]",
						"<IntLink Target=\"il_".$inst_str."_".$found[6]."_".$obj_id."\" Type=\"RepositoryItem\">", $a_text);
				}
			}			
			else
			{
				$a_text = eregi_replace("\[".$found[1]."\]", "[error: iln".$found[1]."]",$a_text);
			}
		}
		while (eregi("\[(iln$ws((inst$ws=$ws([\"0-9])*)?".$ws."media$ws=$ws([\"0-9])*)$ws)/\]", $a_text, $found))
		{
			$attribs = ilUtil::attribsToArray($found[2]);
			$inst_str = $attribs["inst"];
			$a_text = eregi_replace("\[".$found[1]."/\]",
				"<IntLink Target=\"il_".$inst_str."_mob_".$attribs[media]."\" Type=\"MediaObject\"/>", $a_text);
		}
		$a_text = eregi_replace("\[\/iln\]","</IntLink>",$a_text);

		// external link
		$ws= "[ \t\r\f\v\n]*";
		// remove empty external links
		while (eregi("\[(xln$ws(url$ws=$ws\"([^\"])*\")$ws(target$ws=$ws(\"(Glossary|FAQ|Media)\"))?$ws)\]\[\/xln\]", $a_text, $found))
		{
			$a_text = str_replace($found[0], "",$a_text);
		}
		while (eregi("\[(xln$ws(url$ws=$ws(([^]])*)))$ws\]\[\/xln\]", $a_text, $found))
		{
			$a_text = str_replace($found[0], "",$a_text);
		}
		// external links
		while (eregi("\[(xln$ws(url$ws=$ws\"([^\"])*\")$ws(target$ws=$ws(\"(Glossary|FAQ|Media)\"))?$ws)\]", $a_text, $found))
		{
			$attribs = ilUtil::attribsToArray($found[2]);
			if (isset($attribs["url"]))
			{
				$a2 = ilUtil::attribsToArray($found[4]);
				$tstr = "";
				if (in_array($a2["target"], array("FAQ", "Glossary", "Media")))
				{
					$tstr = ' TargetFrame="'.$a2["target"].'"';
				}
				$a_text = str_replace("[".$found[1]."]", "<ExtLink Href=\"".$attribs["url"]."\"$tstr>", $a_text);
			}
			else
			{
				$a_text = str_replace("[".$found[1]."]", "[error: xln".$found[1]."]",$a_text);
			}
		}
		
		// ie/tinymce fix for links without "", see bug #8391
		while (eregi("\[(xln$ws(url$ws=$ws(([^]])*)))$ws\]", $a_text, $found))
		{
			if ($found[3] != "")
			{
				$a_text = str_replace("[".$found[1]."]", "<ExtLink Href=\"".$found[3]."\">", $a_text);
			}
			else
			{
				$a_text = str_replace("[".$found[1]."]", "[error: xln".$found[1]."]",$a_text);
			}
		}
		$a_text = eregi_replace("\[\/xln\]","</ExtLink>",$a_text);
		
		// anchor
		$ws= "[ \t\r\f\v\n]*";
		while (eregi("\[(anc$ws(name$ws=$ws\"([^\"])*\")$ws)\]", $a_text, $found))
		{
			$attribs = ilUtil::attribsToArray($found[2]);
			$a_text = str_replace("[".$found[1]."]", "<Anchor Name=\"".$attribs["name"]."\">", $a_text);
		}
		$a_text = eregi_replace("\[\/anc\]","</Anchor>",$a_text);
//echo htmlentities($a_text); exit;
		return $a_text;
	}
	
	/**
	* Converts xml from DB to output in edit textarea.
	*
	* @param	string	$a_text		xml from db
	*
	* @return	string	string ready for edit textarea
	*/
	static function input2xmlReplaceLists($a_text)
	{
		$rows = explode("<br />", $a_text."<br />");
//var_dump($a_text);

		$old_level = 0;

		$text = "";

		foreach ($rows as $row)
		{
			$level = 0;
			if (str_replace("#", "*", substr($row, 0, 3)) == "***")
			{
				$level = 3;
			}
			else if (str_replace("#", "*", substr($row, 0, 2)) == "**")
			{
				$level = 2;
			}
			else if (str_replace("#", "*", substr($row, 0, 1)) == "*")
			{
				$level = 1;
			}

			// end previous line
			if ($level < $old_level)
			{
				for ($i = $old_level; $i > $level; $i--)
				{
					$text.= "</SimpleListItem></".$clist[$i].">";
				}
				if ($level > 0)
				{
					$text.= "</SimpleListItem>";
				}
			}
			else if ($old_level > 0 && $level > 0 && ($level == $old_level))
			{
				$text.= "</SimpleListItem>";
			}
			else if (($level == $old_level) && $text != "")
			{
				$text.= "<br />";
			}
			
			// start next line
			if ($level > $old_level)
			{
				for($i = $old_level + 1; $i <= $level; $i++)
				{
					if (substr($row, $i - 1, 1) == "*")
					{
						$clist[$i] = "SimpleBulletList";
					}
					else
					{
						$clist[$i] = "SimpleNumberedList";
					}
					$text.= "<".$clist[$i]."><SimpleListItem>";
				}
			}
			else if ($old_level > 0 && $level > 0)
			{
				$text.= "<SimpleListItem>";
			}
			$text.= substr($row, $level);
			
			$old_level = $level;
		}
		
		// remove "<br />" at the end
		if (substr($text, strlen($text) - 6) == "<br />")
		{
			$text = substr($text, 0, strlen($text) - 6);
		}
		
		return $text;
	}
	
	/**
	* Replaces <list> tags with *
	*
	* @param	string	$a_text		xml from db
	*
	* @return	string				string containing * for lists
	*/
	static function xml2outputReplaceLists($a_text)
	{
		$segments = ilPCParagraph::segmentString($a_text, array("<SimpleBulletList>", "</SimpleBulletList>",
			"</SimpleListItem>", "<SimpleListItem>", "<SimpleListItem/>", "<SimpleNumberedList>", "</SimpleNumberedList>"));

		$current_list = array();
		$text = "";
		for ($i=0; $i<= count($segments); $i++)
		{
			if ($segments[$i] == "<SimpleBulletList>")
			{
				if (count($current_list) == 0)
				{
					$list_start = true;
				}
				array_push($current_list, "*");
				$li = false;
			}
			else if ($segments[$i] == "<SimpleNumberedList>")
			{
				if (count($current_list) == 0)
				{
					$list_start = true;
				}
				array_push($current_list, "#");
				$li = false;
			}
			else if ($segments[$i] == "</SimpleBulletList>")
			{
				array_pop($current_list);
				$li = false;
			}
			else if ($segments[$i] == "</SimpleNumberedList>")
			{
				array_pop($current_list);
				$li = false;
			}
			else if ($segments[$i] == "<SimpleListItem>")
			{
				$li = true;
			}
			else if ($segments[$i] == "</SimpleListItem>")
			{
				$li = false;
			}
			else if ($segments[$i] == "<SimpleListItem/>")
			{
				if ($list_start)
				{
					$text.= "<br />";
					$list_start = false;
				}
				foreach($current_list as $list)
				{
					$text.= $list;
				}
				$text.= "<br />";
				$li = false;
			}
			else
			{
				if ($li)
				{
					if ($list_start)
					{
						$text.= "<br />";
						$list_start = false;
					}
					foreach($current_list as $list)
					{
						$text.= $list;
					}
				}
				$text.= $segments[$i];
				if ($li)
				{
					$text.= "<br />";
				}
				$li = false;
			}
		}
		
		// remove trailing <br />, if text ends with list
		if ($segments[count($segments) - 1] == "</SimpleBulletList>" ||
			$segments[count($segments) - 1] == "</SimpleNumberedList>" &&
			substr($text, strlen($text) - 6) == "<br />")
		{
			$text = substr($text, 0, strlen($text) - 6);
		}

		return $text;
	}
	
	/**
	* Segments a string into an array at each position of a substring
	*/
	static function segmentString($a_haystack, $a_needles)
	{
		$segments = array();
		
		$nothing_found = false;
		while (!$nothing_found)
		{
			$nothing_found = true;
			$found = -1;
			foreach($a_needles as $needle)
			{
				$pos = stripos($a_haystack, $needle);
				if (is_int($pos) && ($pos < $found || $found == -1))
				{
					$found = $pos;
					$found_needle = $needle;
					$nothing_found = false;
				}
			}
			if ($found > 0)
			{
				$segments[] = substr($a_haystack, 0, $found);
				$a_haystack = substr($a_haystack, $found);
			}
			if ($found > -1)
			{
				$segments[] = substr($a_haystack, 0, strlen($found_needle));
				$a_haystack = substr($a_haystack, strlen($found_needle));
			}
		}
		if ($a_haystack != "")
		{
			$segments[] = $a_haystack;
		}
		
		return $segments;
	}

	/**
	* Converts xml from DB to output in edit textarea.
	*
	* @param	string	$a_text		xml from db
	*
	* @return	string	string ready for edit textarea
	*/
	static function xml2output($a_text, $a_wysiwyg = false, $a_replace_lists = true)
	{
		// note: the order of the processing steps is crucial
		// and should be the same as in input2xml() in REVERSE order!

		// xml to bb code
		$any = "[^>]*";
		$a_text = eregi_replace("<Comment[^>]*>","[com]",$a_text);
		$a_text = eregi_replace("</Comment>","[/com]",$a_text);
		$a_text = eregi_replace("<Comment/>","[com][/com]",$a_text);
		$a_text = eregi_replace("<Emph>","[emp]",$a_text);
		$a_text = eregi_replace("</Emph>","[/emp]",$a_text);
		$a_text = eregi_replace("<Emph/>","[emp][/emp]",$a_text);
		$a_text = eregi_replace("<Strong>","[str]",$a_text);
		$a_text = eregi_replace("</Strong>","[/str]",$a_text);
		$a_text = eregi_replace("<Strong/>","[str][/str]",$a_text);
		$a_text = eregi_replace("<Footnote[^>]*>","[fn]",$a_text);
		$a_text = eregi_replace("</Footnote>","[/fn]",$a_text);
		$a_text = eregi_replace("<Footnote/>","[fn][/fn]",$a_text);
		$a_text = eregi_replace("<Quotation[^>]*>","[quot]",$a_text);
		$a_text = eregi_replace("</Quotation>","[/quot]",$a_text);
		$a_text = eregi_replace("<Quotation/>","[quot][/quot]",$a_text);
		$a_text = eregi_replace("<Code[^>]*>","[code]",$a_text);
		$a_text = eregi_replace("</Code>","[/code]",$a_text);
		$a_text = eregi_replace("<Code/>","[code][/code]",$a_text);
		$a_text = eregi_replace("<Accent>","[acc]",$a_text);
		$a_text = eregi_replace("</Accent>","[/acc]",$a_text);
		$a_text = eregi_replace("<Important>","[imp]",$a_text);
		$a_text = eregi_replace("</Important>","[/imp]",$a_text);
		$a_text = eregi_replace("<Keyw>","[kw]",$a_text);
		$a_text = eregi_replace("</Keyw>","[/kw]",$a_text);

		// replace lists
		if ($a_replace_lists)
		{
//echo "<br>".htmlentities($a_text);
			$a_text = ilPCParagraph::xml2outputReplaceLists($a_text);
//echo "<br>".htmlentities($a_text);
		}
		
		// internal links
		while (eregi("<IntLink($any)>", $a_text, $found))
		{
			$found[0];
			$attribs = ilUtil::attribsToArray($found[1]);
			$target = explode("_", $attribs["Target"]);
			$target_id = $target[count($target) - 1];
			$inst_str = (!is_int(strpos($attribs["Target"], "__")))
				? $inst_str = "inst=\"".$target[1]."\" "
				: $inst_str = "";
			switch($attribs["Type"])
			{
				case "PageObject":
					$tframestr = (!empty($attribs["TargetFrame"]))
						? " target=\"".$attribs["TargetFrame"]."\""
						: "";
					$ancstr = (!empty($attribs["Anchor"]))
						? ' anchor="'.$attribs["Anchor"].'"'
						: "";
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."page=\"".$target_id."\"$tframestr$ancstr]",$a_text);
					break;

				case "StructureObject":
					$tframestr = (!empty($attribs["TargetFrame"]))
						? " target=\"".$attribs["TargetFrame"]."\""
						: "";
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."chap=\"".$target_id."\"$tframestr]",$a_text);
					break;

				case "GlossaryItem":
					$tframestr = (empty($attribs["TargetFrame"]) || $attribs["TargetFrame"] == "Glossary")
						? ""
						: " target=\"".$attribs["TargetFrame"]."\"";
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."term=\"".$target_id."\"".$tframestr."]",$a_text);
					break;

				case "MediaObject":
					if (empty($attribs["TargetFrame"]))
					{
						$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."media=\"".$target_id."\"/]",$a_text);
					}
					else
					{
						$a_text = eregi_replace("<IntLink".$found[1].">","[iln media=\"".$target_id."\"".
							" target=\"".$attribs["TargetFrame"]."\"]",$a_text);
					}
					break;

				// Repository Item (using ref id)
				case "RepositoryItem":
					if ($inst_str == "")
					{
						$target_type = ilObject::_lookupType($target_id, true);
					}
					else
					{
						$rtype = $target[count($target) - 2];
						$target_type = $rtype;
					}
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."$target_type=\"".$target_id."\"".$tframestr."]",$a_text);
					break;

				// Download File (not in repository, Object ID)
				case "File":
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln ".$inst_str."dfile=\"".$target_id."\"".$tframestr."]",$a_text);
					break;
					
				default:
					$a_text = eregi_replace("<IntLink".$found[1].">","[iln]",$a_text);
					break;
			}
		}
		$a_text = eregi_replace("</IntLink>","[/iln]",$a_text);

		// external links
		while (eregi("<ExtLink($any)>", $a_text, $found))
		{
			$found[0];
			$attribs = ilUtil::attribsToArray($found[1]);
			//$found[1] = str_replace("?", "\?", $found[1]);
			$tstr = "";
			if (in_array($attribs["TargetFrame"], array("FAQ", "Glossary", "Media")))
			{
				$tstr = ' target="'.$attribs["TargetFrame"].'"';
			}
			$a_text = str_replace("<ExtLink".$found[1].">","[xln url=\"".$attribs["Href"]."\"$tstr]",$a_text);
		}
		$a_text = eregi_replace("</ExtLink>","[/xln]",$a_text);

		// anchor
		while (eregi("<Anchor($any)>", $a_text, $found))
		{
			$found[0];
			$attribs = ilUtil::attribsToArray($found[1]);
			$a_text = str_replace("<Anchor".$found[1].">","[anc name=\"".$attribs["Name"]."\"]",$a_text);
		}
		$a_text = eregi_replace("</Anchor>","[/anc]",$a_text);


		// br to linefeed
		if (!$a_wysiwyg)
		{
			$a_text = str_replace("<br />", "\n", $a_text);
			$a_text = str_replace("<br/>", "\n", $a_text);
		}

if (!$a_wysiwyg)
{
		// prevent curly brackets from being swallowed up by template engine
		$a_text = str_replace("{", "&#123;", $a_text);
		$a_text = str_replace("}", "&#125;", $a_text);

		// unmask html
		$a_text = str_replace("&lt;", "<", $a_text);
		$a_text = str_replace("&gt;", ">",$a_text);

		// this is needed to allow html like <tag attribute="value">... in paragraphs
		$a_text = str_replace("&quot;", "\"", $a_text);

		// make ampersands in (enabled) html attributes work
		// e.g. <a href="foo.php?n=4&t=5">hhh</a>
		$a_text = str_replace("&amp;", "&", $a_text);

		// make &gt; and $lt; work to allow (disabled) html descriptions
		$a_text = str_replace("&lt;", "&amp;lt;", $a_text);
		$a_text = str_replace("&gt;", "&amp;gt;", $a_text);
}
		return $a_text;
		//return str_replace("<br />", chr(13).chr(10), $a_text);
	}

	/**
	* This function splits a paragraph text that has been already
	* processed with input2xml at each header position =header1=,
	* ==header2== or ===header3=== and returns an array that contains
	* the single chunks.
	*/
	function autoSplit($a_text)
	{
		$a_text = str_replace ("=<SimpleBulletList>", "=<br /><SimpleBulletList>", $a_text);
		$a_text = str_replace ("=<SimpleNumberedList>", "=<br /><SimpleNumberedList>", $a_text);
		$a_text = str_replace ("</SimpleBulletList>=", "</SimpleBulletList><br />=", $a_text);
		$a_text = str_replace ("</SimpleNumberedList>=", "</SimpleNumberedList><br />=", $a_text);
		$a_text = "<br />".$a_text."<br />";		// add preceding and trailing br
		
		$chunks = array();
		$c_text = $a_text;
//echo "0";
		while ($c_text != "")
		{
//var_dump($c_text); flush();
//echo "1";
			$s1 = strpos($c_text, "<br />=");
			if (is_int($s1))
			{
//echo "2";
				$s2 = strpos($c_text, "<br />==");
				if (is_int($s2) && $s2 <= $s1)
				{
//echo "3";
					$s3 = strpos($c_text, "<br />===");
					if (is_int($s3) && $s3 <= $s2)		// possible level three header
					{
//echo "4";
						$n = strpos($c_text, "<br />", $s3 + 1);
						if ($n > ($s3+9) && substr($c_text, $n-3, 9) == "===<br />")
						{
//echo "5";
							// found level three header
							if ($s3 > 0 || $head != "")
							{
//echo "6";
								$chunks[] = array("level" => 0,
									"text" => $this->removeTrailingBr($head.substr($c_text, 0, $s3)));
								$head = "";
							}
							$chunks[] = array("level" => 3,
								"text" => trim(substr($c_text, $s3+9, $n-$s3-12)));
							$c_text = $this->handleNextBr(substr($c_text, $n+6));
						}
						else
						{
//echo "7";
							$head.= substr($c_text, 0, $n);
							$c_text = substr($c_text, $n);
						}
					}
					else	// possible level two header
					{
//echo "8";
						$n = strpos($c_text, "<br />", $s2 + 1);
						if ($n > ($s2+8) && substr($c_text, $n-2, 8) == "==<br />")
						{
//echo "9";
							// found level two header
							if ($s2 > 0 || $head != "")
							{
//echo "A";
								$chunks[] = array("level" => 0,
									"text" => $this->removeTrailingBr($head.substr($c_text, 0, $s2)));
								$head = "";
							}
							$chunks[] = array("level" => 2, "text" => trim(substr($c_text, $s2+8, $n-$s2-10)));
							$c_text = $this->handleNextBr(substr($c_text, $n+6));
						}
						else
						{
//echo "B";
							$head.= substr($c_text, 0, $n);
							$c_text = substr($c_text, $n);
						}
					}
				}
				else	// possible level one header
				{
//echo "C";
					$n = strpos($c_text, "<br />", $s1 + 1);
					if ($n > ($s1+7) && substr($c_text, $n-1, 7) == "=<br />")
					{
//echo "D";
						// found level one header
						if ($s1 > 0 || $head != "")
						{
//echo "E";
							$chunks[] = array("level" => 0,
								"text" => $this->removeTrailingBr($head.substr($c_text, 0, $s1)));
							$head = "";
						}
						$chunks[] = array("level" => 1, "text" => trim(substr($c_text, $s1+7, $n-$s1-8)));
						$c_text = $this->handleNextBr(substr($c_text, $n+6));
//echo "<br>ctext:".htmlentities($c_text)."<br>";
					}
					else
					{
						$head.= substr($c_text, 0, $n);
						$c_text = substr($c_text, $n);
//echo "<br>head:".$head."c_text:".$c_text."<br>";
					}
				}
			}
			else
			{
//echo "G";
				$chunks[] = array("level" => 0, "text" => $head.$c_text);
				$head = "";
				$c_text = "";
			}
		}
		if (count($chunks) == 0)
		{
			$chunks[] = array("level" => 0, "text" => "");
		}
		

		// remove preceding br
		if (substr($chunks[0]["text"], 0, 6) == "<br />")
		{
			$chunks[0]["text"] = substr($chunks[0]["text"], 6);
		}

		// remove trailing br
		if (substr($chunks[count($chunks) - 1]["text"],
			strlen($chunks[count($chunks) - 1]["text"]) - 6, 6) == "<br />")
		{
			$chunks[count($chunks) - 1]["text"] =
				substr($chunks[count($chunks) - 1]["text"], 0, strlen($chunks[count($chunks) - 1]["text"]) - 6);
			if ($chunks[count($chunks) - 1]["text"] == "")
			{
				unset($chunks[count($chunks) - 1]);
			}
		}
		return $chunks;
	}
	
	/**
	* Remove preceding <br />
	*/
	function handleNextBr($a_str)
	{
		// do not remove, if next line starts with a "=", otherwise two
		// headlines in a row will not be recognized
		if (substr($a_str, 0, 6) == "<br />" && substr($a_str, 6, 1) != "=")
		{
			$a_str = substr($a_str, 6);
		}
		else
		{
			// if next line starts with a "=" we need to reinsert the <br />
			// otherwise it will not be recognized
			if (substr($a_str, 0, 1) == "=")
			{
				$a_str = "<br />".$a_str;
			}
		}
		return $a_str;
	}

	/**
	* Remove trailing <br />
	*/
	function removeTrailingBr($a_str)
	{
		if (substr($a_str, strlen($a_str) - 6) == "<br />")
		{
			$a_str = substr($a_str, 0, strlen($a_str) - 6);
		}
		return $a_str;
	}
	
	/**
	* Need to override getType from ilPageContent to distinguish between Pararagraph and Source
	*/
	function getType()
	{
		return ($this->getCharacteristic() == "Code")?"src":parent::getType();
	}

	////
	//// Ajax related procedures
	////
	
	/**
	 * Save input coming from ajax
	 *
	 * @param
	 * @return
	 */
	function saveJS($a_pg_obj, $a_content, $a_char, $a_pc_id, $a_insert_at = "")
	{
		global $ilUser;

		$t = self::handleAjaxContent($a_content);
		if ($text === false)
		{
			return false;
		}

		$pc_id = explode(":", $a_pc_id);
		$insert_at = explode(":", $a_insert_at);
		$t_id = explode(":", $t["id"]);
		
		// insert new paragraph
		if ($a_insert_at != "")
		{
			$par = new ilPCParagraph($this->dom);
			$par->create($a_pg_obj, $insert_at[0], $insert_at[1]);
		}
		else
		{
			$par = $a_pg_obj->getContentObject($pc_id[0], $pc_id[1]);
		}
		
		if ($a_insert_at != "")
		{
			$pc_id = $a_pg_obj->generatePCId();
			$par->writePCId($pc_id);
			$this->inserted_pc_id = $pc_id;
		}
		else
		{
			$this->inserted_pc_id = $pc_id[1];
		}

		$par->setLanguage($ilUser->getLanguage());
		$par->setCharacteristic($t["class"]);

		$t2 = $par->input2xml($t["text"], true, false);
		$t2 = ilPCParagraph::handleAjaxContentPost($t2);
		$updated = $par->setText($t2, true);

		if ($updated !== true)
		{
			echo $updated; exit;
			return false;
		}
		$updated = $a_pg_obj->update();
		return $updated;
	}

	/**
	 * Get last inserted pc ids
	 *
	 * @param
	 * @return
	 */
	function getLastSavedPCId($a_pg_obj, $a_as_ajax_str = false)
	{
		if ($a_as_ajax_str)
		{
			$a_pg_obj->stripHierIDs();
			$a_pg_obj->addHierIds();
			$ids = "###";
//var_dump($this->inserted_pc_ids);
			$combined = $a_pg_obj->getHierIdsForPCIds(
				array($this->inserted_pc_id));
			foreach ($combined as $pc_id => $hier_id)
			{
//echo "1";
				$ids.= $sep.$hier_id.":".$pc_id;
				$sep = ";";
			}
			$ids.= "###";
			return $ids;
		}

		return $this->inserted_pc_id;
	}
	
	
	/**
	 * Handle ajax content
	 */
	static function handleAjaxContent($a_content)
	{
		$a_content = "<dummy>".$a_content."</dummy>";

		$doc = new DOMDocument();

		$content = ilUtil::stripSlashes($a_content, false);

//		$content = str_replace("&lt;", "<", $content);
//		$content = str_replace("&gt;", ">", $content);
//echo "<br><br>".htmlentities($content); mk();
		$res = $doc->loadXML($content);

		if (!$res)
		{
			return false;
		}

		// convert tags
		$xpath = new DOMXpath($doc);
		
		$elements = $xpath->query("//span");
		include_once("./Services/Utilities/classes/class.ilDOM2Util.php");
		while (!is_null($elements) && !is_null($element = $elements->item(0)))
		{
			//$element = $elements->item(0);
			$class = $element->getAttribute("class");
			if (substr($class, 0, 16) == "ilc_text_inline_")
			{
				$class_arr = explode(" ", $class);
				$cnode = ilDOM2Util::changeName($element, "il".substr($class_arr[0], 16), false);
				for ($i = 1; $i < count($class_arr); $i++)
				{
					$cnode = ilDOM2Util::addParent($cnode, "il".substr($class_arr[$i], 16));
				}
			}
			else
			{
				ilDOM2Util::replaceByChilds($element);
			}
			
			$elements = $xpath->query("//span");
		}

		// convert tags
		$xpath = new DOMXpath($doc);
		$elements = $xpath->query("/dummy/div");
		
		$ret = array();
		if (!is_null($elements))
		{
			foreach ($elements as $element)
			{
				$id = $element->getAttribute("id");
				$class = $element->getAttribute("class");
				$class = substr($class, 15);
				if (trim($class) == "")
				{
					$class = "Standard";
				}

				$text = $doc->saveXML($element);
				$text = str_replace("<br/>", "\n", $text);
		
				// remove wrapping div
				$pos = strpos($text, ">");
				$text = substr($text, $pos + 1);
				$pos = strrpos($text, "<");
				$text = substr($text, 0, $pos);
		
		// todo: remove empty spans <span ...> </span>
		
				// replace tags by bbcode
				foreach (ilPageContentGUI::_getCommonBBButtons() as $bb => $cl)
				{
					if (!in_array($bb, array("code", "tex", "fn", "xln")))
					{
						$text = str_replace("<il".$cl.">",
							"[".$bb."]", $text);
						$text = str_replace("</il".$cl.">",
							"[/".$bb."]", $text);
						$text = str_replace("<il".$cl."/>", "", $text);
					}
				}
				$text = str_replace(array("<code>", "</code>"),
					array("[code]", "[/code]"), $text);
				
				$text = str_replace("<code/>", "", $text);
				$text = str_replace('<ul class="ilc_list_u_BulletedList"/>', "", $text);
				$text = str_replace('<ul class="ilc_list_o_NumberedList"/>', "", $text);
				
				$ret[] = array("text" => $text, "id" => $id, "class" => $class);
			}
		}

		// we should only have one here!
		return $ret[0];
	}

	/**
	 * Post input2xml handling of ajax content
	 */
	static function handleAjaxContentPost($text)
	{
		$text = str_replace(array("&lt;ul&gt;", "&lt;/ul&gt;"),
			array("<SimpleBulletList>", "</SimpleBulletList>"), $text);
		$text = str_replace(array("&lt;ul class='ilc_list_u_BulletedList'&gt;", "&lt;/ul&gt;"),
			array("<SimpleBulletList>", "</SimpleBulletList>"), $text);
		$text = str_replace(array("&lt;ul class=\"ilc_list_u_BulletedList\"&gt;", "&lt;/ul&gt;"),
			array("<SimpleBulletList>", "</SimpleBulletList>"), $text);
		$text = str_replace(array("&lt;ol&gt;", "&lt;/ol&gt;"),
			array("<SimpleNumberedList>", "</SimpleNumberedList>"), $text);
		$text = str_replace(array("&lt;ol class='ilc_list_o_NumberedList'&gt;", "&lt;/ol&gt;"),
			array("<SimpleNumberedList>", "</SimpleNumberedList>"), $text);
		$text = str_replace(array("&lt;ol class=\"ilc_list_o_NumberedList\"&gt;", "&lt;/ol&gt;"),
			array("<SimpleNumberedList>", "</SimpleNumberedList>"), $text);
		$text = str_replace(array("&lt;li&gt;", "&lt;/li&gt;"),
			array("<SimpleListItem>", "</SimpleListItem>"), $text);
		$text = str_replace(array("&lt;li class='ilc_list_item_StandardListItem'&gt;", "&lt;/li&gt;"),
			array("<SimpleListItem>", "</SimpleListItem>"), $text);
		$text = str_replace(array("&lt;li class=\"ilc_list_item_StandardListItem\"&gt;", "&lt;/li&gt;"),
			array("<SimpleListItem>", "</SimpleListItem>"), $text);

		$text = str_replace(array("&lt;li class=\"ilc_list_item_StandardListItem\"/&gt;"),
			array("<SimpleListItem></SimpleListItem>"), $text);
		
		$text = str_replace("<SimpleBulletList><br />", "<SimpleBulletList>", $text);
		$text = str_replace("<SimpleNumberedList><br />", "<SimpleNumberedList>", $text);
		$text = str_replace("<br /><SimpleBulletList>", "<SimpleBulletList>", $text);
		$text = str_replace("<br /><SimpleNumberedList>", "<SimpleNumberedList>", $text);
		$text = str_replace("</SimpleBulletList><br />", "</SimpleBulletList>", $text);
		$text = str_replace("</SimpleNumberedList><br />", "</SimpleNumberedList>", $text);
		$text = str_replace("</SimpleListItem><br />", "</SimpleListItem>", $text);

		return $text;
	}

}
?>

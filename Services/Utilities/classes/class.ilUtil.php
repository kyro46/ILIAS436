<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/** @defgroup ServicesUtilities Services/Utilities
 */

/**
* Util class
* various functions, usage as namespace
*
* @author Sascha Hofmann <saschahofmann@gmx.de>
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id: class.ilUtil.php 47051 2014-01-07 12:01:24Z mjansen $
*
* @ingroup	ServicesUtilities
*/
class ilUtil
{
	/**
	* Builds an html image tag
	* TODO: function still in use, but in future use getImagePath and move HTML-Code to your template file
	* @access	public
	*
	* @param	string	object type
	* @param	string	tpl path
	* @static
	* 
	*/
	public static function getImageTagByType($a_type, $a_path, $a_big = false)
	{
		global $lng;

		if ($a_big)
		{
			$big = "_b";
		}
		$filename = "icon_".$a_type."$big.png";

		return "<img src=\"".ilUtil::getImagePath($filename)."\" alt=\"".$lng->txt("obj_".$a_type)."\" title=\"".$lng->txt("obj_".$a_type)."\" border=\"0\" vspace=\"0\"/>";
		//return "<img src=\"".$a_path."/images/"."icon_".$a_type."$big.png\" alt=\"".$lng->txt("obj_".$a_type)."\" title=\"".$lng->txt("obj_".$a_type)."\" border=\"0\" vspace=\"0\"/>";
	}

	/**
	 * Get type icon path path
	 * Return image path for icon_xxx.pngs
	 * Or (if enabled) path to custom icon
	 *
	 * @access public
	 * @param string obj_type
	 * @param int obj_id
	 * @param string size 'tiny','small' or 'big'
	 * @static 
	 *
	 */
	public static function getTypeIconPath($a_type,$a_obj_id,$a_size = 'small')
	{
	 	global $ilSetting;

	 	if($ilSetting->get("custom_icons"))
	 	{
		 	switch($a_type)
		 	{
	 			case 'cat':
	 			case 'crs':
	 			case 'grp':
	 				include_once('./Services/Container/classes/class.ilContainer.php');
	 				if(strlen($path = ilContainer::_lookupIconPath($a_obj_id,$a_size)))
	 				{
	 					return $path;
	 				}
		 	}
	 	}

	 	switch($a_size)
	 	{
	 		case 'tiny':
	 			$postfix = '_s.png';
	 			break;
	 		case 'big':
	 			$postfix = '_b.png';
	 			break;
	 		default:
	 			$postfix = '.png';
	 			break;
	 	}
	 	return ilUtil::getImagePath('icon_'.$a_type.$postfix);
	}

	/**
	* get image path (for images located in a template directory)
	*
	* @access	public
	* @param	string		full image filename (e.g. myimage.png)
	* @param	boolean		should be set to true, if the image is within a module
	*						template directory (e.g. content/templates/default/images/test.png)
	* @static
	*
	*/
	public static function getImagePath($img, $module_path = "", $mode = "output", $offline = false)
	{
		global $ilias, $styleDefinition, $ilCtrl, $ilUser;

		if (is_int(strpos($_SERVER["PHP_SELF"], "setup.php")))
		{
			$module_path = "..";
		}
		if ($module_path != "")
		{
			$module_path = "/".$module_path;
		}

		// default image
		$default_img = ".".$module_path."/templates/default/images/".$img;

		// use ilStyleDefinition instead of account to get the current skin and style
		require_once("./Services/Style/classes/class.ilStyleDefinition.php");
		$current_skin = ilStyleDefinition::getCurrentSkin();
		$current_style = ilStyleDefinition::getCurrentStyle();
		
		if (is_object($styleDefinition))
		{
			$image_dir = $styleDefinition->getImageDirectory(
				ilStyleDefinition::getCurrentMasterStyle(),
				$current_style);
		}
		if ($current_skin == "default")
		{
			$user_img = ".".$module_path."/templates/default/".$image_dir."/".$img;
			$skin_img = ".".$module_path."/templates/default/images/".$img;
		}
		else if (is_object($styleDefinition) && $current_skin != "default")
		{
			$user_img = "./Customizing/global/skin/".
				$current_skin.$module_path."/".$image_dir."/".$img;
			$skin_img = "./Customizing/global/skin/".
				$current_skin.$module_path."/images/".$img;
		}

		if ($offline)
		{
			return "./images/".$img;
		}
		else if (@file_exists($user_img) && $image_dir != "")
		{
			return $user_img;		// found image for skin and style
		}
		else if (file_exists($skin_img))
		{
			return $skin_img;		// found image in skin/images
		}

		return $default_img;			// take image in default
	}

    /**
    * get url of path
    *
    * @author   Brandon Blackmoor <brandon.blackmoor@jfcom.mil>
    * @access   public
    * @param    $relative_path string     complete path to file, relative to web root
    *                                       (e.g.  /data/pfplms103/mobs/mm_732/athena_standing.jpg)
    * @static  
    *                                     
    */
   public static function getHtmlPath($relative_path)
    {
        if (substr($relative_path, 0, 2) == './')
        {
            $relative_path = (substr($relative_path, 1));
        }
        if (substr($relative_path, 0, 1) != '/')
        {
            $relative_path = '/' . $relative_path;
        }
        $htmlpath = ILIAS_HTTP_PATH . $relative_path;
        return $htmlpath;
    }

	/**
	* get full style sheet file name (path inclusive) of current user
	*
	* @param $mode string Output mode of the style sheet ("output" or "filesystem"). !"filesystem" generates the ILIAS
	* version number as attribute to force the reload of the style sheet in a different ILIAS version
	* @param $a_css_name string The name of the style sheet. If empty, the default style name will be chosen
	* @param $a_css_location string The location of the style sheet e.g. a module path. This parameter only makes sense
	* when $a_css_name is used
	* @access	public
	* @static
	* 
	*/
	public static function getStyleSheetLocation($mode = "output", $a_css_name = "", $a_css_location = "")
	{
		global $ilias;
		
		// add version as parameter to force reload for new releases
		// use ilStyleDefinition instead of account to get the current style
		require_once("./Services/Style/classes/class.ilStyleDefinition.php");
		$stylesheet_name = (strlen($a_css_name))
			? $a_css_name
			: ilStyleDefinition::getCurrentStyle().".css";
		if (strlen($a_css_location) && (strcmp(substr($a_css_location, -1), "/") != 0))
		{
			$a_css_location = $a_css_location . "/";
		}

		$filename = "";
		// use ilStyleDefinition instead of account to get the current skin
		require_once("./Services/Style/classes/class.ilStyleDefinition.php");
		if (ilStyleDefinition::getCurrentSkin() != "default")
		{
			$filename = "./Customizing/global/skin/".ilStyleDefinition::getCurrentSkin()."/".$a_css_location.$stylesheet_name;
		}
		if (strlen($filename) == 0 || !file_exists($filename))
		{
			$filename = "./" . $a_css_location . "templates/default/".$stylesheet_name;
		}
		$vers = "";
		if ($mode != "filesystem")
		{
			$vers = str_replace(" ", "-", $ilias->getSetting("ilias_version"));
			$vers = "?vers=".str_replace(".", "-", $vers);
		}
		return $filename . $vers;
	}

	/**
	* get full javascript file name (path inclusive) of current user
	*
	* @param $a_js_name string The name of the js file
	* @param $a_js_location string The location of the js file e.g. a module path
	* @param $add_version boolean Add version information to the filename
	* @access	public
	* @static
	* 
	*/
	public static function getJSLocation($a_js_name, $a_js_location = "", $add_version = FALSE)
	{
		global $ilias;

		// add version as parameter to force reload for new releases
		$js_name = $a_js_name;
		if (strlen($a_js_location) && (strcmp(substr($a_js_location, -1), "/") != 0)) $a_js_location = $a_js_location . "/";

		$filename = "";
		// use ilStyleDefinition instead of account to get the current skin
		require_once("./Services/Style/classes/class.ilStyleDefinition.php");
		if (ilStyleDefinition::getCurrentSkin() != "default")
		{
			$filename = "./Customizing/global/skin/".ilStyleDefinition::getCurrentSkin()."/".$a_js_location.$js_name;
		}
		if (strlen($filename) == 0 || !file_exists($filename))
		{
			$filename = "./" . $a_js_location . "templates/default/".$js_name;
		}
		$vers = "";
		if ($add_version)
		{
			$vers = str_replace(" ", "-", $ilias->getSetting("ilias_version"));
			$vers = "?vers=".str_replace(".", "-", $vers);
		}
		return $filename . $vers;
	}

	/**
	* Get p3p file path. (Not in use yet, see class.ilTemplate.php->show())
	*
	* @access	public
	* @static
	* 
	*/
	public static function getP3PLocation()
	{
		global $ilias;

		if (defined("ILIAS_MODULE"))
		{
			$base = '';
			for($i = 0;$i < count(explode('/',ILIAS_MODULE));$i++)
			{
				$base .= "../Services/Privacy/";
			}
		}
		else
		{
			$base = "./Services/Privacy/";
		}

		if (is_file($base."w3c/p3p.xml"))
		{
			return ILIAS_HTTP_PATH."w3c/p3p.xml";
		}
		else
		{
			return ILIAS_HTTP_PATH."/w3c/p3p_template.xml";
		}
	}

	/**
	* get full style sheet file name (path inclusive) of current user
	*
	* @access	public
	* @static
	* 
	*/
	public static function getNewContentStyleSheetLocation($mode = "output")
	{
		global $ilias;

		// add version as parameter to force reload for new releases
		if ($mode != "filesystem")
		{
			$vers = str_replace(" ", "-", $ilias->getSetting("ilias_version"));
			$vers = "?vers=".str_replace(".", "-", $vers);
		}

		// use ilStyleDefinition instead of account to get the current skin and style
		require_once("./Services/Style/classes/class.ilStyleDefinition.php");
		if (ilStyleDefinition::getCurrentSkin() == "default")
		{
			$in_style = "./templates/".ilStyleDefinition::getCurrentSkin()."/"
									.ilStyleDefinition::getCurrentStyle()."_cont.css";
		}
		else
		{
			$in_style = "./Customizing/global/skin/".ilStyleDefinition::getCurrentSkin()."/"
													.ilStyleDefinition::getCurrentStyle()."_cont.css";
		}

		if (is_file("./".$in_style))
		{
			return $in_style.$vers;
		}
		else
		{
			return "templates/default/delos_cont.css".$vers;
		}
	}

	/**
	* Builds a select form field with options and shows the selected option first
	*
	* @access	public
	* @param	string/array	value to be selected
	* @param	string			variable name in formular
	* @param	array			array with $options (key = lang_key, value = long name)
	* @param	boolean			multiple selection list true/false
	* @param	boolean			if true, the option values are displayed directly, otherwise
	*							they are handled as language variable keys and the corresponding
	*							language variable is displayed
	* @param	int				size
	* @param	string			style class
	* @param	array			additional attributes (key = attribute name, value = attribute value)
	* @param    boolean			disabled
	* @static
	* 
	*/
	public static function formSelect($selected,$varname,$options,$multiple = false,$direct_text = false, $size = "0",
		$style_class = "", $attribs = "",$disabled = false)
	{
		global $lng;

		if ($multiple == true)
		{
			$multiple = " multiple=\"multiple\"";
		}
		else
		{
			$multiple = "";
			$size = 0;
		}

		if ($style_class != "")
		{
			$class = " class=\"".$style_class."\"";
		}
		else
		{
			$class = "";
		}
		$attributes = "";
		if (is_array($attribs))
		{
			foreach ($attribs as $key => $val)
			{
				$attributes .= " ".$key."=\"".$val."\"";
			}
		}
		if($disabled)
		{
			$disabled = ' disabled=\"disabled\"';
		}

		$str = "<select name=\"".$varname ."\"".$multiple." $class size=\"".$size."\" $attributes $disabled>\n";

		foreach ((array) $options as $key => $val)
		{
			$style = "";
			if (is_array($val))
			{
				$style = $val["style"];
				$val = $val["text"];		// mus be last line, since we overwrite
			}

			$sty = ($style != "")
				? ' style="'.$style.'" '
				: "";
				
			if ($direct_text)
			{
				$str .= " <option $sty value=\"".$key."\"";
			}
			else
			{
				$str .= " <option $sty value=\"".$val."\"";
			}
			if (is_array($selected) )
			{
				if (in_array($key,$selected))
				{
					$str .= " selected=\"selected\"";
				}
			}
			else if ($selected == $key)
			{
				$str .= " selected=\"selected\"";
			}

			if ($direct_text)
			{
				$str .= ">".$val."</option>\n";
			}
			else
			{
				$str .= ">".$lng->txt($val)."</option>\n";
			}
		}

		$str .= "</select>\n";

		return $str;
	}

	/**
	* ???
	*
	* @access	public
	* @param string
	* @param string
	* @static
	* 
	*/
	public static function getSelectName ($selected,$values)
	{
		return($values[$selected]);
	}

	/**
	* ???
	* @access	public
	* @param	string
	* @param	string
	* @param	string
	* @param	boolean	disabled checked checkboxes (default: false)
	* @return	string
	* @static
	* 
	*/
	public static function formCheckbox ($checked,$varname,$value,$disabled = false)
	{
		$str = "<input type=\"checkbox\" name=\"".$varname."\"";

		if ($checked == 1)
		{
			$str .= " checked=\"checked\"";
		}

		if ($disabled)
		{
			$str .= " disabled=\"disabled\"";
		}

		$array_var = false;

		if (substr($varname,-2) == "[]")
		{
			$array_var = true;
		}

		// if varname ends with [], use varname[-2] + _ + value as id tag (e.g. "user_id[]" => "user_id_15")
		if ($array_var)
		{
			$varname_id = substr($varname,0,-2)."_".$value;
		}
		else
		{
			$varname_id = $varname;
		}

		// dirty removal of other "[]" in string
		$varname_id = ereg_replace("\[","_",$varname_id);
		$varname_id = ereg_replace("\]","",$varname_id);

		$str .= " value=\"".$value."\" id=\"".$varname_id."\" />\n";

		return $str;
	}

	/**
	 * ???
	 * @accesspublic
	 * @paramstring
	 * @paramstring
	 * @paramstring
	 * @param        string
	 * @returnstring
	 * @static
	 * 
	 */
	public static function formDisabledRadioButton($checked,$varname,$value,$disabled)
	  {
	    if ($disabled) {
	      $str = "<input disabled type=\"radio\" name=\"".$varname."\"";
	    }
	    else {
	      $str = "<input type=\"radio\" name=\"".$varname."\"";
	    }
	    if ($checked == 1)
	      {
		$str .= " checked=\"checked\"";
	      }

	    $str .= " value=\"".$value."\"";
	    $str .= " id=\"".$value."\" />\n";

	    return $str;

	  }


	/**
	* ???
	* @access	public
	* @param	string
	* @param	string
	* @param	string
	* @return	string
	* @static
	* 
	*/
	public static function formRadioButton($checked,$varname,$value,$onclick=null)
	{
		$str = '<input ';

		if($onclick)
		{
			$str .= ('onclick="'.$onclick.'"');
		}
		
		$str .= (" type=\"radio\" name=\"".$varname."\"");
		if ($checked == 1)
		{
			$str .= " checked=\"checked\"";
		}

		$str .= " value=\"".$value."\"";

		$str .= " id=\"".$value."\" />\n";

		return $str;
	}


	/**
	 * create html input area
	 *
	 * @param string $varname    name of form variable
	 * @param string $value      value and id of input
	 * @param boolean $disabled   if true, input appears disabled
	 * @return string string
	 * @static
	 */
	public static function formInput($varname,$value,$disabled = false)
	{

	    $str = "<input type=\"input\" name=\"".$varname."\"";
		if ($disabled)
		{
			$str .= " disabled";
		}

		$str .= " value=\"".$value."\"";

		$str .= " id=\"".$value."\" />\n";

		return $str;
	}


	/**
	* ???
	* @param string
	* @static
	* 
	*/
	public static function checkInput ($vars)
	{
		// TO DO:
		// Diese Funktion soll Formfeldeingaben berprfen (empty und required)
	}

	/**
	* ???
	* @access	public
	* @param	string
	* @static
	*/
	public static function setPathStr ($a_path)
	{
		if ("" != $a_path && "/" != substr($a_path, -1))
		{
			$a_path .= "/";
			//$a_path = substr($a_path,1);
		}

		//return getcwd().$a_path;
		return $a_path;
	}

	/**
	* switches style sheets for each even $a_num
	* (used for changing colors of different result rows)
	*
	* @access	public
	* @param	integer	$a_num	the counter
	* @param	string	$a_css1	name of stylesheet 1
	* @param	string	$a_css2	name of stylesheet 2
	* @return	string	$a_css1 or $a_css2
	* @static
	* 
	*/
	public static function switchColor ($a_num,$a_css1,$a_css2)
	{
		if (!($a_num % 2))
		{
			return $a_css1;
		}
		else
		{
			return $a_css2;
		}
	}

	/**
	* ???
	* @access	public
	* @param	array
	* @return	string
	* @static
	* 
	*/
	public static function checkFormEmpty ($emptyFields)
	{

		$feedback = "";

		foreach ($emptyFields as $key => $val)
		{
			if ($val == "") {
				if ($feedback != "") $feedback .= ", ";
				$feedback .= $key;
			}
		}

		return $feedback;
	}

	/**
	* Linkbar
	* Diese Funktion erzeugt einen typischen Navigationsbalken mit
	* "Previous"- und "Next"-Links und den entsprechenden Seitenzahlen
	*
	* die komplette LinkBar wird zur?ckgegeben
	* der Variablenname f?r den offset ist "offset"
	*
	* @author Sascha Hofmann <shofmann@databay.de>
	*
	* @access	public
	* @param	integer	Name der Skriptdatei (z.B. test.php)
	* @param	integer	Anzahl der Elemente insgesamt
	* @param	integer	Anzahl der Elemente pro Seite
	* @param	integer	Das aktuelle erste Element in der Liste
	* @param	array	Die zu ?bergebenen Parameter in der Form $AParams["Varname"] = "Varwert" (optional)
	* @param	array	layout options (all optional)
	* 					link	=> css name for <a>-tag
	* 					prev	=> value for 'previous page' (default: '<<')
	* 					next	=> value for 'next page' (default: '>>')
	* @return	array	linkbar or false on error
	* @static
	* 
	*/
	public static function Linkbar ($AScript,$AHits,$ALimit,$AOffset,$AParams = array(),$ALayout = array(), $prefix = '')
	{
		$LinkBar = "";

		$layout_link = "";
		$layout_prev = "&lt;&lt;";
		$layout_next = "&gt;&gt;";

		// layout options
		if (count($ALayout > 0))
		{
			if ($ALayout["link"])
			{
				$layout_link = " class=\"".$ALayout["link"]."\"";
			}

			if ($ALayout["prev"])
			{
				$layout_prev = $ALayout["prev"];
			}

			if ($ALayout["next"])
			{
				$layout_next = $ALayout["next"];
			}
		}

		// show links, if hits greater limit
		// or offset > 0 (can be > 0 due to former setting)
		if ($AHits > $ALimit || $AOffset > 0)
		{
			if (!empty($AParams))
			{
				foreach ($AParams as $key => $value)
				{
					$params.= $key."=".$value."&";
				}
			}
			// if ($params) $params = substr($params,0,-1);
			if(strpos($AScript,'&'))
			{
				$link = $AScript."&".$params.$prefix."offset=";
			}
			else
			{
				$link = $AScript."?".$params.$prefix."offset=";
			}

			// ?bergehe "zurck"-link, wenn offset 0 ist.
			if ($AOffset >= 1)
			{
				$prevoffset = $AOffset - $ALimit;
				if ($prevoffset < 0) $prevoffset = 0;
				$LinkBar .= "<a".$layout_link." href=\"".$link.$prevoffset."\">".$layout_prev."&nbsp;</a>";
			}

			// Ben?tigte Seitenzahl kalkulieren
			$pages=intval($AHits/$ALimit);

			// Wenn ein Rest bleibt, addiere eine Seite
			if (($AHits % $ALimit))
			$pages++;

			// Bei Offset = 0 keine Seitenzahlen anzeigen : DEAKTIVIERT
			//			if ($AOffset != 0) {

			// ansonsten zeige Links zu den anderen Seiten an
			for ($i = 1 ;$i <= $pages ; $i++)
			{
				$newoffset=$ALimit*($i-1);

				if ($newoffset == $AOffset)
				{
					$LinkBar .= "[".$i."] ";
				}
				else
				{
					$LinkBar .= '<a '.$layout_link.' href="'.
						$link.$newoffset.'">['.$i.']</a> ';
				}
			}
			//			}

			// Checken, ob letze Seite erreicht ist
			// Wenn nicht, gebe einen "Weiter"-Link aus
			if (! ( ($AOffset/$ALimit)==($pages-1) ) && ($pages!=1) )
			{
				$newoffset=$AOffset+$ALimit;
				$LinkBar .= "<a".$layout_link." href=\"".$link.$newoffset."\">&nbsp;".$layout_next."</a>";
			}

			return $LinkBar;
		}
		else
		{
			return false;
		}
	}

	/**
	* makeClickable
	* In Texten enthaltene URLs und Mail-Adressen klickbar machen
	*
	* @access	public
	* @param	string	$text: Der Text
	* @param	boolean	$detectGotoLinks	if true, internal goto-links will be retargeted to _self and text is replaced by title
	* @return	string	clickable link
	* @static
	* 
	*/
	public static function makeClickable($a_text, $detectGotoLinks = false)
	{
		// New code, uses MediaWiki Sanitizer
		$ret = $a_text;

		// www-URL ohne ://-Angabe
		$ret = eregi_replace("(^|[[:space:]]+)(www\.)([[:alnum:]#?/&=\.-]+)",
			"\\1http://\\2\\3", $ret);

		// ftp-URL ohne ://-Angabe
		$ret = eregi_replace("(^|[[:space:]]+)(ftp\.)([[:alnum:]#?/&=\.-]+)",
			"\\1ftp://\\2\\3", $ret);

		// E-Mail (this does not work as expected, users must add mailto: manually)
		//$ret = eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))",
		//	"mailto:\\1", $ret);

		// mask existing image tags
		$ret = str_replace('src="http://', '"***masked_im_start***', $ret);
		
		include_once("./Services/Utilities/classes/class.ilMWParserAdapter.php");
		$parser = new ilMWParserAdapter();
		$ret = $parser->replaceFreeExternalLinks($ret);

		// unmask existing image tags
		$ret = str_replace('"***masked_im_start***', 'src="http://', $ret);

		// Should be Safe

		if ($detectGotoLinks)
		// replace target blank with self and text with object title.
		{
			$regExp = "<a[^>]*href=\"(".str_replace("/","\/",ILIAS_HTTP_PATH)."\/goto.php\?target=\w+_(\d+)[^\"]*)\"[^>]*>[^<]*<\/a>";
//			echo htmlentities($regExp);
			$ret = preg_replace_callback(
				"/".$regExp."/i",
				array("ilUtil", "replaceLinkProperties"),
				$ret);

			// Static links
			$regExp = "<a[^>]*href=\"(".str_replace("/","\/",ILIAS_HTTP_PATH)."\/goto_.*[a-z0-9]+_([0-9]+)\.html)\"[^>]*>[^<]*<\/a>";
//			echo htmlentities($regExp);
			$ret = preg_replace_callback(
				"/".$regExp."/i",
				array("ilUtil", "replaceLinkProperties"),
				$ret);
		}

		return($ret);
	}

	/**
	 * replaces target _blank with _self and the link text with the according object title.
	 *
	 * @private
	 *
	 * @param string $matches
	 * 	$matches[0] contains complete link
	 * 	$matches[1] contains href attribute
	 * 	$matches[2] contains id of goto link
	 * @return link containg a _self target, same href and new text content
	 * @static
	 * 
	 */
	public static function replaceLinkProperties ($matches)
	{
		$link = $matches[0];
		$ref_id = $matches[2];

		if ($ref_id > 0)
		{
			$obj_id = ilObject::_lookupObjId($ref_id);
			if ($obj_id > 0)
			{
				$title = ilObject::_lookupTitle($obj_id);
				$link = "<a href=".$matches[1]." target=\"_self\">".$title."</a>";
			}
		}
		return $link;
	}

	/**
	* Creates a combination of HTML selects for date inputs
	*
	* Creates a combination of HTML selects for date inputs
	* The select names are $prefix[y] for years, $prefix[m]
	* for months and $prefix[d] for days.
	*
	* @access	public
	* @param	string	$prefix Prefix of the select name
	* @param	integer	$year Default value for year select
	* @param	integer	$month Default value for month select
	* @param	integer	$day Default value for day select
	* @return	string	HTML select boxes
	* @author	Aresch Yavari <ay@databay.de>
	* @author Helmut Schottmüller <hschottm@tzi.de>
	* @static
	* 
	*/
	public static function makeDateSelect($prefix, $year = "", $month = "", $day = "", $startyear = "",$a_long_month = true,$a_further_options = array(), $emptyoption = false)
	{
		global $lng;

		$disabled = '';
		if(isset($a_further_options['disabled']) and $a_further_options['disabled'])
		{
			$disabled = 'disabled="disabled" ';
		}

		$now = getdate();
		if (!$emptyoption)
		{
			if (!strlen($year)) $year = $now["year"];
			if (!strlen($month)) $month = $now["mon"];
			if (!strlen($day)) $day = $now["mday"];
		}

		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;

		// build day select
		
		$sel_day .= '<select ';
		if(isset($a_further_options['select_attributes']))
		{
			foreach($a_further_options['select_attributes'] as $name => $value)
			{
				$sel_day .= ($name.'="'.$value.'" ');
			}
		}
		
		$sel_day .= $disabled."name=\"".$prefix."[d]\" id=\"".$prefix."_d\">\n";
		
		if ($emptyoption) $sel_day .= "<option value=\"0\">--</option>\n";
		for ($i = 1; $i <= 31; $i++)
		{
			$sel_day .= "<option value=\"$i\">" . sprintf("%02d", $i) . "</option>\n";
		}
		$sel_day .= "</select>\n";
		$sel_day = preg_replace("/(value\=\"$day\")/", "$1 selected=\"selected\"", $sel_day);

		// build month select
		$sel_month = '<select ';
		if(isset($a_further_options['select_attributes']))
		{
			foreach($a_further_options['select_attributes'] as $name => $value)
			{
				$sel_month .= ($name.'="'.$value.'" ');
			}
		}
		$sel_month .= $disabled."name=\"".$prefix."[m]\" id=\"".$prefix."_m\">\n";

		if ($emptyoption) $sel_month .= "<option value=\"0\">--</option>\n";
		for ($i = 1; $i <= 12; $i++)
		{
			if($a_long_month)
			{
				$sel_month .= "<option value=\"$i\">" . $lng->txt("month_" . sprintf("%02d", $i) . "_long") . "</option>\n";
			}
			else
			{
				$sel_month .= "<option value=\"$i\">" . $i  . "</option>\n";
			}
		}
		$sel_month .= "</select>\n";
		$sel_month = preg_replace("/(value\=\"$month\")/", "$1 selected=\"selected\"", $sel_month);

		// build year select
		$sel_year = '<select ';
		if(isset($a_further_options['select_attributes']))
		{
			foreach($a_further_options['select_attributes'] as $name => $value)
			{
				$sel_year .= ($name.'="'.$value.'" ');
			}
		}
		$sel_year .= $disabled."name=\"".$prefix."[y]\" id=\"".$prefix."_y\">\n";
		if ((strlen($startyear) == 0) || ($startyear > $year))
		{
			if (!$emptyoption || $year != 0) $startyear = $year - 5;
		}

		if(($year + 5) < (date('Y',time()) + 5))
		{
			$end_year = date('Y',time()) + 5;
		}
		else
		{
			$end_year = $year + 5;
		}

		if ($emptyoption) $sel_year .= "<option value=\"0\">----</option>\n";
		for ($i = $startyear; $i <= $end_year; $i++)
		{
			$sel_year .= "<option value=\"$i\">" . sprintf("%04d", $i) . "</option>\n";
		}
		$sel_year .= "</select>\n";
		$sel_year = preg_replace("/(value\=\"$year\")/", "$1 selected=\"selected\"", $sel_year);

		//$dateformat = $lng->text["lang_dateformat"];
		$dateformat = "d-m-Y";
		$dateformat = strtolower(preg_replace("/\W/", "", $dateformat));
		$dateformat = strtolower(preg_replace("/(\w)/", "%%$1", $dateformat));
		$dateformat = preg_replace("/%%d/", $sel_day, $dateformat);
		$dateformat = preg_replace("/%%m/", $sel_month, $dateformat);
		$dateformat = preg_replace("/%%y/", $sel_year, $dateformat);
		return $dateformat;
	}

	/**
	* Creates a combination of HTML selects for time inputs
	*
	* Creates a combination of HTML selects for time inputs.
	* The select names are $prefix[h] for hours, $prefix[m]
	* for minutes and $prefix[s] for seconds.
	*
	* @access	public
	* @param	string	$prefix Prefix of the select name
	* @param  boolean $short Set TRUE for a short time input (only hours and minutes). Default is TRUE
	* @param	integer $hour Default hour value
	* @param	integer $minute Default minute value
	* @param	integer $second Default second value
	* @return	string	HTML select boxes
	* @author Helmut Schottmüller <hschottm@tzi.de>
	* @static
	* 
	*/
	public static function makeTimeSelect($prefix, $short = true, $hour = "", $minute = "", $second = "",$a_use_default = true,$a_further_options = array())
	{
		global $lng, $ilUser;

		$minute_steps = 1;
		$disabled = '';
		if(count($a_further_options))
		{
			if(isset($a_further_options['minute_steps']))
			{
				$minute_steps = $a_further_options['minute_steps'];
			}
			if(isset($a_further_options['disabled']) and $a_further_options['disabled'])
			{
				$disabled = 'disabled="disabled" ';
			}
		}

		if ($a_use_default and !strlen("$hour$minute$second")) {
			$now = localtime();
			$hour = $now[2];
			$minute = $now[1];
			$second = $now[0];
		} else {
			$hour = (int)$hour;
			$minute = (int)$minute;
			$second = (int)$second;
		}
		// build hour select
		$sel_hour = '<select ';
		if(isset($a_further_options['select_attributes']))
		{
			foreach($a_further_options['select_attributes'] as $name => $value)
			{
				$sel_hour .= $name.'='.$value.' ';
			}
		}
		$sel_hour .= " ".$disabled."name=\"".$prefix."[h]\" id=\"".$prefix."_h\">\n";

		$format = $ilUser->getTimeFormat();
		for ($i = 0; $i <= 23; $i++)
		{
			if($format == ilCalendarSettings::TIME_FORMAT_24)
			{
			  $sel_hour .= "<option value=\"$i\">" . sprintf("%02d", $i) . "</option>\n";
			}
			else
			{
			  $sel_hour .= "<option value=\"$i\">" . date("ga", mktime($i, 0, 0)) . "</option>\n";
			}
		}
		$sel_hour .= "</select>\n";
		$sel_hour = preg_replace("/(value\=\"$hour\")/", "$1 selected=\"selected\"", $sel_hour);

		// build minutes select
		$sel_minute .= "<select ".$disabled."name=\"".$prefix."[m]\" id=\"".$prefix."_m\">\n";

		for ($i = 0; $i <= 59; $i = $i + $minute_steps)
		{
			$sel_minute .= "<option value=\"$i\">" . sprintf("%02d", $i) . "</option>\n";
		}
		$sel_minute .= "</select>\n";
		$sel_minute = preg_replace("/(value\=\"$minute\")/", "$1 selected=\"selected\"", $sel_minute);

		if (!$short) {
			// build seconds select
			$sel_second .= "<select ".$disabled."name=\"".$prefix."[s]\" id=\"".$prefix."_s\">\n";

			for ($i = 0; $i <= 59; $i++)
			{
				$sel_second .= "<option value=\"$i\">" . sprintf("%02d", $i) . "</option>\n";
			}
			$sel_second .= "</select>\n";
			$sel_second = preg_replace("/(value\=\"$second\")/", "$1 selected=\"selected\"", $sel_second);
		}
		$timeformat = $lng->text["lang_timeformat"];
		if (strlen($timeformat) == 0) $timeformat = "H:i:s";
		$timeformat = strtolower(preg_replace("/\W/", "", $timeformat));
		$timeformat = preg_replace("/(\w)/", "%%$1", $timeformat);
		$timeformat = preg_replace("/%%h/", $sel_hour, $timeformat);
		$timeformat = preg_replace("/%%i/", $sel_minute, $timeformat);
		if ($short) {
			$timeformat = preg_replace("/%%s/", "", $timeformat);
		} else {
			$timeformat = preg_replace("/%%s/", $sel_second, $timeformat);
		}
		return $timeformat;
	}

	/**
	* This preg-based function checks whether an e-mail address is formally valid.
	* It works with all top level domains including the new ones (.biz, .info, .museum etc.)
	* and the special ones (.arpa, .int etc.)
	* as well as with e-mail addresses based on IPs (e.g. webmaster@123.45.123.45)
	* Valid top level domains: http://data.iana.org/TLD/tlds-alpha-by-domain.txt
	* @author	Unknown <mail@philipp-louis.de> (source: http://www.php.net/preg_match)
	* @access	public
	* @param	string	email address
	* @return	boolean	true if valid
	* @static
	* 
	*/
	public static function is_email($a_email)
	{
		// BEGIN Mail: If possible, use PearMail to validate e-mail address
		global $ilErr, $ilias;

		// additional check for ilias object is needed,
		// otherwise setup will fail with this if branch
		if(is_object($ilias))
		{
			require_once './Services/PEAR/lib/Mail/RFC822.php';
			$parser = new Mail_RFC822();
			PEAR::setErrorHandling(PEAR_ERROR_EXCEPTION);
			try
			{
				$addresses = $parser->parseAddressList($a_email, 'ilias', false, true);
				if(!is_a($addresses, 'PEAR_Error') &&
					count($addresses) == 1 && $addresses[0]->host != 'ilias'
				)
				{
					PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ilErr, "errorHandler"));
					return true;
				}
			}
			catch(Exception $e)
			{
				PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ilErr, "errorHandler"));
				return false;
			}
			PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ilErr, "errorHandler"));
			return false;
		}
		else
		{
			$tlds = strtolower(
				"AC|AD|AE|AERO|AF|AG|AI|AL|AM|AN|AO|AQ|AR|ARPA|AS|ASIA|AT|AU|AW|AX|AZ|BA|BB|BD|BE|BF|BG|BH|BI|BIZ|BJ|BM|BN|BO|BR|BS|BT|BV|BW|BY|".
				"BZ|CA|CAT|CC|CD|CF|CG|CH|CI|CK|CL|CM|CN|CO|COM|COOP|CR|CU|CV|CX|CY|CZ|DE|DJ|DK|DM|DO|DZ|EC|EDU|EE|EG|".
				"ER|ES|ET|EU|FI|FJ|FK|FM|FO|FR|GA|GB|GD|GE|GF|GG|GH|GI|GL|GM|GN|GOV|GP|GQ|GR|GS|GT|GU|GW|GY|HK|HM|HN|HR|HT|".
				"HU|ID|IE|IL|IM|IN|INFO|INT|IO|IQ|IR|IS|IT|JE|JM|JO|JOBS|JP|KE|KG|KH|KI|KM|KN|KP|KR|KW|KY|KZ|LA|LB|LC|".
				"LI|LK|LR|LS|LT|LU|LV|LY|MA|MC|MD|ME|MG|MH|MIL|MK|ML|MM|MN|MO|MOBI|MP|MQ|MR|MS|MT|MU|MUSEUM|MV|MW|MX|".
				"MY|MZ|NA|NAME|NC|NE|NET|NF|NG|NI|NL|NO|NP|NR|NU|NZ|OM|ORG|PA|PE|PF|PG|PH|PK|PL|PM|PN|PR|PRO|PS|".
				"PT|PW|PY|QA|RE|RO|RS|RU|RW|SA|SB|SC|SD|SE|SG|SH|SI|SJ|SK|SL|SM|SN|SO|SR|ST|SU|SV|SY|SZ|TC|TD|TEL|".
				"TF|TG|TH|TJ|TK|TL|TM|TN|TO|TP|TR|TRAVEL|TT|TV|TW|TZ|UA|UG|UK|US|UY|UZ|VA|VC|VE|VG|VI|VN|VU|".
				"WF|WS|XN|YE|YT|YU|ZA|ZM|ZW");
			
			return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(".$tlds.")|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$a_email));
		}
		// END Mail: If possible, use PearMail to validate e-mail address
	}

	/**
	* validates a password
	* @access	public
	* @param	string	password
	* @return	boolean	true if valid
	* @static
	* 
	*/
	public static function isPassword($a_passwd, &$customError = null)
	{
		global $lng;
		
		include_once('./Services/PrivacySecurity/classes/class.ilSecuritySettings.php');
		$security = ilSecuritySettings::_getInstance();

		if( $security->getAccountSecurityMode() != ilSecuritySettings::ACCOUNT_SECURITY_MODE_CUSTOMIZED )
		{
			/// standard ilias account security mode ///
			
			$customError = null;

			switch( true )
			{
				// no empty password
				case empty($a_passwd):
				
				// min password length is 6
				case strlen($a_passwd) < 6:
					
				// valid chars for password
				case !preg_match("/^[A-Za-z0-9_\.\+\?\#\-\*\@!\$\%\~]+$/", $a_passwd):
					
					return false;
					
				default:
					
					return true;
			}
		}

		/// customized account security mode ///

		// check if password is empty
		if( empty($a_passwd) )
		{
			$customError = $lng->txt('password_empty');
			return false;
		}
		
		$isPassword = true;
		$errors = array();
		
		// check if password to short
		if( $security->getPasswordMinLength() > 0 && strlen($a_passwd) < $security->getPasswordMinLength() )
		{
			$errors[] = sprintf( $lng->txt('password_to_short'), $security->getPasswordMinLength() );
			$isPassword = false;
		}
		
		// check if password not to long
		if( $security->getPasswordMaxLength() > 0 && strlen($a_passwd) > $security->getPasswordMaxLength() )
		{
			$errors[] = sprintf( $lng->txt('password_to_long'), $security->getPasswordMaxLength() );
			$isPassword = false;
		}

		// if password must contains Chars and Numbers
		if( $security->isPasswordCharsAndNumbersEnabled() )
		{
			$hasCharsAndNumbers = true;

			// check password for existing chars
			if( !preg_match('/[A-Za-z]+/',$a_passwd) )
			{
				$hasCharsAndNumbers = false;
			}

			// check password for existing numbers
			if( !preg_match('/[0-9]+/',$a_passwd) )
			{
				$hasCharsAndNumbers = false;
			}

			if( !$hasCharsAndNumbers )
			{
				$errors[] = $lng->txt('password_must_chars_and_numbers');
				$isPassword = false;
			}
		}

		// if password must contains Special-Chars
		if( $security->isPasswordSpecialCharsEnabled() )
		{
			$specialCharsReg = '/[_\.\+\?\#\-\*\@!\$\%\~\/\:\;]+/';
		
			// check password for existing special-chars
			if( !preg_match($specialCharsReg, $a_passwd) )
			{
				$errors[] = $lng->txt('password_must_special_chars');
				$isPassword = false;
			}
		}
		
		// ensure password matches the positive list of chars/special-chars
		if( !preg_match("/^[A-Za-z0-9_\.\+\?\#\-\*\@!\$\%\~\/\:\;]+$/", $a_passwd) )
		{
			$errors[] = $lng->txt('password_contains_invalid_chars');
			$isPassword = false;
		}
		
		// build custom error message
		if( count($errors) == 1 )
		{
			$customError = $errors[0];
		}
		elseif( count($errors) > 1 )
		{
			$customError = $lng->txt('password_multiple_errors');
			$customError .= '<br />'.implode('<br />', $errors);
		}

		return $isPassword;
	}

	/**
	 *	infotext for ilPasswordInputGUI setInfo()
	 *
	 * @global <type> $lng
	 * @return <string>  info about allowed chars for password
	 * @static
	 * 
	 */
	public static function getPasswordRequirementsInfo()
	{
		global $lng;

		include_once('./Services/PrivacySecurity/classes/class.ilSecuritySettings.php');
		$security = ilSecuritySettings::_getInstance();
		
		if( $security->getAccountSecurityMode() == ilSecuritySettings::ACCOUNT_SECURITY_MODE_CUSTOMIZED )
		{
			$validPasswordChars = 'A-Z a-z 0-9 _.+?#-*@!$%~/:;';
		}
		else
		{			
			$validPasswordChars = 'A-Z a-z 0-9 _.+?#-*@!$%~';
		}

		return sprintf($lng->txt('password_allow_chars'), $validPasswordChars);
	}

	/*
	* validates a login
	* @access	public
	* @param	string	login
	* @return	boolean	true if valid
	*/
	function isLogin($a_login)
	{
		if (empty($a_login))
		{
			return false;
		}

		if (strlen($a_login) < 3)
		{
			return false;
		}

		// FIXME - If ILIAS is configured to use RFC 822
		//         compliant mail addresses we should not
		//         allow the @ character.
		if (!ereg("^[A-Za-z0-9_\.\+\*\@!\$\%\~\-]+$", $a_login))
		{
			return false;
		}

		return true;
	}

	/**
	* shorten a string to given length.
	* Adds 3 dots at the end of string (optional)
	* TODO: do not cut within words (->wordwrap function)
	* @access	public
	* @param	string	string to be shortened
	* @param	integer	string length in chars
	* @param	boolean	adding 3 dots (true) or not (false, default)
	* @param	truncate at first blank after $a_len characters
	* @return	string 	shortended string
	* @static
	* 
	*/
	public static function shortenText ($a_str, $a_len, $a_dots = false, $a_next_blank = false,
		$a_keep_extension = false)
	{
		include_once("./Services/Utilities/classes/class.ilStr.php");
		if (ilStr::strLen($a_str) > $a_len)
		{
			if ($a_next_blank)
			{
				$len = ilStr::strPos($a_str, " ", $a_len);
			}
			else
			{
				$len = $a_len;
			}
			// BEGIN WebDAV
			//             - Shorten names in the middle, before the filename extension
			//             Workaround for Windows WebDAV Client:
			//             Use the unicode ellipsis symbol for shortening instead of
			//             three full stop characters.
			if ($a_keep_extension)
			{
				$p = strrpos($a_str, '.');	// this messes up normal shortening, see bug #6190
			}
			if ($p === false || $p == 0 || strlen($a_str) - $p > $a_len)
			{
				$a_str = ilStr::subStr($a_str,0,$len);
				if ($a_dots)
				{
					$a_str .= "\xe2\x80\xa6"; // UTF-8 encoding for Unicode ellipsis character.
				}
			}
			else
			{
				if ($a_dots)
				{
					$a_str = ilStr::subStr($a_str,0,$len - (strlen($a_str) - $p + 1))."\xe2\x80\xa6".substr($a_str, $p);
				}
				else
				{
					$a_str = ilStr::subStr($a_str,0,$len - (strlen($a_str) - $p + 1)).substr($a_str, $p);
				}
			}
		}

		return $a_str;
	}

	/**
	* Ensure that the maximum word lenght within a text is not longer
	* than $a_len
	*
	* @param	string		input string
	* @param	integer		max. word length
	* @param	boolean		append "..." to shortened words
	* @static
	* 
	*/
	public static function shortenWords($a_str, $a_len = 30, $a_dots = true)
	{
		include_once("./Services/Utilities/classes/class.ilStr.php");
		$str_arr = explode(" ", $a_str);
		
		for ($i = 0; $i < count($str_arr); $i++)
		{
			if (ilStr::strLen($str_arr[$i]) > $a_len)
			{
				$str_arr[$i] = ilStr::subStr($str_arr[$i], 0, $a_len);
				if ($a_dots)
				{
					$str_arr[$i].= "...";
				}
			}
		}
		
		return implode($str_arr, " ");
	}

	/**
	* converts a string of format var1 = "val1" var2 = "val2" ... into an array
	*
	* @param	string		$a_str		string in format: var1 = "val1" var2 = "val2" ...
	*
	* @return	array		array of variable value pairs
	* @static
	* 
	*/
	public static function attribsToArray($a_str)
	{
		$attribs = array();
		while (is_int(strpos($a_str, "=")))
		{
			$eq_pos = strpos($a_str, "=");
			$qu1_pos = strpos($a_str, "\"");
			$qu2_pos = strpos(substr($a_str, $qu1_pos + 1), "\"") + $qu1_pos + 1;
			if (is_int($eq_pos) && is_int($qu1_pos) && is_int($qu2_pos))
			{
				$var = trim(substr($a_str, 0, $eq_pos));
				$val = trim(substr($a_str, $qu1_pos + 1, ($qu2_pos - $qu1_pos) - 1));
				$attribs[$var] = $val;
				$a_str = substr($a_str, $qu2_pos + 1);
			}
			else
			{
				$a_str = "";
			}
		}
		return $attribs;
	}

	/**
	* Copies content of a directory $a_sdir recursively to a directory $a_tdir
	* @param	string	$a_sdir		source directory
	* @param	string	$a_tdir		target directory
	* @param 	boolean $preserveTimeAttributes	if true, ctime will be kept.
	*
	* @return	boolean	TRUE for sucess, FALSE otherwise
	* @access	public
	* @static
	* 
	*/
	public static function rCopy ($a_sdir, $a_tdir, $preserveTimeAttributes = false)
	{
		// check if arguments are directories
		if (!@is_dir($a_sdir) or
		!@is_dir($a_tdir))
		{
			return FALSE;
		}

		// read a_sdir, copy files and copy directories recursively
		$dir = opendir($a_sdir);

		while($file = readdir($dir))
		{
			if ($file != "." and
			$file != "..")
			{
				// directories
				if (@is_dir($a_sdir."/".$file))
				{
					if (!@is_dir($a_tdir."/".$file))
					{
						if (!ilUtil::makeDir($a_tdir."/".$file))
						return FALSE;

						//chmod($a_tdir."/".$file, 0775);
					}

					if (!ilUtil::rCopy($a_sdir."/".$file,$a_tdir."/".$file))
					{
						return FALSE;
					}
				}

				// files
				if (@is_file($a_sdir."/".$file))
				{
					if (!copy($a_sdir."/".$file,$a_tdir."/".$file))
					{
						return FALSE;
					}
					if ($preserveTimeAttributes)
						touch($a_tdir."/".$file, filectime($a_sdir."/".$file));
				}
			}
		}
		return TRUE;
	}

	/**
	* get webspace directory
	*
	* @param	string		$mode		use "filesystem" for filesystem operations
	*									and "output" for output operations, e.g. images
	* @static
	*
	*/
	public static function getWebspaceDir($mode = "filesystem")
	{
		global $ilias;

		if ($mode == "filesystem")
		{
			return "./".ILIAS_WEB_DIR."/".$ilias->client_id;
		}
		else
		{
			if (defined("ILIAS_MODULE"))
			{
				return "../".ILIAS_WEB_DIR."/".$ilias->client_id;
			}
			else
			{
				return "./".ILIAS_WEB_DIR."/".$ilias->client_id;
			}
		}

		//return $ilias->ini->readVariable("server","webspace_dir");
	}

	/**
	* get data directory (outside webspace)
	* 
	* @static
	* 
	*/
	public static function getDataDir()
	{
		return CLIENT_DATA_DIR;
		//global $ilias;

		//return $ilias->ini->readVariable("server", "data_dir");
	}

	/**
	* reads all active sessions from db and returns users that are online
	* OR returns only one active user if a user_id is given
	*
	* @param	integer	user_id (optional)
	* @return	array
	* @static
	* 
	*/
	public static function getUsersOnline($a_user_id = 0)
	{
		include_once("./Services/User/classes/class.ilObjUser.php");
		return ilObjUser::_getUsersOnline($a_user_id);
	}

	/**
	* reads all active sessions from db and returns users that are online
	* and who have a local role in a group or a course for which the
    * the current user has also a local role.
	*
	* @param	integer	user_id User ID of the current user.
	* @return	array
	* @static
	* 
	*/
	public static function getAssociatedUsersOnline($a_user_id)
	{
		include_once("./Services/User/classes/class.ilObjUser.php");
		return ilObjUser::_getAssociatedUsersOnline($a_user_id);
	}

	/**
	* Create a temporary file in an ILIAS writable directory
	*
	* @return	string File name of the temporary file
	* @static
	* 
	*/
	public static function ilTempnam()
	{
		$temp_path = ilUtil::getDataDir() . "/temp";
		if (!is_dir($temp_path))
		{
			ilUtil::createDirectory($temp_path);
		}
		$temp_name = tempnam($temp_path, "tmp");
		// --->
		// added the following line because tempnam creates a backslash on some
		// Windows systems which leads to problems, because the "...\tmp..." can be
		// interpreted as "...{TAB-CHARACTER}...". The normal slash works fine
		// even under windows (Helmut Schottmüller, 2005-08-31)
		$temp_name = str_replace("\\", "/", $temp_name);
		// --->
		unlink($temp_name);
		return $temp_name;
	}

	/**
	* create directory
	*
	* deprecated use makeDir() instead!
	* 
	* @static
	* 
	*/
	public static function createDirectory($a_dir, $a_mod = 0755)
	{
		ilUtil::makeDir($a_dir);
		//@mkdir($a_dir);
		//@chmod($a_dir, $a_mod);
	}


	/**
	* unzip file
	*
	* @param	string	$a_file		full path/filename
	* @param	boolean	$overwrite	pass true to overwrite existing files
	* @static
	* 
	*/
	public static function unzip($a_file, $overwrite = false, $a_flat = false)
	{
		if (!is_file($a_file))
		{
			return;
		}
		
		// if flat, move file to temp directory first
		if ($a_flat)
		{
			$tmpdir = ilUtil::ilTempnam();
			ilUtil::makeDir($tmpdir);
			copy($a_file, $tmpdir.DIRECTORY_SEPARATOR.basename($a_file));
			$orig_file = $a_file;
			$a_file = $tmpdir.DIRECTORY_SEPARATOR.basename($a_file);
			$origpathinfo = pathinfo($orig_file);
		}
		
		$pathinfo = pathinfo($a_file);
		$dir = $pathinfo["dirname"];
		$file = $pathinfo["basename"];

		// unzip
		$cdir = getcwd();
		chdir($dir);
		$unzip = PATH_TO_UNZIP;

		// the following workaround has been removed due to bug
		// http://www.ilias.de/mantis/view.php?id=7578
		// since the workaround is quite old, it may not be necessary
		// anymore, alex 9 Oct 2012
/*
		// workaround for unzip problem (unzip of subdirectories fails, so
		// we create the subdirectories ourselves first)
		// get list
		$unzipcmd = "-Z -1 ".ilUtil::escapeShellArg($file);
		$arr = ilUtil::execQuoted($unzip, $unzipcmd);
		$zdirs = array();

		foreach($arr as $line)
		{
			if(is_int(strpos($line, "/")))
			{
				$zdir = substr($line, 0, strrpos($line, "/"));
				$nr = substr_count($zdir, "/");
				//echo $zdir." ".$nr."<br>";
				while ($zdir != "")
				{
					$nr = substr_count($zdir, "/");
					$zdirs[$zdir] = $nr;				// collect directories
					//echo $dir." ".$nr."<br>";
					$zdir = substr($zdir, 0, strrpos($zdir, "/"));
				}
			}
		}

		asort($zdirs);

		foreach($zdirs as $zdir => $nr)				// create directories
		{
			ilUtil::createDirectory($zdir);
		}
*/

		// real unzip
		if (!$overwrite)
		{
			$unzipcmd = ilUtil::escapeShellArg($file);
		}
		else
		{
			$unzipcmd = "-o ".ilUtil::escapeShellArg($file);
		}
		ilUtil::execQuoted($unzip, $unzipcmd);

		chdir($cdir);
		
		// if flat, get all files and move them to original directory
		if ($a_flat)
		{
			include_once("./Services/Utilities/classes/class.ilFileUtils.php");
			$filearray = array();
			ilFileUtils::recursive_dirscan($tmpdir, $filearray);
			if (is_array($filearray["file"]))
			{
				foreach ($filearray["file"] as $k => $f)
				{
					if (substr($f, 0, 1) != "." && $f != basename($orig_file))
					{
						copy($filearray["path"][$k].$f, $origpathinfo["dirname"].DIRECTORY_SEPARATOR.$f);
					}
				}
			}
			ilUtil::delDir($tmpdir);
		}
	}

	/**
	*	zips given directory/file into given zip.file
	*
	* @static
	*
	*/
	public static function zip($a_dir, $a_file, $compress_content = false)
	{
		$cdir = getcwd();

		if($compress_content)
		{
			$a_dir .="/*";
			$pathinfo = pathinfo($a_dir);
			chdir($pathinfo["dirname"]);
		}
		
		$pathinfo = pathinfo($a_file);
		$dir = $pathinfo["dirname"];
		$file = $pathinfo["basename"];

		if(!$compress_content)
		{
			chdir($dir);
		}

		$zip = PATH_TO_ZIP;
		
		if(!$zip)
		{
			chdir($cdir);
			return false;
		}

		if (is_array($a_dir))
		{
			$source = "";
			foreach($a_dir as $dir)
			{
				$name = basename($dir);
				$source.= " ".ilUtil::escapeShellArg($name);
			}
		}
		else
		{
			$name = basename($a_dir);
			if (trim($name) != "*")
			{
				$source = ilUtil::escapeShellArg($name);
			}
			else
			{
				$source = $name;
			}
		}

		$zipcmd = "-r ".ilUtil::escapeShellArg($a_file)." ".$source;
		ilUtil::execQuoted($zip, $zipcmd);
		chdir($cdir);
		return true;
	}

	public static function CreateIsoFromFolder($a_dir, $a_file)
	{
		$cdir = getcwd();

		$pathinfo = pathinfo($a_dir);
		chdir($pathinfo["dirname"]);
		
		$pathinfo = pathinfo($a_file);
		$dir = $pathinfo["dirname"];
		$file = $pathinfo["basename"];	$zipcmd = "-r ".ilUtil::escapeShellArg($a_file)." ".$source;

		$mkisofs = PATH_TO_MKISOFS;
		if(!$mkisofs)
		{
			chdir($cdir);
			return false;
		}
		
		$name = basename($a_dir);
		$source = ilUtil::escapeShellArg($name);

		$zipcmd = "-r -J -o ".$a_file." ".$source;
		ilUtil::execQuoted($mkisofs, $zipcmd);
		chdir($cdir);
		return true;
	}
	
	/**
	* get convert command
	*
	* @deprecated
	* @see ilUtil::execConvert()
	* @static
	* 
	*/
	public static function getConvertCmd()
	{
		return PATH_TO_CONVERT;
	}
	
	/**
	 * execute convert command
	 *
	 * @param	string	$args
	 * @static
	 * 
	 */
	public static function execConvert($args)
	{
		ilUtil::execQuoted(PATH_TO_CONVERT, $args);
	}
	
	/**
	 * Compare convert version numbers
	 * 
	 * @param string $a_version w.x.y-z
	 * @return bool
	 */
	public static function isConvertVersionAtLeast($a_version)
	{		
		$current_version = ilUtil::execQuoted(PATH_TO_CONVERT, "--version");
		$current_version = self::processConvertVersion($current_version[0]);
		$version = self::processConvertVersion($a_version);
		if($current_version >= $version)
		{		
			return true;
		}
		return false;
	}
	
	/**
	 * Parse convert version string, e.g. 6.3.8-3, into integer
	 * 
	 * @param string $a_version w.x.y-z
	 * @return int
	 */
	protected static function processConvertVersion($a_version)
	{
		if(preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)([\.|\-]([0-9]+))?/", $a_version, $match))
		{
			$version = str_pad($match[1], 2, 0, STR_PAD_LEFT).
				str_pad($match[2], 2, 0, STR_PAD_LEFT).
				str_pad($match[3], 2, 0, STR_PAD_LEFT).
				str_pad($match[5], 2, 0, STR_PAD_LEFT);				
			return (int)$version;
		}
	}

	/**
	* convert image
	*
	* @param	string		$a_from				source file
	* @param	string		$a_to				target file
	* @param	string		$a_target_format	target image file format
	* @static
	* 
	*/
	public static function convertImage($a_from, $a_to, $a_target_format = "", $a_geometry = "",
		$a_background_color = "")
	{
		$format_str = ($a_target_format != "")
			? strtoupper($a_target_format).":"
			: "";
		$geometry = "";
		if ($a_geometry != "")
		{
			if (is_int(strpos($a_geometry, "x")))
			{
				$geometry = " -geometry ".$a_geometry." ";
			}
			else
			{
				$geometry = " -geometry ".$a_geometry."x".$a_geometry." ";
			}
		}
		
		$bg_color = ($a_background_color != "")
			? " -background color ".$a_background_color." "
			: "";
		$convert_cmd = ilUtil::escapeShellArg($a_from)." ".$bg_color.$geometry.ilUtil::escapeShellArg($format_str.$a_to);

		ilUtil::execConvert($convert_cmd);
	}

	/**
	* resize image
	*
	* @param	string		$a_from				source file
	* @param	string		$a_to				target file
	* @param	string		$a_width			target width
	* @param	string		$a_height			target height
	* @static
	* 
	*/
	public static function resizeImage($a_from, $a_to, $a_width, $a_height, $a_constrain_prop = false)
	{
		if ($a_constrain_prop)
		{
			$size = " -geometry ".$a_width."x".$a_height." ";
		}
		else
		{
			$size = " -resize ".$a_width."x".$a_height."! ";
		}
		$convert_cmd = ilUtil::escapeShellArg($a_from)." ".$size.ilUtil::escapeShellArg($a_to);

		ilUtil::execConvert($convert_cmd);
	}
	
	/**
	* Build img tag
	* 
	* @static
	* 
	*/
	public static function img($a_src, $a_alt = "", $a_width = "", $a_height = "", $a_border = 0, $a_id = "")
	{
		$img = '<img src="'.$a_src.'"';
		if ($a_alt != "")
		{
			$img.= ' alt="'.$a_alt.'" title="'.$a_alt.'"';
		}
		if ($a_width != "")
		{
			$img.= ' width="'.$a_width.'"';
		}
		if ($a_height != "")
		{
			$img.= ' height="'.$a_height.'"';
		}
		if ($a_id != "")
		{
			$img.= ' id="'.$a_id.'"';
		}
		$img.= ' border="'.(int) $a_border.'"/>';

		return $img;
	}

	/**
	*	produce pdf out of html with htmldoc
	*   @param  html    String  HTML-Data given to create pdf-file
	*   @param  pdf_file    String  Filename to save pdf in
	*   @static
	*   
	*/
	public static function html2pdf($html, $pdf_file)
	{
		$html_file = str_replace(".pdf",".html",$pdf_file);

		$fp = fopen( $html_file ,"wb");
		fwrite($fp, $html);
		fclose($fp);

		ilUtil::htmlfile2pdf($html_file,$pdf_file);
	}

	/**
	*	produce pdf out of html with htmldoc
	*   @param  html    String  HTML-Data given to create pdf-file
	*   @param  pdf_file    String  Filename to save pdf in
	* @static
	*/
	public static function htmlfile2pdf($html_file, $pdf_file)
	{
		$htmldoc_path = PATH_TO_HTMLDOC;

		$htmldoc = "--no-toc ";
		$htmldoc .= "--no-jpeg ";
		$htmldoc .= "--webpage ";
		$htmldoc .= "--outfile " . ilUtil::escapeShellArg($pdf_file) . " ";
		$htmldoc .= "--bodyfont Arial ";
		$htmldoc .= "--charset iso-8859-15 ";
		$htmldoc .= "--color ";
		$htmldoc .= "--size A4  ";      // --landscape
		$htmldoc .= "--format pdf ";
		$htmldoc .= "--footer ... ";
		$htmldoc .= "--header ... ";
		$htmldoc .= "--left 60 ";
		// $htmldoc .= "--right 200 ";
		$htmldoc .= $html_file;
		ilUtil::execQuoted($htmldoc_path, $htmldoc);

	}

	/**
	*   deliver data for download via browser.
	*   
	* @static
	*   
	*/
	public static function deliverData($a_data, $a_filename, $mime = "application/octet-stream", $charset = "")
	{
		$disposition = "attachment"; // "inline" to view file in browser or "attachment" to download to hard disk
		//		$mime = "application/octet-stream"; // or whatever the mime type is

		include_once './Services/Http/classes/class.ilHTTPS.php';
		
		//if($_SERVER['HTTPS'])
		if( ilHTTPS::getInstance()->isDetected() )
		{

			// Added different handling for IE and HTTPS => send pragma after content informations
			/**
			* We need to set the following headers to make downloads work using IE in HTTPS mode.
			*/
			#header("Pragma: ");
			#header("Cache-Control: ");
			#header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			#header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			#header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
			#header("Cache-Control: post-check=0, pre-check=0", false);
		}
		else if ($disposition == "attachment")
		{
			header("Cache-control: private");
		}
		else
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
		}

		$ascii_filename = ilUtil::getASCIIFilename($a_filename);

		if (strlen($charset))
		{
			$charset = "; charset=$charset";
		}
		header("Content-Type: $mime$charset");
		header("Content-Disposition:$disposition; filename=\"".$ascii_filename."\"");
		header("Content-Description: ".$ascii_filename);
		header("Content-Length: ".(string)(strlen($a_data)));

		//if($_SERVER['HTTPS'])
		if( ilHTTPS::getInstance()->isDetected() )
		{
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
		}

		header("Connection: close");
		echo $a_data;
		exit;
	}

	// BEGIN WebDAV: Show file in browser or provide it as attachment
	/**
	*   deliver file for download via browser.
	* @param $mime Mime of the file
	* @param $isInline Set this to true, if the file shall be shown in browser
	* @static
	* 
	*/
	public static function deliverFile($a_file, $a_filename,$a_mime = '', $isInline = false, $removeAfterDelivery = false,
		$a_exit_after = true)
	{
		// should we fail silently?
		if(!file_exists($a_file))
		{
			return false;
		}	

		if ($isInline) {
			$disposition = "inline"; // "inline" to view file in browser
		} else {
			$disposition =  "attachment"; // "attachment" to download to hard disk
			//$a_mime = "application/octet-stream"; // override mime type to ensure that no browser tries to show the file anyway.
		}
	// END WebDAV: Show file in browser or provide it as attachment

		if(strlen($a_mime))
		{
			$mime = $a_mime;
		}
		else
		{
			$mime = "application/octet-stream"; // or whatever the mime type is
		}
	// BEGIN WebDAV: Removed broken HTTPS code.
	// END WebDAV: Removed broken HTTPS code.
		if ($disposition == "attachment")
		{
			header("Cache-control: private");
		}
		else
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
		}

		$ascii_filename = ilUtil::getASCIIFilename($a_filename);

		header("Content-Type: $mime");
		header("Content-Disposition:$disposition; filename=\"".$ascii_filename."\"");
		header("Content-Description: ".$ascii_filename);
		
		// #7271: if notice gets thrown download will fail in IE
		$filesize = @filesize($a_file);
		if ($filesize)
		{
			header("Content-Length: ".(string)$filesize);
		}

		include_once './Services/Http/classes/class.ilHTTPS.php';
		#if($_SERVER['HTTPS'])
		if(ilHTTPS::getInstance()->isDetected())
		{
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
		}

		header("Connection: close");
		ilUtil::readFile( $a_file );
		if ($removeAfterDelivery)
		{
			unlink ($a_file);
		}
		if ($a_exit_after)
		{
			exit;
		}
	}


	/**
	* there are some known problems with the original readfile method, which
	* sometimes truncates delivered files regardless of php.ini setting
	* (see http://de.php.net/manual/en/function.readfile.php) use this
	* method to avoid these problems.
	* 
	* @static
	* 
	*/
	public static function readFile($a_file)
	{
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$handle = fopen($a_file, 'rb');
		if ($handle === false)
		{
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			print $buffer;
		}
		return fclose($handle);
	}

	/**
	* convert utf8 to ascii filename
	*
	* @param	string		$a_filename		utf8 filename
	* @static
	* 
	*/
	public static function getASCIIFilename($a_filename)
	{
		// The filename must be converted to ASCII, as of RFC 2183,
		// section 2.3.

		/// Implementation note:
		/// 	The proper way to convert charsets is mb_convert_encoding.
		/// 	Unfortunately Multibyte String functions are not an
		/// 	installation requirement for ILIAS 3.
		/// 	Codelines behind three slashes '///' show how we would do
		/// 	it using mb_convert_encoding.
		/// 	Note that mb_convert_encoding has the bad habit of
		/// 	substituting unconvertable characters with HTML
		/// 	entitities. Thats why we need a regular expression which
		/// 	replaces HTML entities with their first character.
		/// 	e.g. &auml; => a

		/// $ascii_filename = mb_convert_encoding($a_filename,'US-ASCII','UTF-8');
		/// $ascii_filename = preg_replace('/\&(.)[^;]*;/','\\1', $ascii_filename);

		$ascii_filename = htmlentities($a_filename, ENT_NOQUOTES, 'UTF-8');
		$ascii_filename = preg_replace('/\&(.)[^;]*;/', '\\1', $ascii_filename);
		$ascii_filename = preg_replace('/[\x7f-\xff]/', '_', $ascii_filename);
		
		// OS do not allow the following characters in filenames: \/:*?"<>|
		$ascii_filename = preg_replace('/[:\x5c\/\*\?\"<>\|]/', '_', $ascii_filename);

		return $ascii_filename;
	}

	/**
	* Encodes HTML entities outside of HTML tags
	* 
	* @static
	* 
	*/
	public static function htmlentitiesOutsideHTMLTags($htmlText)
	{
		$matches = Array();
		$sep = '###HTMLTAG###';

		preg_match_all("@<[^>]*>@", $htmlText, $matches);   
		$tmp = preg_replace("@(<[^>]*>)@", $sep, $htmlText);
		$tmp = explode($sep, $tmp);

		for ($i=0; $i<count($tmp); $i++)
			$tmp[$i] = htmlentities($tmp[$i], ENT_COMPAT, "UTF-8");

		$tmp = join($sep, $tmp);

		for ($i=0; $i<count($matches[0]); $i++)
			$tmp = preg_replace("@$sep@", $matches[0][$i], $tmp, 1);

		return $tmp;
	}

	/**
	* get full java path (dir + java command)
	* 
	* @static
	* 
	*/
	public static function getJavaPath()
	{
		return PATH_TO_JAVA;
		//global $ilias;

		//return $ilias->getSetting("java_path");
	}

	/**
	* append URL parameter string ("par1=value1&par2=value2...")
	* to given URL string
	* 
	* @static
	* 
	*/
	public static function appendUrlParameterString($a_url, $a_par, $xml_style = false)
	{
		$amp = $xml_style
			? "&amp;"
			: "&";
		
		$url = (is_int(strpos($a_url, "?")))
			? $a_url.$amp.$a_par
			: $a_url."?".$a_par;

		return $url;
	}

	/**
	* creates a new directory and inherits all filesystem permissions of the parent directory
	* You may pass only the name of your new directory or with the entire path or relative path information.
	*
	* examples:
	* a_dir = /tmp/test/your_dir
	* a_dir = ../test/your_dir
	* a_dir = your_dir (--> creates your_dir in current directory)
	*
	* @access	public
	* @param	string	[path] + directory name
	* @return	boolean
	* @static
	*
	*/
	public static function makeDir($a_dir)
	{
		$a_dir = trim($a_dir);

		// remove trailing slash (bugfix for php 4.2.x)
		if (substr($a_dir,-1) == "/")
		{
			$a_dir = substr($a_dir,0,-1);
		}

		// check if a_dir comes with a path
		if (!($path = substr($a_dir,0, strrpos($a_dir,"/") - strlen($a_dir))))
		{
			$path = ".";
		}

		// create directory with file permissions of parent directory
		umask(0000);
		return @mkdir($a_dir,fileperms($path));
	}


	/**
	* Create a new directory and all parent directories
	*
	* Creates a new directory and inherits all filesystem permissions of the parent directory
	* If the parent directories doesn't exist, they will be created recursively.
	* The directory name NEEDS TO BE an absolute path, because it seems that relative paths
	* are not working with PHP's file_exists function.
	*
	* @author Helmut Schottmüller <hschottm@tzi.de>
	* @param string $a_dir The directory name to be created
	* @access public
	* @static
	* 
	*/
	public static function makeDirParents($a_dir)
	{
		$dirs = array($a_dir);
		$a_dir = dirname($a_dir);
		$last_dirname = '';

		while($last_dirname != $a_dir)
		{
			array_unshift($dirs, $a_dir);
			$last_dirname = $a_dir;
			$a_dir = dirname($a_dir);
		}

		// find the first existing dir
		$reverse_paths = array_reverse($dirs, TRUE);
		$found_index = -1;
		foreach ($reverse_paths as $key => $value)
		{
			if ($found_index == -1)
			{
				if (is_dir($value))
				{
					$found_index = $key;
				}
			}
		}

		umask(0000);
		foreach ($dirs as $dirindex => $dir)
		{
			// starting with the longest existing path
			if ($dirindex >= $found_index)
			{
				if (! file_exists($dir))
				{
					if (strcmp(substr($dir,strlen($dir)-1,1),"/") == 0)
					{
						// on some systems there is an error when there is a slash
						// at the end of a directory in mkdir, see Mantis #2554
						$dir = substr($dir,0,strlen($dir)-1);
					}
					if (! mkdir($dir, $umask))
					{
						error_log("Can't make directory: $dir");
						return false;
					}
				}
				elseif (! is_dir($dir))
				{
					error_log("$dir is not a directory");
					return false;
				}
				else
				{
					// get umask of the last existing parent directory
					$umask = fileperms($dir);
				}
			}
		}
		return true;
	}

	/**
	* removes a dir and all its content (subdirs and files) recursively
	*
	* @access	public
	* @param	string	dir to delete
	* @author	Unknown <flexer@cutephp.com> (source: http://www.php.net/rmdir)
	* @static
	* 
	*/
	public static function delDir($a_dir, $a_clean_only = false)
	{
		if (!is_dir($a_dir) || is_int(strpos($a_dir, "..")))
		{
			return;
		}

		$current_dir = opendir($a_dir);

		$files = array();

		// this extra loop has been necessary because of a strange bug
		// at least on MacOS X. A looped readdir() didn't work
		// correctly with larger directories
		// when an unlink happened inside the loop. Getting all files
		// into the memory first solved the problem.
		while($entryname = readdir($current_dir))
		{
			$files[] = $entryname;
		}

		foreach($files as $file)
		{
			if(is_dir($a_dir."/".$file) and ($file != "." and $file!=".."))
			{
				ilUtil::delDir(${a_dir}."/".${file});
			}
			elseif ($file != "." and $file != "..")
			{
				unlink(${a_dir}."/".${file});
			}
		}

		closedir($current_dir);
		if (!$a_clean_only)
		{
			@rmdir(${a_dir});
		}
	}


	/**
	* get directory
	* 
	* @static
	* 
	*/
	public static function getDir($a_dir, $a_rec = false, $a_sub_dir = "")
	{
		$current_dir = opendir($a_dir.$a_sub_dir);

		$dirs = array();
		$files = array();
		$subitems = array();
		while($entry = readdir($current_dir))
		{
			if(is_dir($a_dir."/".$entry))
			{
				$dirs[$entry] = array("type" => "dir", "entry" => $entry,
					"subdir" => $a_sub_dir);
				if ($a_rec && $entry != "." && $entry != "..")
				{
					$si = ilUtil::getDir($a_dir, true, $a_sub_dir."/".$entry);
					$subitems = array_merge($subitems, $si);
				}
			}
			else
			{
				if ($entry != "." && $entry != "..")
				{
					$size = filesize($a_dir.$a_sub_dir."/".$entry);
					$files[$entry] = array("type" => "file", "entry" => $entry,
					"size" => $size, "subdir" => $a_sub_dir);
				}
			}
		}
		ksort($dirs);
		ksort($files);

		return array_merge($dirs, $files, $subitems);
	}

	/**
	* Strip slashes from array
	* 
	* @static
	* 
	*/
	public static function stripSlashesArray($a_arr, $a_strip_html = true, $a_allow = "")
	{
		if (is_array($a_arr))
		{
			foreach ($a_arr as $k => $v)
			{
				$a_arr[$k] = ilUtil::stripSlashes($v, $a_strip_html, $a_allow);
			}
		}

		return $a_arr;
	}
	
	/**
	* Strip slashes from array and sub-arrays
	* 
	* @static
	* 
	*/
	public static function stripSlashesRecursive($a_data, $a_strip_html = true, $a_allow = "")
	{
		if (is_array($a_data))
		{
			foreach ($a_data as $k => $v)
			{
				if (is_array($v))
				{
					$a_data[$k] = ilUtil::stripSlashesRecursive($v, $a_strip_html, $a_allow);
				}
				else
				{
					$a_data[$k] = ilUtil::stripSlashes($v, $a_strip_html, $a_allow);
				}
			}
		}
		else
		{
			$a_data = ilUtil::stripSlashes($a_data, $a_strip_html, $a_allow);
		}

		return $a_data;
	}

	/**
	* strip slashes if magic qoutes is enabled
	*
	* @param	boolean		strip also html tags
	* @static
	* 
	*/
	public static function stripSlashes($a_str, $a_strip_html = true, $a_allow = "")
	{
		if (ini_get("magic_quotes_gpc"))
		{
			$a_str = stripslashes($a_str);
		}
//echo "<br><br>-".$a_strip_html."-".htmlentities($a_str);
//echo "<br>-".htmlentities(ilUtil::secureString($a_str, $a_strip_html, $a_allow));
		return ilUtil::secureString($a_str, $a_strip_html, $a_allow);
	}
	
	/**
	* strip slashes if magic qoutes is enabled
	*
	* @param	string		string
	* @static
	* 
	*/
	public static function stripOnlySlashes($a_str)
	{
		if (ini_get("magic_quotes_gpc"))
		{
			$a_str = stripslashes($a_str);
		}

		return $a_str;
	}

	/**
	* Remove unsecure tags
	* 
	* @static
	* 
	*/
	public static function secureString($a_str, $a_strip_html = true, $a_allow = "")
	{
		// check whether all allowed tags can be made secure
		$only_secure = true;
		$allow_tags = explode(">", $a_allow);
		$sec_tags = ilUtil::getSecureTags();
		$allow_array = array();
		foreach($allow_tags as $allow)
		{
			if ($allow != "")
			{
				$allow = str_replace("<", "", $allow);

				if (!in_array($allow, $sec_tags))
				{
					$only_secure = false;
				}
				$allow_array[] = $allow;
			}
		}

		// default behaviour: allow only secure tags 1:1
		if (($only_secure || $a_allow == "") && $a_strip_html)
		{
			if ($a_allow == "")
			{
				$allow_array = array ("b", "i", "strong", "em", "code", "cite",
					"gap", "sub", "sup", "pre", "strike");
			}

			// this currently removes parts of strings like "a <= b"
			// because "a <= b" is treated like "<spam onclick='hurt()'>ss</spam>"
			$a_str = ilUtil::maskSecureTags($a_str, $allow_array);
			$a_str = strip_tags($a_str);		// strip all other tags
			$a_str = ilUtil::unmaskSecureTags($a_str, $allow_array);

			// a possible solution could be something like:
			// $a_str = str_replace("<", "&lt;", $a_str);
			// $a_str = str_replace(">", "&gt;", $a_str);
			// $a_str = ilUtil::unmaskSecureTags($a_str, $allow_array);
			//
			// output would be ok then, but input fields would show
			// "a &lt;= b" for input "a <= b" if data is brought back to a form
		}
		else
		{
			// only for scripts, that need to allow more/other tags and parameters
			if ($a_strip_html)
			{
				$a_str = ilUtil::stripScriptHTML($a_str, $a_allow);
			}
		}

		return $a_str;
	}

	public static function getSecureTags()
	{
		return array("strong", "em", "u", "strike", "ol", "li", "ul", "p", "div",
			"i", "b", "code", "sup", "sub", "pre", "gap", "a", "img");
	}

	public static function maskSecureTags($a_str, $allow_array)
	{
		foreach ($allow_array as $t)
		{
			switch($t)
			{
				case "a":
					$a_str = ilUtil::maskAttributeTag($a_str, "a", "href");
					break;

				case "img":
					$a_str = ilUtil::maskAttributeTag($a_str, "img", "src");
					break;

				case "p":
				case "div":
					$a_str = ilUtil::maskTag($a_str, $t, array(
						array("param" => "align", "value" => "left"),
						array("param" => "align", "value" => "center"),
						array("param" => "align", "value" => "justify"),
						array("param" => "align", "value" => "right")
						));
					break;

				default:
					$a_str = ilUtil::maskTag($a_str, $t);
					break;
			}
		}

		return $a_str;
	}

	public static function unmaskSecureTags($a_str, $allow_array)
	{
		foreach ($allow_array as $t)
		{
			switch($t)
			{
				case "a":
					$a_str = ilUtil::unmaskAttributeTag($a_str, "a", "href");
					break;

				case "img":
					$a_str = ilUtil::unmaskAttributeTag($a_str, "img", "src");
					break;

				case "p":
				case "div":
					$a_str = ilUtil::unmaskTag($a_str, $t, array(
						array("param" => "align", "value" => "left"),
						array("param" => "align", "value" => "center"),
						array("param" => "align", "value" => "justify"),
						array("param" => "align", "value" => "right")
						));
					break;

				default:
					$a_str = ilUtil::unmaskTag($a_str, $t);
					break;
			}
		}

		return $a_str;
	}

	/**
	* Remove unsecure characters from a plain text string.
	* This function currently returns the string without doing any changes.
	* 
	* @static
	* 
	*/
	public static function securePlainString($a_str)
	{
		if (ini_get("magic_quotes_gpc"))
		{
			return stripslashes($a_str);
		}
		else
		{
			return $a_str;
		}
	}
	/**
	* Encodes a plain text string into HTML for display in a browser.
	* This function encodes HTML special characters: < > & with &lt; &gt; &amp;
	* and converts newlines into <br>
	*
	* If $a_make_links_clickable is set to true, URLs in the plain string which
	* are considered to be safe, are made clickable.
	*
	*
	* @param string the plain text string
	* @param boolean set this to true, to make links in the plain string
	* clickable.
	* @param boolean set this to true, to detect goto links
	* @static
	* 
	*/
	public static function htmlencodePlainString($a_str, $a_make_links_clickable, $a_detect_goto_links = false)
	{
		$encoded = "";

		if ($a_make_links_clickable)
		{
			// Find text sequences in the plain text string which match
			// the URI syntax rules, and pass them to ilUtil::makeClickable.
			// Encode all other text sequences in the plain text string using
			// htmlspecialchars and nl2br.
			// The following expressions matches URI's as specified in RFC 2396.
			//
			// The expression matches URI's, which start with some well known
			// schemes, like "http:", or with "www.". This must be followed
			// by at least one of the following RFC 2396 expressions:
			// - alphanum:           [a-zA-Z0-9]
			// - reserved:           [;\/?:|&=+$,]
			// - mark:               [\\-_.!~*\'()]
			// - escaped:            %[0-9a-fA-F]{2}
			// - fragment delimiter: #
			// - uric_no_slash:      [;?:@&=+$,]
			$matches = array();
			$numberOfMatches = preg_match_all('/(?:(?:http|https|ftp|ftps|mailto):|www\.)(?:[a-zA-Z0-9]|[;\/?:|&=+$,]|[\\-_.!~*\'()]|%[0-9a-fA-F]{2}|#|[;?:@&=+$,])+/',$a_str, $matches, PREG_OFFSET_CAPTURE);
			$pos1 = 0;
			$encoded = "";
			foreach ($matches as $match)
			{
			}
			foreach ($matches[0] as $match)
			{
				$matched_text = $match[0];
				$pos2 = $match[1];
				if ($matched_offset != previous_offset)
				{
					// encode plain text
					$encoded .= nl2br(htmlspecialchars(substr($a_str, $pos1, $pos2 - $pos1)));
				}
				// encode URI
				$encoded .= ilUtil::makeClickable($matched_text, $a_detect_goto_links);


				$pos1 = $pos2 + strlen($matched_text);
			}
			if ($pos1 < strlen($a_str))
			{
				$encoded .= nl2br(htmlspecialchars(substr($a_str, $pos1)));
			}
		}
		else
		{
			$encoded = nl2br(htmlspecialchars($a_str));
		}
		return $encoded;
	}


	public static function maskAttributeTag($a_str, $tag, $tag_att)
	{
		global $ilLog;

		$ws = "[ \t\r\f\v\n]*";
		$att = $ws."[^>]*".$ws;

		while (eregi("\<($tag$att($tag_att$ws=$ws\"(([\$@!*()~;,_0-9A-z/:=%\\.&#?+\\-])*)\")$att)\>",
			$a_str, $found))
		{
			$un = array(".", "-", "+", "?", '$', "*", "(", ")");
			$esc = array();
			foreach($un as $v)
			{
				$esc[] = "\\".$v;
			}
			$ff = str_replace($un, $esc, $found[1]);

			$old_str = $a_str;
			$a_str = eregi_replace("\<".$ff."\>",
				"&lt;$tag $tag_att$tag_att=\"".$found[3]."\"&gt;", $a_str);
			if ($old_str == $a_str)
			{
				$ilLog->write("ilUtil::maskA-".htmlentities($old_str)." == ".
					htmlentities($a_str));
				return $a_str;
			}
		}
		$a_str = str_ireplace("</$tag>",
			"&lt;/$tag&gt;", $a_str);
		return $a_str;
	}

	public static function unmaskAttributeTag($a_str, $tag, $tag_att)
	{
		global $ilLog;

		while (eregi("&lt;($tag $tag_att$tag_att=\"(([\$@!*()~;,_0-9A-z/:=%\\.&#?+\\-])*)\")&gt;",
			$a_str, $found))
		{
			$un = array(".", "-", "+", "?", '$', "*", "(", ")");
			$esc = array();
			foreach($un as $v)
			{
				$esc[] = "\\".$v;
			}
			$ff = str_replace($un, $esc, $found[1]);

			$old_str = $a_str;
			$a_str = eregi_replace("&lt;".$ff."&gt;",
				"<$tag $tag_att=\"".ilUtil::secureLink($found[2])."\">", $a_str);
			if ($old_str == $a_str)
			{
				$ilLog->write("ilUtil::unmaskA-".htmlentities($old_str)." == ".
					htmlentities($a_str));
				return $a_str;
			}
		}
		$a_str = str_replace("&lt;/$tag&gt;", "</$tag>", $a_str);
		return $a_str;
	}

	public static function maskTag($a_str, $t, $fix_param = "")
	{
		$a_str = str_replace(array("<$t>", "<".strtoupper($t).">"),
			"&lt;".$t."&gt;", $a_str);
		$a_str = str_replace(array("</$t>", "</".strtoupper($t).">"),
			"&lt;/".$t."&gt;", $a_str);

		if (is_array($fix_param))
		{
			foreach ($fix_param	 as $p)
			{
				$k = $p["param"];
				$v = $p["value"];
				$a_str = str_replace("<$t $k=\"$v\">",
					"&lt;"."$t $k=\"$v\""."&gt;", $a_str);
			}
		}

		return $a_str;
	}

	public static function unmaskTag($a_str, $t, $fix_param = "")
	{
		$a_str = str_replace("&lt;".$t."&gt;", "<".$t.">", $a_str);
		$a_str = str_replace("&lt;/".$t."&gt;", "</".$t.">", $a_str);

		if (is_array($fix_param))
		{
			foreach ($fix_param	 as $p)
			{
				$k = $p["param"];
				$v = $p["value"];
				$a_str = str_replace("&lt;$t $k=\"$v\"&gt;",
					"<"."$t $k=\"$v\"".">", $a_str);
			}
		}
		return $a_str;
	}

	public static function secureLink($a_str)
	{
		$a_str = str_ireplace("javascript", "jvscrpt", $a_str);
		$a_str = str_ireplace(array("%00", "%0a", "%0d", "%1a", "&#00;", "&#x00;",
			"&#0;", "&#x0;", "&#x0a;", "&#x0d;", "&#10;", "&#13;"), "-", $a_str);
		return $a_str;
	}

	/**
	* strip only html tags (4.0) from text
	* $allowed contains tags to be allowed, in format <a><b>
	* tags a and b are allowed
	* todo: needs to be optimized-> not very efficient
	*
	* @param	string		$a_str		input string
	* @param	string		$a_allow	allowed tags, if an empty string is passed a default
	*									set of tags is allowed
	* @param	boolean		$a_rm_js	remove javascript attributes (onclick...)
	* @static
	* 
	*/
	public static function stripScriptHTML($a_str, $a_allow = "", $a_rm_js = true)
	{
		//$a_str = strip_tags($a_str, $a_allow);

		$negativestr = "a,abbr,acronym,address,applet,area,b,base,basefont,".
			"bdo,big,blockquote,body,br,button,caption,center,cite,code,col,".
			"colgroup,dd,del,dfn,dir,div,dl,dt,em,fieldset,font,form,frame,".
			"frameset,h1,h2,h3,h4,h5,h6,head,hr,html,i,iframe,img,input,ins,isindex,kbd,".
			"label,legend,li,link,map,menu,meta,noframes,noscript,object,ol,".
			"optgroup,option,p,param,q,s,samp,script,select,small,span,".
			"strike,strong,style,sub,sup,table,tbody,td,textarea,tfoot,th,thead,".
			"title,tr,tt,u,ul,var";
		$a_allow = strtolower ($a_allow);
		$negatives = explode(",",$negativestr);
		$outer_old_str = "";
		while($outer_old_str != $a_str)
		{
			$outer_old_str = $a_str;
			foreach ($negatives as $item)
			{
				$pos = strpos($a_allow, "<$item>");

				// remove complete tag, if not allowed
				if ($pos === false)
				{
					$old_str = "";
					while($old_str != $a_str)
					{
						$old_str = $a_str;
						$a_str = preg_replace("/<\/?\s*$item(\/?)\s*>/i", "", $a_str);
						$a_str = preg_replace("/<\/?\s*$item(\/?)\s+([^>]*)>/i", "", $a_str);
					}
				}
			}
		}

		if ($a_rm_js)
		{
			// remove all attributes if an "on..." attribute is given
			$a_str = preg_replace("/<\s*\w*(\/?)(\s+[^>]*)?(\s+on[^>]*)>/i", "", $a_str);

			// remove all attributes if a "javascript" is within tag
			$a_str = preg_replace("/<\s*\w*(\/?)\s+[^>]*javascript[^>]*>/i", "", $a_str);

			// remove all attributes if an "expression" is within tag
			// (IE allows something like <b style='width:expression(alert(1))'>test</b>)
			$a_str = preg_replace("/<\s*\w*(\/?)\s+[^>]*expression[^>]*>/i", "", $a_str);
		}

		return $a_str;
	}


	/**
	* add slashes if magic qoutes is disabled
	* don't use that for db inserts/updates! use prepareDBString
	* instead
	* 
	* @static
	* 
	*/
	public static function addSlashes($a_str)
	{
		if (ini_get("magic_quotes_gpc"))
		{
			return $a_str;
		}
		else
		{
			return addslashes($a_str);
		}
	}

	/**
	* prepares string output for html forms
	* @access	public
	* @param	string
	* @param	boolean		true: strip slashes, if magic_quotes is enabled
	*						use this if $a_str comes from $_GET or $_POST var,
	*						use false, if $a_str comes from database
	* @return	string
	* @static
	* 
	*/
	public static function prepareFormOutput($a_str, $a_strip = false)
	{
		if($a_strip)
		{
			$a_str = ilUtil::stripSlashes($a_str);
		}
		$a_str = htmlspecialchars($a_str);
		// Added replacement of curly brackets to prevent
		// problems with PEAR templates, because {xyz} will
		// be removed as unused template variable
		$a_str = str_replace("{", "&#123;", $a_str);
		$a_str = str_replace("}", "&#125;", $a_str);
		// needed for LaTeX conversion \\ in LaTeX is a line break
		// but without this replacement, php changes \\ to \
		$a_str = str_replace("\\", "&#92;", $a_str);
		return $a_str;
	}


	/**
	* prepare a string for db writing (insert/update)
	*
	* @param	string		$a_str		string
	*
	* @return	string		escaped string
	* @static
	* 
	*/
	public static function prepareDBString($a_str)
	{
		return addslashes($a_str);
	}


	/**
	* removes object from all user's desktops
	* @access	public
	* @param	integer	ref_id
	* @return	array	user_ids of all affected users
	* @static
	* 
	*/
	public static function removeItemFromDesktops($a_id)
	{
		return ilObjUser::_removeItemFromDesktops($a_id);
	}


	/**
	* extracts parameter value pairs from a string into an array
	*
	* @param	string		$a_parstr		parameter string (format: par1="value1", par2="value2", ...)
	*
	* @return	array		array of parameter value pairs
	* @static
	* 
	*/
	public static function extractParameterString($a_parstr)
	{
		// parse parameters in array
		$par = array();
		$ok=true;
		while(($spos=strpos($a_parstr,"=")) && $ok)
		{
			// extract parameter
			$cpar = substr($a_parstr,0,$spos);
			$a_parstr = substr($a_parstr,$spos,strlen($a_parstr)-$spos);
			while(substr($cpar,0,1)=="," ||substr($cpar,0,1)==" " || substr($cpar,0,1)==chr(13) || substr($cpar,0,1)==chr(10))
			$cpar = substr($cpar,1,strlen($cpar)-1);
			while(substr($cpar,strlen($cpar)-1,1)==" " || substr($cpar,strlen($cpar)-1,1)==chr(13) || substr($cpar,strlen($cpar)-1,1)==chr(10))
			$cpar = substr($cpar,0,strlen($cpar)-1);

			// parameter name should only
			$cpar_old = "";
			while($cpar != $cpar_old)
			{
				$cpar_old = $cpar;
				$cpar = eregi_replace("[^a-zA-Z0-9_]", "", $cpar);
			}

			// extract value
			if ($cpar != "")
			{
				if($spos=strpos($a_parstr,"\""))
				{
					$a_parstr = substr($a_parstr,$spos+1,strlen($a_parstr)-$spos);
					$spos=strpos($a_parstr,"\"");
					if(is_int($spos))
					{
						$cval = substr($a_parstr,0,$spos);
						$par[$cpar]=$cval;
						$a_parstr = substr($a_parstr,$spos+1,strlen($a_parstr)-$spos-1);
					}
					else
					$ok=false;
				}
				else
				$ok=false;
			}
		}

		if($ok) return $par; else return false;
	}

	public static function assembleParameterString($a_par_arr)
	{
		if (is_array($a_par_arr))
		{
			$target_arr = array();
			foreach ($a_par_arr as $par => $val)
			{
				$target_arr[] = "$par=\"$val\"";
			}
			$target_str = implode(", ", $target_arr);
		}

		return $target_str;
	}

	/**
	* dumps ord values of every character of string $a_str
	* 
	* @static
	* 
	*/
	public static function dumpString($a_str)
	{
		$ret = $a_str.": ";
		for($i=0; $i<strlen($a_str); $i++)
		{
			$ret.= ord(substr($a_str,$i,1))." ";
		}
		return $ret;
	}


	/**
	* convert "y"/"n" to true/false
	* 
	* @static
	* 
	*/
	public static function yn2tf($a_yn)
	{
		if(strtolower($a_yn) == "y")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* convert true/false to "y"/"n"
	* 
	* @static
	* 
	*/
	public static function tf2yn($a_tf)
	{
		if($a_tf)
		{
			return "y";
		}
		else
		{
			return "n";
		}
	}

	/**
	* sub-function to sort an array
	*
	* @param	array	$a
	* @param	array	$b
	*
	* @return	boolean	true on success / false on error
	* @static
	* 
	*/
	public static function sort_func ($a, $b)
	{
		global $array_sortby,$array_sortorder;

		// this comparison should give optimal results if
		// locale is provided and mb string functions are supported
		if ($array_sortorder == "asc")
		{
			return ilStr::strCmp($a[$array_sortby], $b[$array_sortby]);
		}

		if ($array_sortorder == "desc")
		{
			return !ilStr::strCmp($a[$array_sortby], $b[$array_sortby]);
			return strcoll(ilStr::strToUpper($b[$array_sortby]), ilStr::strToUpper($a[$array_sortby]));
		}
	}

	/**
	* sub-function to sort an array
	*
	* @param	array	$a
	* @param	array	$b
	*
	* @return	boolean	true on success / false on error
	* @static
	* 
	*/
	public static function sort_func_numeric ($a, $b)
	{
		global $array_sortby,$array_sortorder;

		if ($array_sortorder == "asc")
		{
			return $a["$array_sortby"] > $b["$array_sortby"];
		}

		if ($array_sortorder == "desc")
		{
			return $a["$array_sortby"] < $b["$array_sortby"];
		}
	}
	/**
	* sortArray
	*
	* @param	array	array to sort
	* @param	string	sort_column
	* @param	string	sort_order (ASC or DESC)
	* @param	bool	sort numeric?
	*
	* @return	array	sorted array
	* @static
	* 
	*/
	public static function sortArray($array,$a_array_sortby,$a_array_sortorder = 0,$a_numeric = false,
		$a_keep_keys = false)
	{
		include_once("./Services/Utilities/classes/class.ilStr.php");
		
		// BEGIN WebDAV: Provide a 'stable' sort algorithm
		if (! $a_keep_keys) {
			return self::stableSortArray($array,$a_array_sortby,$a_array_sortorder,$a_numeric,$a_keep_keys);
		}
		// END WebDAV Provide a 'stable' sort algorithm

		global $array_sortby,$array_sortorder;

		$array_sortby = $a_array_sortby;

		if ($a_array_sortorder == "desc")
		{
			$array_sortorder = "desc";
		}
		else
		{
			$array_sortorder = "asc";
		}
		if($a_numeric)
		{
			if ($a_keep_keys)
			{
				uasort($array, array("ilUtil", "sort_func_numeric"));
			}
			else
			{
				usort($array, array("ilUtil", "sort_func_numeric"));
			}
		}
		else
		{
			if ($a_keep_keys)
			{
				uasort($array, array("ilUtil", "sort_func"));
			}
			else
			{
				usort($array, array("ilUtil", "sort_func"));
			}
		}
		//usort($array,"ilUtil::sort_func");

		return $array;
	}
	// BEGIN WebDAV: Provide a 'stable' sort algorithm
	/**
	* Sort an aray using a stable sort algorithm, which preveserves the sequence
    * of array elements which have the same sort value.
    * To sort an array by multiple sort keys, invoke this function for each sort key.
	*
	* @param	array	array to sort
	* @param	string	sort_column
	* @param	string	sort_order (ASC or DESC)
	* @param	bool	sort numeric?
	*
	* @return	array	sorted array
	* @static
	* 
	*/
	public static function stableSortArray($array,$a_array_sortby,$a_array_sortorder = 0,$a_numeric = false)
	{
		global $array_sortby,$array_sortorder;

		$array_sortby = $a_array_sortby;

		if ($a_array_sortorder == "desc")
		{
			$array_sortorder = "desc";
		}
		else
		{
			$array_sortorder = "asc";
		}

		// Create a copy of the array values for sorting
		$sort_array = array_values($array);

		if($a_numeric)
		{
			ilUtil::mergesort($sort_array, array("ilUtil", "sort_func_numeric"));
		}
		else
		{
			ilUtil::mergesort($sort_array, array("ilUtil", "sort_func"));
		}

		return $sort_array;
	}
	public static function mergesort(&$array, $cmp_function = 'strcmp') {
		 // Arrays of size < 2 require no action.
		 if (count($array) < 2) return;

		 // Split the array in half
		 $halfway = count($array) / 2;
		 $array1 = array_slice($array, 0, $halfway);
		 $array2 = array_slice($array, $halfway);

		 // Recurse to sort the two halves
		 ilUtil::mergesort($array1, $cmp_function);
		 ilUtil::mergesort($array2, $cmp_function);

		 // If all of $array1 is <= all of $array2, just append them.
		 if (call_user_func($cmp_function, end($array1), $array2[0]) < 1) {
			 $array = array_merge($array1, $array2);
			 return;
		 }

		 // Merge the two sorted arrays into a single sorted array
		 $array = array();
		 $ptr1 = $ptr2 = 0;
		 while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
			 if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2]) < 1) {
				 $array[] = $array1[$ptr1++];
			 }
			 else {
				 $array[] = $array2[$ptr2++];
			 }
		 }

		 // Merge the remainder
		 while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
		 while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];

		 return;
	 }
	// END WebDAV: Provide a 'stable' sort algorithm

	/**
	* Make a multi-dimensional array to have only DISTINCT values for a certain "column".
	* It's like using the DISTINCT parameter on a SELECT sql statement.
	*
	* @param	array	your multi-dimensional array
	* @param	string	'column' to filter
	* @return	array	filtered array
	* @author	Unknown <tru@ascribedata.com> (found in PHP annotated manual)
	* @static
	* 
	*/
	public static function unique_multi_array($array, $sub_key)
	{
		$target = array();
		$existing_sub_key_values = array();

		foreach ($array as $key=>$sub_array)
		{
			if (!in_array($sub_array[$sub_key], $existing_sub_key_values))
			{
				$existing_sub_key_values[] = $sub_array[$sub_key];
				$target[$key] = $sub_array;
			}
		}

		return $target;
	}


	/**
	* returns the best supported image type by this PHP build
	*
	* @param	string	$desired_type	desired image type ("jpg" | "gif" | "png")
	*
	* @return	string					supported image type ("jpg" | "gif" | "png" | "")
	* @static
	* 
	*/
	public static function getGDSupportedImageType($a_desired_type)
	{
		$a_desired_type = strtolower($a_desired_type);
		// get supported Image Types
		$im_types = ImageTypes();

		switch($a_desired_type)
		{
			case "jpg":
			if ($im_types & IMG_JPG) return "jpg";
			if ($im_types & IMG_GIF) return "gif";
			if ($im_types & IMG_PNG) return "png";
			break;

			case "gif":
			if ($im_types & IMG_GIF) return "gif";
			if ($im_types & IMG_JPG) return "jpg";
			if ($im_types & IMG_PNG) return "png";
			break;

			case "png":
			if ($im_types & IMG_PNG) return "png";
			if ($im_types & IMG_JPG) return "jpg";
			if ($im_types & IMG_GIF) return "gif";
			break;
		}

		return "";
	}

	/**
	* checks if mime type is provided by getimagesize()
	*
	* @param	string		$a_mime		mime format
	*
	* @return	boolean		returns true if size is deducible by getimagesize()_DiffEngine
	* @static
	* 
	*/
	public static function deducibleSize($a_mime)
	{
		if (($a_mime == "image/gif") || ($a_mime == "image/jpeg") ||
		($a_mime == "image/png") || ($a_mime == "application/x-shockwave-flash") ||
		($a_mime == "image/tiff") || ($a_mime == "image/x-ms-bmp") ||
		($a_mime == "image/psd") || ($a_mime == "image/iff"))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	* http redirect to other script
	*
	* @param	string		$a_script		target script
	* @static
	* 
	*/
	public static function redirect($a_script)
	{
		global $log, $PHP_SELF;
		
//echo "<br>".$a_script;
		if (!is_int(strpos($a_script, "://")))
		{
			if (substr($a_script, 0, 1) != "/" && defined("ILIAS_HTTP_PATH"))
			{
				if (is_int(strpos($_SERVER["PHP_SELF"], "/setup/")))
				{
					$a_script = "setup/".$a_script;
				}
				$a_script = ILIAS_HTTP_PATH."/".$a_script;
			}
		}
//echo "<br>".$a_script; exit;

  		// include the user interface hook
		global $ilPluginAdmin;
		if (is_object($ilPluginAdmin))
		{
			$pl_names = $ilPluginAdmin->getActivePluginsForSlot(IL_COMP_SERVICE, "UIComponent", "uihk");
			foreach ($pl_names as $pl)
			{
				$ui_plugin = ilPluginAdmin::getPluginObject(IL_COMP_SERVICE, "UIComponent", "uihk", $pl);
				$gui_class = $ui_plugin->getUIClassInstance();
				$resp = $gui_class->getHTML("Services/Utilities", "redirect", array("html" => $a_script));
				if ($resp["mode"] != ilUIHookPluginGUI::KEEP)
				{
					$a_script = $gui_class->modifyHTML($a_script, $resp);
				}
			}
		}
		  		
		header("Location: ".$a_script);
		exit();
	}

	/**
	* inserts installation id into ILIAS id
	*
	* e.g. "il__pg_3" -> "il_43_pg_3"
	* 
	* @static
	* 
	*/
	public static function insertInstIntoID($a_value)
	{
		if (substr($a_value, 0, 4) == "il__")
		{
			$a_value = "il_".IL_INST_ID."_".substr($a_value, 4, strlen($a_value) - 4);
		}

		return $a_value;
	}

	/**
	* checks if group name already exists. Groupnames must be unique for mailing purposes
	* static function
	* @access	public
	* @param	string	groupname
	* @param	integer	obj_id of group to exclude from the check.
	* @return	boolean	true if exists
	* @static
	* 
	*/
	public static function groupNameExists($a_group_name,$a_id = 0)
	{
		global $ilDB,$ilErr;

		if (empty($a_group_name))
		{
			$message = __METHOD__.": No groupname given!";
			$ilErr->raiseError($message,$ilErr->WARNING);
		}

		$clause = ($a_id) ? " AND obj_id != ".$ilDB->quote($a_id)." " : "";

		$q = "SELECT obj_id FROM object_data ".
		"WHERE title = ".$ilDB->quote($a_group_name, "text")." ".
		"AND type = ".$ilDB->quote("grp", "text").
		$clause;

		$r = $ilDB->query($q);

		if ($r->numRows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* get current memory usage as string
	* 
	* @static
	* 
	*/
	public static function getMemString()
	{
		$my_pid = getmypid();
		return ("MEMORY USAGE (% KB PID ): ".`ps -eo%mem,rss,pid | grep $my_pid`);
	}

	/**
	* check wether the current client system is a windows system
	* 
	* @static
	* 
	*/
	public static function isWindows()
	{
		if (strtolower(substr(php_uname(), 0, 3)) == "win")
		{
			return true;
		}
		return false;
	}


	public static function escapeShellArg($a_arg)
	{
		setlocale(LC_CTYPE, "UTF8", "en_US.UTF-8"); // fix for PHP escapeshellcmd bug. See: http://bugs.php.net/bug.php?id=45132
										// see also ilias bug 5630
		return escapeshellarg($a_arg);
	}

	/**
	 * escape shell cmd
	 *
	 * @access public
	 * @param
	 * @return
	 * @static
	 * 
	 */
	public static function escapeShellCmd($a_arg)
	{
		if(ini_get('safe_mode') == 1)
		{
			return $a_arg;
		}
		setlocale(LC_CTYPE, "UTF8", "en_US.UTF-8"); // fix for PHP escapeshellcmd bug. See: http://bugs.php.net/bug.php?id=45132
		return escapeshellcmd($a_arg);
	}
	
	/**
	 * exec command and fix spaces on windows
	 *
	 * @param	string $cmd
	 * @param	string $args
	 * @return array
	 * @static
	 * 
	 */
	public static function execQuoted($cmd, $args = NULL)
	{
		global $ilLog;
		
		if(ilUtil::isWindows() && strpos($cmd, " ") !== false && substr($cmd, 0, 1) !== '"')
		{
			// cmd won't work without quotes
			$cmd = '"'.$cmd.'"';
			if($args)
			{
				// args are also quoted, workaround is to quote the whole command AGAIN
				// was fixed in php 5.2 (see php bug #25361)
				if (version_compare(phpversion(), "5.2", "<") && strpos($args, '"') !== false)
				{
					$cmd = '"'.$cmd." ".$args.'"';
				}
				// args are not quoted or php is fixed, just append
				else
				{
					$cmd .= " ".$args;
				}
			}
		}
		// nothing todo, just append args
		else if($args)
		{
			$cmd .= " ".$args;
		}
//echo "<br>".$cmd;
		exec($cmd, $arr);
//		$ilLog->write("ilUtil::execQuoted: ".$cmd.".");
		return $arr;
	}

	/**
	* Calculates a Microsoft Excel date/time value
	*
	* Calculates a Microsoft Excel date/time value (nr of days after 1900/1/1 0:00) for
	* a given date and time. The function only accepts dates after 1970/1/1, because the
	* unix timestamp functions used in the function are starting with that date.
	* If you don't enter parameters the date/time value for the actual date/time
	* will be calculated.
	*
	* static function
	*
	* @param	integer $year Year
	* @param	integer $month Month
	* @param	integer $day Day
	* @param	integer $hour Hour
	* @param	integer $minute Minute
	* @param	integer $second Second
	* @return float The Microsoft Excel date/time value
	* @access	public
	* @static
	* 
	*/
	public static function excelTime($year = "", $month = "", $day = "", $hour = "", $minute = "", $second = "")
	{
		$starting_time = mktime(0, 0, 0, 1, 2, 1970);
		if (strcmp("$year$month$day$hour$minute$second", "") == 0)
		{
			$target_time = time();
		}
		else
		{
			if ($year < 1970)
			{
				return 0;
			}
		}
		$target_time = mktime($hour, $minute, $second, $month, $day, $year);
		$difference = $target_time - $starting_time;
		$days = (($difference - ($difference % 86400)) / 86400);
		$difference = $difference - ($days * 86400) + 3600;
		return ($days + 25570 + ($difference / 86400));
	}

	/**
	* Rename uploaded executables for security reasons.
	* 
	* @static
	* 
	*/
	public static function renameExecutables($a_dir)
	{
		$def_arr = explode(",", SUFFIX_REPL_DEFAULT);
		foreach ($def_arr as $def)
		{
			ilUtil::rRenameSuffix($a_dir, trim($def), "sec");
		}

		$def_arr = explode(",", SUFFIX_REPL_ADDITIONAL);
		foreach ($def_arr as $def)
		{
			ilUtil::rRenameSuffix($a_dir, trim($def), "sec");
		}
	}

	/**
	* Renames all files with certain suffix and gives them a new suffix.
	* This words recursively through a directory.
	*
	* @param	string	$a_dir			directory
	* @param	string	$a_old_suffix	old suffix
	* @param	string	$a_new_suffix	new suffix
	*
	* @access	public
	* @static
	* 
	*/
	public static function rRenameSuffix ($a_dir, $a_old_suffix, $a_new_suffix)
	{
		if ($a_dir == "/" || $a_dir == "" || is_int(strpos($a_dir, ".."))
			|| trim($a_old_suffix) == "")
		{
			return false;
		}

		// check if argument is directory
		if (!@is_dir($a_dir))
		{
			return false;
		}

		// read a_dir
		$dir = opendir($a_dir);

		while($file = readdir($dir))
		{
			if ($file != "." and
			$file != "..")
			{
				// directories
				if (@is_dir($a_dir."/".$file))
				{
					ilUtil::rRenameSuffix($a_dir."/".$file, $a_old_suffix, $a_new_suffix);
				}

				// files
				if (@is_file($a_dir."/".$file))
				{
					// first check for files with trailing dot
					if(strrpos($file,'.') == (strlen($file) - 1))
					{
						rename($a_dir.'/'.$file,substr($a_dir.'/'.$file,0,-1));
						$file = substr($file,0,-1);
					}

					$path_info = pathinfo($a_dir."/".$file);

					if (strtolower($path_info["extension"]) ==
					strtolower($a_old_suffix))
					{
						$pos = strrpos($a_dir."/".$file, ".");
						$new_name = substr($a_dir."/".$file, 0, $pos).".".$a_new_suffix;
						rename($a_dir."/".$file, $new_name);
					}
				}
			}
		}
		return true;
	}

	public static function isAPICall () {
		return  strpos($_SERVER["SCRIPT_FILENAME"],"api") !== false ||
		strpos($_SERVER["SCRIPT_FILENAME"],"dummy") !== false;
	}

	public static function KT_replaceParam($qstring, $paramName, $paramValue) {
		if (preg_match("/&" . $paramName . "=/", $qstring)) {
			return preg_replace("/&" . $paramName . "=[^&]+/", "&" . $paramName . "=" . urlencode($paramValue), $qstring);
		} else {
			return $qstring . "&" . $paramName . "=" . urlencode($paramValue);
		}
	}

	public static function replaceUrlParameterString ($url, $parametersArray) {

		foreach ($parametersArray as $paramName => $paramValue ) {
			$url = ilUtil::KT_replaceParam($url, $paramName, $paramValue);
		}
		return $url;
	}

	/**
	* Generate a number of passwords
	* 
	* @static
	* 
	*/
	public static function generatePasswords ($a_number)
	{
		$ret = array();
		srand((double) microtime()*1000000);
		
		include_once('./Services/PrivacySecurity/classes/class.ilSecuritySettings.php');
		$security = ilSecuritySettings::_getInstance();

        for ($i=1; $i<=$a_number; $i++)
        {
        	$min = ($security->getPasswordMinLength() > 0)
        		? $security->getPasswordMinLength()
        		: 6;
        	$max = ($security->getPasswordMaxLength() > 0)
        		? $security->getPasswordMaxLength()
        		: 10;
			if ($min > $max)
			{
				$max = $max + 1;
			}
			$length  = rand($min,$max);
			$next  = rand(1,2);
			$vowels = "aeiou";
			$consonants = "bcdfghjklmnpqrstvwxyz";
			$numbers = "1234567890";
			$special = "_.+?#-*@!$%~";
			$pw = "";
			
			// position for number
			if ($security->isPasswordCharsAndNumbersEnabled())
			{
				$num_pos = rand(0, $length - 1);
			}
			
			// position for special character
			if ($security->isPasswordSpecialCharsEnabled())
			{
				$spec_pos = rand(0, $length - 1);
				if ($security->isPasswordCharsAndNumbersEnabled())
				{
					if ($num_pos == $spec_pos)	// not same position for number/special
					{
						if ($spec_pos > 0)
						{
							$spec_pos -= 1;
						}
						else
						{
							$spec_pos += 1;
						}
					}
				}
			}
			for ($j=0; $j < $length; $j++)
			{
				if ($security->isPasswordCharsAndNumbersEnabled() && $num_pos == $j)
				{
					$pw.= $numbers[rand(0,strlen($numbers)-1)];
				}
				else if ($security->isPasswordSpecialCharsEnabled() && $spec_pos == $j)
				{
					$pw.= $special[rand(0,strlen($special)-1)];
				}
				else
				{
					switch ($next)
					{
						case 1:
							$pw.= $consonants[rand(0,strlen($consonants)-1)];
							$next = 2;
							break;
						
						case 2:
							$pw.= $vowels[rand(0,strlen($vowels)-1)];
							$next = 1;
							break;
					}
				}
			}
		
			$ret[] = $pw;
		}
		return $ret;
	}

	public static function removeTrailingPathSeparators($path)
	{
		$path = preg_replace("/[\/\\\]+$/", "", $path);
		return $path;
	}

	/**
	 * convert php arrays to javascript arrays
	 *
	 * @author gigi@orsone.com
	 * @access	public
	 * @param	array
	 * @return	string
	 * @static
	 * 
	 */
	public static function array_php2js($data)
	{
		if (empty($data))
		{
			$data = array();
		}

		foreach($data as $k=>$datum)
  		{
  			if(is_null($datum)) $data[$k] = 'null';
   			if(is_string($datum)) $data[$k] = "'" . $datum . "'";
   			if(is_array($datum)) $data[$k] = array_php2js($datum);
   		}

   		return "[" . implode(', ', $data) . "]";
   	}

	/**
	* scan file for viruses and clean files if possible
	* 
	* @static
	*
	*/
	public static function virusHandling($a_file, $a_orig_name = "", $a_clean = true)
	{
		global $lng;

		if (IL_VIRUS_SCANNER != "None")
		{
			require_once("./Services/VirusScanner/classes/class.ilVirusScannerFactory.php");
			$vs = ilVirusScannerFactory::_getInstance();
			if (($vs_txt = $vs->scanFile($a_file, $a_orig_name)) != "")
			{
				if ($a_clean && (IL_VIRUS_CLEAN_COMMAND != ""))
				{
					$clean_txt = $vs->cleanFile($a_file, $a_orig_name);
					if ($vs->fileCleaned())
					{
						$vs_txt.= "<br />".$lng->txt("cleaned_file").
							"<br />".$clean_txt;
						$vs_txt.= "<br />".$lng->txt("repeat_scan");
						if (($vs2_txt = $vs->scanFile($a_file, $a_orig_name)) != "")
						{
							return array(false, nl2br($vs_txt)."<br />".$lng->txt("repeat_scan_failed").
								"<br />".nl2br($vs2_txt));
						}
						else
						{
							return array(true, nl2br($vs_txt)."<br />".$lng->txt("repeat_scan_succeded"));
						}
					}
					else
					{
						return array(false, nl2br($vs_txt)."<br />".$lng->txt("cleaning_failed"));
					}
				}
				else
				{
					return array(false, nl2br($vs_txt));
				}
			}
		}

		return array(true,"");
	}


	/**
	* move uploaded file
	* 
	* @static
	* 
	*/
	public static function moveUploadedFile($a_file, $a_name, $a_target, $a_raise_errors = true,
		$a_mode = "move_uploaded")
	{
		global $lng, $ilias;
//echo "<br>ilUtli::moveuploadedFile($a_name)";

		if (!is_file($a_file))
		{
			if ($a_raise_errors)
			{
				$ilias->raiseError($lng->txt("upload_error_file_not_found"), $ilias->error_obj->MESSAGE);
			}
			else
			{
				ilUtil::sendFailure($lng->txt("upload_error_file_not_found"), true);
			}
			return false;
		}

		// virus handling
		$vir = ilUtil::virusHandling($a_file, $a_name);
		if (!$vir[0])
		{
			unlink($a_file);
			if ($a_raise_errors)
			{
				$ilias->raiseError($lng->txt("file_is_infected")."<br />".
					$vir[1],
					$ilias->error_obj->MESSAGE);
			}
			else
			{
				ilUtil::sendFailure($lng->txt("file_is_infected")."<br />".
					$vir[1], true);
			}
			return false;
		}
		else
		{
			if ($vir[1] != "")
			{
				ilUtil::sendInfo($vir[1], true);
			}
			switch ($a_mode)
			{
				case "rename":
					return rename($a_file, $a_target);
					break;

				case "copy":
					return copy($a_file, $a_target);
					break;

				default:
					return move_uploaded_file($a_file, $a_target);
					break;
			}
		}
	}


	/**
	 *	 make time object from mysql_date_time
	 *
	 * @static
	 *
	 */
	 public static function date_mysql2time($mysql_date_time) {
	 	list($datum, $uhrzeit) = explode (" ",$mysql_date_time);
  		list($jahr, $monat, $tag) = explode("-", $datum);
  		list($std, $min, $sec) = explode(":", $uhrzeit);
  		return mktime ((int) $std, (int) $min, (int) $sec, (int) $monat, (int) $tag, (int) $jahr);
	 }
	 
	 /**
	 * Return current timestamp in Y-m-d H:i:s format
	 * 
	 * @static
	 * 
	 */
	 public static function now()
	 {
		 return date("Y-m-d H:i:s");
	 }

/**
* Convertes an array for CSV usage
*
* Processes an array as a CSV row and converts the array values to correct CSV
* values. The "converted" array is returned
*
* @author Helmut Schottmüller <hschottm@gmx.de>
* @param array $row The array containing the values for a CSV row
* @param string $quoteAll Indicates to quote every value (=TRUE) or only values containing quotes and separators (=FALSE, default)
* @param string $separator The value separator in the CSV row (used for quoting) (; = default)
* @return array The converted array ready for CSV use
* @access public
* @static
* 
*/
	public static function &processCSVRow(&$row, $quoteAll = FALSE, $separator = ";", $outUTF8 = FALSE, $compatibleWithMSExcel = TRUE)
	{
		$resultarray = array();
		foreach ($row as $rowindex => $entry)
		{
			$surround = FALSE;
			if ($quoteAll)
			{
				$surround = TRUE;
			}
			if (strpos($entry, "\"") !== FALSE)
			{
				$entry = str_replace("\"", "\"\"", $entry);
				$surround = TRUE;
			}
			if (strpos($entry, $separator) !== FALSE)
			{
				$surround = TRUE;
			}
			if ($compatibleWithMSExcel)
			{
				// replace all CR LF with LF (for Excel for Windows compatibility
				$entry = str_replace(chr(13).chr(10), chr(10), $entry);
			}
			if ($surround)
			{
				if ($outUTF8)
				{
					$resultarray[$rowindex] = "\"" . $entry . "\"";
				}
				else
				{
					$resultarray[$rowindex] = utf8_decode("\"" . $entry . "\"");
				}
			}
			else
			{
				if ($outUTF8)
				{
					$resultarray[$rowindex] = $entry;
				}
				else
				{
					$resultarray[$rowindex] = utf8_decode($entry);
				}
			}
		}
		return $resultarray;
	}

	// validates a domain name (example: www.ilias.de)
	public static function isDN($a_str)
	{
		return(preg_match("/^[a-z]+([a-z0-9-]*[a-z0-9]+)?(\.([a-z]+([a-z0-9-]*[a-z0-9]+)?)+)*$/",$a_str));
	}

	// validates an IP address (example: 192.168.1.1)
	public static function isIPv4($a_str)
	{
		return(preg_match("/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.".
						  "(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/",$a_str));
	}


	/**
	* Get all objects of a specific type and check access
	* This function is not recursive, instead it parses the serialized rbac_pa entries
	*
	* Get all objects of a specific type where access is granted for the given
	* operation. This function does a checkAccess call for all objects
	* in the object hierarchy and return only the objects of the given type.
	* Please note if access is not granted to any object in the hierarchy
	* the function skips all objects under it.
	* Example:
	* You want a list of all Courses that are visible and readable for the user.
	* The function call would be:
	* $your_list = IlUtil::getObjectsByOperation ("crs", "visible");
	* Lets say there is a course A where the user would have access to according to
	* his role assignments. Course A lies within a group object which is not readable
	* for the user. Therefore course A won't appear in the result list although
	* the queried operations 'read' would actually permit the user
	* to access course A.
	*
	* @access	public
	* @param	string/array	object type 'lm' or array('lm','sahs')
	* @param	string	permission to check e.g. 'visible' or 'read'
	* @param	int id of user in question
	* @param    int limit of results. if not given it defaults to search max hits.If limit is -1 limit is unlimited
	* @return	array of ref_ids
	* @static
	* 
	*/
	public static function _getObjectsByOperations($a_obj_type,$a_operation,$a_usr_id = 0,$limit = 0)
	{
		global $ilDB,$rbacreview,$ilAccess,$ilUser,$ilias,$tree;

		if(!is_array($a_obj_type))
		{
			$where = "WHERE type = ".$ilDB->quote($a_obj_type, "text")." ";
		}
		else
		{
			$where = "WHERE ".$ilDB->in("type", $a_obj_type, false, "text")." ";
		}

		// limit number of results default is search result limit
		if(!$limit)
		{
			$limit = $ilias->getSetting('search_max_hits',100);
		}
		if($limit == -1)
		{
			$limit = 10000;
		}

		// default to logged in usr
		$a_usr_id = $a_usr_id ? $a_usr_id : $ilUser->getId();
		$a_roles = $rbacreview->assignedRoles($a_usr_id);

		// Since no rbac_pa entries are available for the system role. This function returns !all! ref_ids in the case the user
		// is assigned to the system role
		if($rbacreview->isAssigned($a_usr_id,SYSTEM_ROLE_ID))
		{
			$query = "SELECT ref_id FROM object_reference obr LEFT JOIN object_data obd ON obr.obj_id = obd.obj_id ".
				"LEFT JOIN tree ON obr.ref_id = tree.child ".
				$where.
				"AND tree = 1";

			$res = $ilDB->query($query);
			$counter = 0;
			while($row = $ilDB->fetchObject($res))
			{
				// Filter recovery folder
				if($tree->isGrandChild(RECOVERY_FOLDER_ID, $row->ref_id))
				{
					continue;
				}

				if($counter++ >= $limit)
				{
					break;
				}

				$ref_ids[] = $row->ref_id;
			}
			return $ref_ids ? $ref_ids : array();
		} // End Administrators

		// Check ownership if it is not asked for edit_permission or a create permission
		if($a_operation == 'edit_permissions' or strpos($a_operation,'create') !== false)
		{
			$check_owner = ") ";
		}
		else
		{
			$check_owner = "OR owner = ".$ilDB->quote($a_usr_id, "integer").") ";
		}

		$ops_ids = ilRbacReview::_getOperationIdsByName(array($a_operation));
		$ops_id = $ops_ids[0];

		$and = "AND ((".$ilDB->in("rol_id", $a_roles, false, "integer")." ";

		$query = "SELECT DISTINCT(obr.ref_id),obr.obj_id,type FROM object_reference obr ".
			"JOIN object_data obd ON obd.obj_id = obr.obj_id ".
			"LEFT JOIN rbac_pa  ON obr.ref_id = rbac_pa.ref_id ".
			$where.
			$and.
			"AND (".$ilDB->like("ops_id", "text","%i:".$ops_id."%"). " ".
			"OR ".$ilDB->like("ops_id", "text", "%:\"".$ops_id."\";%").")) ".
			$check_owner;

		$res = $ilDB->query($query);
		$counter = 0;
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			if($counter >= $limit)
			{
				break;
			}
			
			// Filter objects in recovery folder
			if($tree->isGrandChild(RECOVERY_FOLDER_ID, $row->ref_id))
			{
				continue;
			}
			
			// Check deleted, hierarchical access ...
			if($ilAccess->checkAccessOfUser($a_usr_id,$a_operation,'',$row->ref_id,$row->type,$row->obj_id))
			{
				$counter++;
				$ref_ids[] = $row->ref_id;
			}
		}
		return $ref_ids ? $ref_ids : array();
	}

	/**
	* replace [text]...[/tex] tags with formula image code
	*
	* added additional parameters to make this method usable
	* for other start and end tags as well
	* 
	* @static
	* 
	*/
	public static function insertLatexImages($a_text, $a_start = "\[tex\]", $a_end = "\[\/tex\]")
	{
		global $tpl, $lng, $ilUser;

		$cgi = URL_TO_LATEX;

		// - take care of html exports (-> see buildLatexImages)
		include_once "./Services/Administration/classes/class.ilSetting.php";
		$mathJaxSetting = new ilSetting("MathJax");
		$use_mathjax = $mathJaxSetting->get("enable");
		if ($use_mathjax)
		{
			$a_text = preg_replace("/\\\\([RZN])([^a-zA-Z]|<\/span>)/", "\\mathbb{"."$1"."}"."$2", $a_text);
			$tpl->addJavaScript($mathJaxSetting->get("path_to_mathjax"));
		}
		
		// this is a fix for bug5362
		$cpos = 0;
		$o_start = $a_start;
		$o_end = $a_end;
		$a_start = str_replace("\\", "", $a_start);
		$a_end = str_replace("\\", "", $a_end);

		while (is_int($spos = stripos($a_text, $a_start, $cpos)))	// find next start
		{
			if (is_int ($epos = stripos($a_text, $a_end, $spos + 1)))
			{
				$tex = substr($a_text, $spos + strlen($a_start), $epos - $spos - strlen($a_start));

				// replace, if tags do not go across div borders
				if (!is_int(strpos($tex, "</div>")))
				{
					if (!$use_mathjax)
					{
						$a_text = substr($a_text, 0, $spos).
							"<img alt=\"".htmlentities($tex)."\" src=\"".$cgi."?".
							rawurlencode(str_replace('&amp;', '&', str_replace('&gt;', '>', str_replace('&lt;', '<', $tex))))."\" ".
							" />".
							substr($a_text, $epos + strlen($a_end));
					}
					else
					{
						$tex = $a_start.$tex.$a_end;
						
						switch ((int) $mathJaxSetting->get("limiter"))
						{
							case 1:
								$mj_start = "[tex]";
								$mj_end = "[/tex]";
								break;

							case 2:
								$mj_start = '<span class="math">';
								$mj_end = '</span>';
								break;
								
							default:
								$mj_start = "\(";
								$mj_end = "\)";
								break;
						}
						
						$replacement = 
							preg_replace('/' . $o_start . '(.*?)' . $o_end . '/ie',
							"'".$mj_start."' . preg_replace('/[\\\\\\\\\\]{2}/', '\\cr', str_replace('<', '&lt;', str_replace('<br/>', '', str_replace('<br />', '', str_replace('<br>', '', '$1'))))) . '".$mj_end."'", $tex);
						// added special handling for \\ -> \cr, < -> $lt; and removal of <br/> tags in jsMath expressions, H. Schottmüller, 2007-09-09
						$a_text = substr($a_text, 0, $spos).
							$replacement.
							substr($a_text, $epos + strlen($a_end));
					}
				}
			}
			$cpos = $spos + 1;
		}
		
		$result_text = $a_text;

		return $result_text;
	}

	/**
	* replace [text]...[/tex] tags with formula image code
	* ////////
	* added additional parameters to make this method usable
	* for other start and end tags as well
	* 
	* @static
	* 
	*/
	public static function buildLatexImages($a_text, $a_dir)
	{
		$result_text = $a_text;
		
		$start = "\[tex\]";
		$end = "\[\/tex\]";

		$cgi = URL_TO_LATEX;
		
		if ($cgi != "")
		{
			while (preg_match('/' . $start . '(.*?)' . $end . '/ie', $result_text, $found))
			{
				$cnt = (int) $GLOBALS["teximgcnt"]++;
				// get image from cgi and write it to file
				$fpr = @fopen($cgi."?".rawurlencode($found[1]), "r");
				$lcnt = 0;
				if ($fpr)
				{
					while(!feof($fpr))
					{
						$buf = fread($fpr, 1024);
						if ($lcnt == 0)
						{
							if (is_int(strpos(strtoupper(substr($buf, 0, 5)), "GIF")))
							{
								$suffix = "gif";
							}
							else
							{
								$suffix = "png";
							}
							$fpw = fopen($a_dir."/teximg/img".$cnt.".".$suffix, "w");
						}
						$lcnt++;
						fwrite($fpw, $buf);
					}
					fclose($fpw);
					fclose($fpr);
				}

				// replace tex-tag
				$img_str = "./teximg/img".$cnt.".".$suffix;
				$result_text = str_replace($found[0],
					'<img alt="'.$found[1].'" src="'.$img_str.'" />', $result_text);
			}
		}

		return $result_text;
	}

	/**
	* Prepares a string for a text area output where latex code may be in it
	* If the text is HTML-free, CHR(13) will be converted to a line break
	*
	* @param string $txt_output String which should be prepared for output
	* @access public
	* 
	*/
	public static function prepareTextareaOutput($txt_output, $prepare_for_latex_output = FALSE)
	{
		$result = $txt_output;
		$is_html = self::isHTML($result);

		if ($prepare_for_latex_output)
		{
			$result = ilUtil::insertLatexImages($result, "\<span class\=\"latex\">", "\<\/span>");
			$result = ilUtil::insertLatexImages($result, "\[tex\]", "\[\/tex\]");
		}

		// removed: did not work with magic_quotes_gpc = On
		if (!$is_html)
		{
			// if the string does not contain HTML code, replace the newlines with HTML line breaks
			$result = preg_replace("/[\n]/", "<br />", $result);
		}
		else
		{
			// patch for problems with the <pre> tags in tinyMCE
			if (preg_match_all("/(\<pre>.*?\<\/pre>)/ims", $result, $matches))
			{
				foreach ($matches[0] as $found)
				{
					$replacement = "";
					if (strpos("\n", $found) === FALSE)
					{
						$replacement = "\n";
					}
					$removed = preg_replace("/\<br\s*?\/>/ims", $replacement, $found);
					$result = str_replace($found, $removed, $result);
				}
			}
		}
		if ($prepare_for_latex_output)
		{
			// replace special characters to prevent problems with the ILIAS template system
			// eg. if someone uses {1} as an answer, nothing will be shown without the replacement
			$result = str_replace("{", "&#123;", $result);
			$result = str_replace("}", "&#125;", $result);
			$result = str_replace("\\", "&#92;", $result);
		}
		return $result;
	}

	/**
	 * Checks if a given string contains HTML or not
	 *
	 * @param string $a_text Text which should be checked
	 * @return boolean 
	 * @access public
	 * @static
	 */
	public static function isHTML($a_text)
	{
		if( preg_match("/<[^>]*?>/", $a_text) )
		{
			return true;
		}

		return false; 
	}

	/**
	* Return an array of date segments.
	*
	* @param	  int $seconds Number of seconds to be parsed
	* @return	 mixed An array containing named segments
	* @static
	* 
	*/
	public static function int2array ($seconds, $periods = null)
	{
		// Define time periods
		if (!is_array($periods))
		{
			$periods = array (
			'years'	=> 31536000,
			'months' => 2592000,
			'days'	=> 86400,
			'hours'	=> 3600,
			'minutes' => 60,
			'seconds' => 1
			);
		}

		// Loop
		$seconds = (float) $seconds;
		foreach ($periods as $period => $value)
		{
			$count = floor($seconds / $value);

			if ($count == 0)
			{
				continue;
			}

			$values[$period] = $count;
			$seconds = $seconds % $value;
		}
		// Return
		if (empty($values))
		{
			$values = null;
		}

		return $values;
	}

	/**
	* Return a string of time periods.
	*
	* @param	  mixed $duration An array of named segments
	* @return	 string
	* @static
	* 
	*/
	public static function timearray2string ($duration)
	{
		global $lng;

		if (!is_array($duration))
		{
			return false;
	 	}

		foreach ($duration as $key => $value) {

			// Plural
			if ($value > 1)
			{
				$segment_name = $key;
				$segment_name = $lng->txt($segment_name);
				$segment = $value . ' ' . $segment_name;
			}
			else
			{
				$segment_name = substr($key, 0, -1);
				$segment_name = $lng->txt($segment_name);
				$segment = $value . ' ' . $segment_name;
			}

			$array[] = $segment;
	 	}
	 	$len = count($array);

		if ($len>3)
		{
			$array=array_slice($array,0,(3-$len));
    	}

	 	$str = implode(', ', $array);

	 	return $str;
	}

	public static function getFileSizeInfo()
	{
		global $lng;

		// get the value for the maximal uploadable filesize from the php.ini (if available)
		$umf=get_cfg_var("upload_max_filesize");
		// get the value for the maximal post data from the php.ini (if available)
		$pms=get_cfg_var("post_max_size");

		// use the smaller one as limit
		$max_filesize=min($umf, $pms);
		if (!$max_filesize) $max_filesize=max($umf, $pms);

		return $lng->txt("file_notice")." $max_filesize.";
	 }

    /**
    *  extract ref id from role title, e.g. 893 from 'il_crs_member_893'
	*	@param role_title with format like il_crs_member_893
	*	@return	ref id or false
	* @static
	*
	*/

	public static function __extractRefId($role_title)
	{

		$test_str = explode('_',$role_title);

		if ($test_str[0] == 'il')
		{
			$test2 = (int) $test_str[3];
			return is_numeric ($test2) ? (int) $test2 : false;
		}
		return false;
	}

	 /**
     *  extract ref id from role title, e.g. 893 from 'il_122_role_893'
	*	@param ilias id with format like il_<instid>_<objTyp>_ID
	*   @param int inst_id  Installation ID must match inst id in param ilias_id
	*	@return	id or false
	* @static
	*
	*
	*/

	public static function __extractId($ilias_id, $inst_id)
	{

		$test_str = explode('_',$ilias_id);

		if ($test_str[0] == 'il' && $test_str[1] == $inst_id && count($test_str) == 4)
		{
			$test2 = (int) $test_str[3];
			return is_numeric ($test2) ? (int) $test2 : false;
		}
		return false;
	}

	/**
	* Function that sorts ids by a given table field using WHERE IN
	* E.g: __sort(array(6,7),'usr_data','lastname','usr_id') => sorts by lastname
	*
	* @param array Array of ids
	* @param string table name
	* @param string table field
	* @param string id name
	* @return array sorted ids
	*
	* @access protected
	* @static
	* 
	*/
	public static function _sortIds($a_ids,$a_table,$a_field,$a_id_name)
	{
		global $ilDB;

		if(!$a_ids)
		{
			return array();
		}

		// use database to sort user array
		$where = "WHERE ".$a_id_name." IN (";
		$where .= implode(",", ilUtil::quoteArray($a_ids));
		$where .= ") ";

		$query = "SELECT ".$a_id_name." FROM ".$a_table." ".
			$where.
			"ORDER BY ".$a_field;

		$res = $ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$ids[] = $row->$a_id_name;
		}
		return $ids ? $ids : array();
	}

	/**
	* Get MySQL timestamp in 4.1.x or higher format (yyyy-mm-dd hh:mm:ss)
	* This function converts a timestamp, if MySQL 4.0 is used.
	*
	* @param	string		MySQL timestamp string
	* @return	string		MySQL 4.1.x timestamp string
	* @static
	* 
	*/
	public static function getMySQLTimestamp($a_ts)
	{
		global $ilDB;

		return $a_ts;
	}

	/**
	* Quotes all members of an array for usage in DB query statement.
	* 
	* @static
	* 
	*/
	public static function quoteArray($a_array)
	{
		global $ilDB;


		if(!is_array($a_array) or !count($a_array))
		{
			return array("''");
		}

		foreach($a_array as $k => $item)
		{
			$a_array[$k] = $ilDB->quote($item);
		}

		return $a_array;
	}

	/**
	* Send Info Message to Screen.
	*
	* @param	string	message
	* @param	boolean	if true message is kept in session
	* @static
	* 
	*/
	public static function sendInfo($a_info = "",$a_keep = false)
	{
		global $tpl;
		$tpl->setMessage("info", $a_info, $a_keep);
	}

	/**
	* Send Failure Message to Screen.
	*
	* @param	string	message
	* @param	boolean	if true message is kept in session
	* @static
	* 
	*/
	public static function sendFailure($a_info = "",$a_keep = false)
	{
		global $tpl;
		$tpl->setMessage("failure", $a_info, $a_keep);
	}

	/**
	* Send Question to Screen.
	*
	* @param	string	message
	* @param	boolean	if true message is kept in session
	* @static	*/
	public static function sendQuestion($a_info = "",$a_keep = false)
	{
		global $tpl;
		$tpl->setMessage("question", $a_info, $a_keep);
	}

	/**
	* Send Success Message to Screen.
	*
	* @param	string	message
	* @param	boolean	if true message is kept in session
	* @static
	* 
	*/
	public static function sendSuccess($a_info = "",$a_keep = false)
	{
		global $tpl;
		$tpl->setMessage("success", $a_info, $a_keep);
	}

	public static function infoPanel($a_keep = true)
	{
		global $tpl,$ilias,$lng;

		if (!empty($_SESSION["infopanel"]) and is_array($_SESSION["infopanel"]))
		{
			$tpl->addBlockFile("INFOPANEL", "infopanel", "tpl.infopanel.html",
				"Services/Utilities");
			$tpl->setCurrentBlock("infopanel");

			if (!empty($_SESSION["infopanel"]["text"]))
			{
				$link = "<a href=\"".$dir.$_SESSION["infopanel"]["link"]."\" target=\"".
					ilFrameTargetInfo::_getFrame("MainContent").
					"\">";
				$link .= $lng->txt($_SESSION["infopanel"]["text"]);
				$link .= "</a>";
			}

			// deactivated
			if (!empty($_SESSION["infopanel"]["img"]))
			{
				$link .= "<td><a href=\"".$_SESSION["infopanel"]["link"]."\" target=\"".
					ilFrameTargetInfo::_getFrame("MainContent").
					"\">";
				$link .= "<img src=\"".$ilias->tplPath.$ilias->account->prefs["skin"]."/images/".
					$_SESSION["infopanel"]["img"]."\" border=\"0\" vspace=\"0\"/>";
				$link .= "</a></td>";
			}

			$tpl->setVariable("INFO_ICONS",$link);
			$tpl->parseCurrentBlock();
		}

		//if (!$a_keep)
		//{
				ilSession::clear("infopanel");
		//}
	}


	/**
	 * get size of a directory or a file.
	 *
	 * @param string path to a directory or a file
	 * @return integer. Returns -1, if the directory does not exist.
	 * @static
	 * 
	 */
	public static function dirsize($directory)
    {
		$size = 0;
		if (!is_dir($directory))
		{
			// BEGIN DiskQuota Suppress PHP warning when attempting to determine
			//       dirsize of non-existing directory
			$size = @filesize($directory);
			// END DiskQuota Suppress PHP warning.
			return ($size === false) ? -1 : $size;
		}
		if ($DIR = opendir($directory))
		{
			while (($dirfile = readdir($DIR)) !== false)
			{
				if (is_link($directory . DIRECTORY_SEPARATOR  . $dirfile) || $dirfile == '.' || $dirfile == '..')
					continue;
				if (is_file($directory .  DIRECTORY_SEPARATOR   . $dirfile))
					$size += filesize($directory . DIRECTORY_SEPARATOR   . $dirfile);
				else if (is_dir($directory . DIRECTORY_SEPARATOR   . $dirfile))
				{
					// BEGIN DiskQuota: dirsize is not a global function anymore
					$dirSize = ilUtil::dirsize($directory .  DIRECTORY_SEPARATOR   . $dirfile);
					// END DiskQuota: dirsize is not a global function anymore
					if ($dirSize >= 0)
						$size += $dirSize;
					else return -1;
				}
			}
			closedir($DIR);
		}
		return $size;
	}

	public static function randomhash()
	{
		return md5(rand(1,9999999) + str_replace(" ", "", (string) microtime()));
	}
	
	public static function setCookie($a_cookie_name,$a_cookie_value = '', $a_also_set_super_global = true, $a_set_cookie_invalid = false)
	{
		/*
		if(!(bool)$a_set_cookie_invalid) $expire = IL_COOKIE_EXPIRE;
		else $expire = time() - (365*24*60*60);
		*/
		// Temporary fix for feed.php 
		if(!(bool)$a_set_cookie_invalid) $expire = 0;
		else $expire = time() - (365*24*60*60);
		
		// setcookie() supports 5th parameter
		// only for php version 5.2.0 and above
		if( version_compare(PHP_VERSION, '5.2.0', '>=') )
		{
			// PHP version >= 5.2.0
			setcookie( $a_cookie_name, $a_cookie_value, $expire,
				IL_COOKIE_PATH, IL_COOKIE_DOMAIN, IL_COOKIE_SECURE, IL_COOKIE_HTTPONLY
			);
		}
		else
		{
			// PHP version < 5.2.0
			setcookie( $a_cookie_name, $a_cookie_value, $expire,
				IL_COOKIE_PATH, IL_COOKIE_DOMAIN, IL_COOKIE_SECURE
			);
		}
					
		if((bool)$a_also_set_super_global) $_COOKIE[$a_cookie_name] = $a_cookie_value;
	}
	
	public static function _sanitizeFilemame($a_filename)
	{
		return strip_tags(self::stripSlashes($a_filename));
	}
	
	public static function _getHttpPath()
	{
		global $ilIliasIniFile;
		
		if($_SERVER['SHELL'] || php_sapi_name() == 'cli' ||
			// fallback for windows systems, useful in crons
			(class_exists("ilContext") && !ilContext::usesHTTP()))
		{
			return $ilIliasIniFile->readVariable('server', 'http_path');
		}
		else
		{
			return ILIAS_HTTP_PATH;
		}
	}
	
	/**
	 * printBacktrace
	 *
	 * @param
	 * @return
	 * @static
	 * 
	 */
	public static function printBacktrace()
	{
		$bt = debug_backtrace();
		foreach ($bt as $t)
		{
			echo "<br>".$t["file"].", ".$t["function"]." [".$t["line"]."]";
		}
	}

	/**
	 * Parse an ilias import id
	 * Typically of type il_[IL_INST_ID]_[OBJ_TYPE]_[OBJ_ID]
	 * returns array(
	 * 'orig' => 'il_4800_rolt_123'
	 * 'prefix' => 'il'
	 * 'inst_id => '4800'
	 * 'type' => 'rolt'
	 * 'id' => '123'
	 *
	 *
	 * @param string il_id
	 *
	 */
	public static function parseImportId($a_import_id)
	{
		$exploded = explode('_'.$a_import_id);

		$parsed['orig'] = $a_import_id;
		if($exploded[0] == 'il')
		{
			$parsed['prefix'] = $exploded[0];
		}
		if(is_numeric($exploded[1]))
		{
			$parsed['inst_id'] = (int) $exploded[1];
		}
		$parsed['type'] = $exploded[2];

		if(is_numeric($exploded[3]))
		{
			$parsed['id'] = (int) $exploded[3];
		}
		return $parsed;
	}

	/**
	 * Returns the unserialized ILIAS session data.
	 *
	 * @param array $data The serialized ILIAS session data from database
	 * @return array
	 */
	public static function unserializeSession($data)
	{
		$vars = preg_split(
			'/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
			$data,
			-1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
		);

		$result = array();

		for($i = 0; $vars[$i]; $i++)
		{
			$result[$vars[$i++]] = unserialize($vars[$i]);
		}

		return $result;
	}
} // END class.ilUtil


?>

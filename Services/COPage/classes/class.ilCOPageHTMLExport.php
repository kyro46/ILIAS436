<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * HTML export class for pages
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id: $
 * @ingroup ServicesCOPage
 */
class ilCOPageHTMLExport
{
	private $mobs = array();
	private $files = array();
	private $files_direct = array();
	private $exp_dir = "";
	private $content_style_id = 0;

	/**
	 * Initialisation
	 */
	function __construct($a_exp_dir)
	{
		$this->exp_dir = $a_exp_dir;
		$this->mobs_dir = $a_exp_dir."/mobs";
		$this->files_dir = $a_exp_dir."/files";
		$this->tex_dir = $a_exp_dir."/teximg";
		$this->content_style_dir = $a_exp_dir."/content_style";
		$this->content_style_img_dir = $a_exp_dir."/content_style/images";
		
		include_once("./Services/MediaObjects/classes/class.ilPlayerUtil.php");
		$this->services_dir = $a_exp_dir."/Services";
		$this->media_service_dir = $this->services_dir."/MediaObjects";
		$this->flv_dir = $a_exp_dir."/".ilPlayerUtil::getFlashVideoPlayerDirectory();
		$this->mp3_dir = $this->media_service_dir."/flash_mp3_player";

		$this->js_dir = $a_exp_dir.'/js';
		$this->js_yahoo_dir = $a_exp_dir.'/js/yahoo';
		$this->css_dir = $a_exp_dir.'/css';

		$GLOBALS["teximgcnt"] = 0;
	}

	/**
	 * Set content style id
	 *
	 * @param int $a_val content style id	
	 */
	function setContentStyleId($a_val)
	{
		$this->content_style_id = $a_val;
	}
	
	/**
	 * Get content style id
	 *
	 * @return int content style id
	 */
	function getContentStyleId()
	{
		return $this->content_style_id;
	}
	
	/**
	 * Create directories
	 *
	 * @param
	 * @return
	 */
	function createDirectories()
	{
		ilUtil::makeDir($this->mobs_dir);
		ilUtil::makeDir($this->files_dir);
		ilUtil::makeDir($this->tex_dir);
		ilUtil::makeDir($this->content_style_dir);
		ilUtil::makeDir($this->content_style_img_dir);
		ilUtil::makeDir($this->services_dir);
		ilUtil::makeDir($this->media_service_dir);
		ilUtil::makeDir($this->flv_dir);
		ilUtil::makeDir($this->mp3_dir);
		
		ilUtil::makeDir($this->js_dir);
		ilUtil::makeDir($this->js_yahoo_dir);
		ilUtil::makeDir($this->css_dir);
	}
	
	/**
	 * Export content style
	 *
	 * @param
	 * @return
	 */
	function exportStyles()
	{
		include_once "Services/Style/classes/class.ilObjStyleSheet.php";
		
		// export content style sheet
		if ($this->getContentStyleId() < 1)
		{
			$cont_stylesheet = "./Services/COPage/css/content.css";

			$css = fread(fopen($cont_stylesheet,'r'),filesize($cont_stylesheet));
			preg_match_all("/url\(([^\)]*)\)/",$css,$files);
			foreach (array_unique($files[1]) as $fileref)
			{
				if (is_file(str_replace("..", ".", $fileref)))
				{
					copy(str_replace("..", ".", $fileref), $this->content_style_img_dir."/".basename($fileref));
				}
				$css = str_replace($fileref, "images/".basename($fileref),$css);
			}
			fwrite(fopen($this->content_style_dir."/content.css",'w'),$css);
		}
		else
		{			
			$style = new ilObjStyleSheet($this->getContentStyleId());
			$style->writeCSSFile($this->content_style_dir."/content.css", "images");
			$style->copyImagesToDir($this->content_style_img_dir);
		}
		
		// export syntax highlighting style
		$syn_stylesheet = ilObjStyleSheet::getSyntaxStylePath();
		copy($syn_stylesheet, $this->exp_dir."/syntaxhighlight.css");
	}
	
	/**
	 * Export support scripts
	 *
	 * @param
	 * @return
	 */
	function exportSupportScripts()
	{
		// export flv/mp3 player
		//copy(ilPlayerUtil::getFlashVideoPlayerFilename(true),
		//	$this->js_dir."/".ilPlayerUtil::getFlashVideoPlayerFilename());
		//copy("./Services/MediaObjects/flash_mp3_player/mp3player.swf",
		//	$this->mp3_dir."/mp3player.swf");
		
		// basic js
		copy('./Services/JavaScript/js/Basic.js', $this->js_dir.'/Basic.js');
		
		copy('./Services/UIComponent/Overlay/js/ilOverlay.js',$this->js_dir.'/ilOverlay.js');
		
		// jquery
		include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
		copy(iljQueryUtil::getLocaljQueryPath(), $this->js_dir.'/jquery.js');
		copy(iljQueryUtil::getLocaljQueryUIPath(), $this->js_dir.'/jquery-ui-min.js');
		copy(iljQueryUtil::getLocalMaphilightPath(), $this->js_dir.'/maphilight.js');

		// yui stuff we use
		include_once("./Services/YUI/classes/class.ilYuiUtil.php");
		copy(ilYuiUtil::getLocalPath('yahoo/yahoo-min.js'),
			$this->js_yahoo_dir.'/yahoo-min.js');
		copy(ilYuiUtil::getLocalPath('yahoo-dom-event/yahoo-dom-event.js'),
			$this->js_yahoo_dir.'/yahoo-dom-event.js');
		copy(ilYuiUtil::getLocalPath('animation/animation-min.js'),
			$this->js_yahoo_dir.'/animation-min.js');
		copy(ilYuiUtil::getLocalPath('container/container-min.js'),
			$this->js_yahoo_dir.'/container-min.js');
		copy(ilYuiUtil::getLocalPath('container/assets/skins/sam/container.css'),
			$this->css_dir.'/container.css');
		
		// accordion
		copy('./Services/Accordion/js/accordion.js',
			$this->js_dir.'/accordion.js');
		copy('./Services/Accordion/css/accordion.css',
			$this->css_dir.'/accordion.css');
		
		// page presentation js
		copy('./Services/COPage/js/ilCOPagePres.js',
			$this->js_dir.'/ilCOPagePres.js');
		
		// tooltip
		copy('./Services/UIComponent/Tooltip/js/ilTooltip.js',
			$this->js_dir.'/ilTooltip.js');
		
		// mediaelement.js
		include_once("./Services/MediaObjects/classes/class.ilPlayerUtil.php");
		ilPlayerUtil::copyPlayerFilesToTargetDirectory($this->flv_dir);

//		copy(ilPlayerUtil::getLocalMediaElementCssPath(),
//			$this->css_dir.'/mediaelementplayer.css');
//		copy(ilPlayerUtil::getLocalMediaElementJsPath(),
//			$this->js_dir.'/mediaelement-and-player.js');
	}

	/**
	 * Get prepared main template
	 *
	 * @param
	 * @return
	 */
	function getPreparedMainTemplate($a_tpl = "")
	{
		global $ilUser;
		
		include_once("./Services/MediaObjects/classes/class.ilPlayerUtil.php");
		
		if ($a_tpl != "")
		{
			$tpl = $a_tpl;
		}
		else
		{
			// template workaround: reset of template
			$tpl = new ilTemplate("tpl.main.html", true, true);
		}
		
		// scripts needed
		$scripts = array("./js/yahoo/yahoo-min.js", "./js/yahoo/yahoo-dom-event.js",
			"./js/yahoo/animation-min.js", "./js/yahoo/container-min.js",
			"./js/Basic.js", "./js/jquery.js", "./js/jquery-ui-min.js",
			"./js/ilOverlay.js", "./js/accordion.js", "./js/ilCOPagePres.js",
			"./js/ilTooltip.js", "./js/maphilight.js");
		$scripts = array_merge($scripts, ilPlayerUtil::getJsFilePaths());

		$mathJaxSetting = new ilSetting("MathJax");
		$use_mathjax = $mathJaxSetting->get("enable");
		if ($use_mathjax)
		{
			$scripts[] = $mathJaxSetting->get("path_to_mathjax");
		}

		foreach ($scripts as $script)
		{
			$tpl->setCurrentBlock("js_file");
			$tpl->setVariable("JS_FILE", $script);
			$tpl->parseCurrentBlock();
		}

		// css files needed
		$style_name = $ilUser->prefs["style"].".css";
		$css_files = array("./css/accordion.css", "./css/container.css",
			"./content_style/content.css", "./style/".$style_name);
		$css_files = array_merge($css_files, ilPlayerUtil::getCssFilePaths());

		foreach ($css_files as $css)
		{
			$tpl->setCurrentBlock("css_file");
			$tpl->setVariable("CSS_FILE", $css);
			$tpl->parseCurrentBlock();
		}

		return $tpl;
	}
	
	/**
	 * Collect page elements (that need to be exported separately)
	 *
	 * @param string $a_pg_type page type
	 * @param int $a_pg_id page id
	 */
	function collectPageElements($a_type, $a_id)
	{
		// collect media objects
		$pg_mobs = ilObjMediaObject::_getMobsOfObject($a_type, $a_id);
		foreach($pg_mobs as $pg_mob)
		{
			$this->mobs[$pg_mob] = $pg_mob;
		}
		
		// collect all files
		include_once("./Modules/File/classes/class.ilObjFile.php");
		$files = ilObjFile::_getFilesOfObject($a_type, $a_id);
		foreach($files as $f)
		{
			$this->files[$f] = $f;
		}

		
		$skill_tree = $ws_tree = null;		
		
		$pcs = ilPageContentUsage::getUsagesOfPage($a_id, $a_type);
		foreach ($pcs as $pc)			
		{		
			// skils
			if ($pc["type"] == "skmg")
			{
				$skill_id = $pc["id"];
				
				// get user id from portfolio page
				include_once "Services/Portfolio/classes/class.ilPortfolioPage.php";
				$page = new ilPortfolioPage(0, $a_id);
				$user_id = $page->create_user;
							
				// we only need 1 instance each
				if(!$skill_tree)
				{
					include_once "Services/Skill/classes/class.ilSkillTree.php";
					$skill_tree = new ilSkillTree();
					
					include_once "Services/Skill/classes/class.ilPersonalSkill.php";
			
					include_once "Services/PersonalWorkspace/classes/class.ilWorkspaceTree.php";
					$ws_tree = new ilWorkspaceTree($user_id);
				}				
				
				// walk skill tree
				$b_skills = ilSkillTreeNode::getSkillTreeNodes($skill_id, true);
				foreach ($b_skills as $bs)
				{															
					$skill = ilSkillTreeNodeFactory::getInstance($bs["id"]);
					$level_data = $skill->getLevelData();			
					foreach ($level_data as $k => $v)
					{
						// get assigned materials from personal skill				
						$mat = ilPersonalSkill::getAssignedMaterial($user_id, $bs["tref"], $v["id"]);
						if(sizeof($mat))
						{														
							foreach($mat as $item)
							{
								$wsp_id = $item["wsp_id"];
								$obj_id = $ws_tree->lookupObjectId($wsp_id);
								
								// all possible material types for now
								switch(ilObject::_lookupType($obj_id))
								{
									case "file":
										$this->files[$obj_id] = $obj_id;
										break;

									case "tstv":
										include_once "Modules/Test/classes/class.ilObjTestVerification.php";
										$obj = new ilObjTestVerification($obj_id, false);
										$this->files_direct[$obj_id] = array($obj->getFilePath(),
											$obj->getOfflineFilename());								
										break;

									case "excv":										
										include_once "Modules/Exercise/classes/class.ilObjExerciseVerification.php";
										$obj = new ilObjExerciseVerification($obj_id, false);
										$this->files_direct[$obj_id] = array($obj->getFilePath(),
											$obj->getOfflineFilename());	
										break;														
								}
							}
						}
					}
				}
						
			}
		}

	}
	
	/**
	 * Export page elements
	 *
	 * @param
	 * @return
	 */
	function exportPageElements()
	{
		// export all media objects
		$linked_mobs = array();
		foreach ($this->mobs as $mob)
		{
			if (ilObject::_exists($mob) && ilObject::_lookupType($mob) == "mob")
			{
				$this->exportHTMLMOB($mob, $linked_mobs);
			}
		}
		$linked_mobs2 = array();				// mobs linked in link areas
		foreach ($linked_mobs as $mob)
		{
			if (ilObject::_exists($mob))
			{
				$this->exportHTMLMOB($mob, $linked_mobs2);
			}
		}

		// export all file objects
		foreach ($this->files as $file)
		{
			$this->exportHTMLFile($file);
		}
		
		// export all files (which are not objects
		foreach ($this->files_direct as $file_id => $attr)
		{			
			$this->exportHTMLFileDirect($file_id, $attr[0], $attr[1]);
		}
	}
	
	/**
	 * Export media object to html
	 */
	function exportHTMLMOB($a_mob_id, &$a_linked_mobs)
	{
		global $tpl;

		$source_dir = ilUtil::getWebspaceDir()."/mobs/mm_".$a_mob_id;
		if (@is_dir($source_dir))
		{
			ilUtil::makeDir($this->mobs_dir."/mm_".$a_mob_id);
			ilUtil::rCopy($source_dir, $this->mobs_dir."/mm_".$a_mob_id);
		}

		// fullscreen
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mob_obj = new ilObjMediaObject($a_mob_id);
		if ($mob_obj->hasFullscreenItem())
		{
			$tpl = new ilTemplate("tpl.main.html", true, true);
			$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");			
			$file = $this->exp_dir."/fullscreen_".$a_mob_id.".html";

			// open file
			if (!($fp = @fopen($file,"w+")))
			{
				die ("<b>Error</b>: Could not open \"".$file."\" for writing".
					" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
			}
			chmod($file, 0770);
			fwrite($fp, $content);
			fclose($fp);
		}
		$linked_mobs = $mob_obj->getLinkedMediaObjects();
		$a_linked_mobs = array_merge($a_linked_mobs, $linked_mobs);
	}

	/**
	 * Export file object
	 */
	function exportHTMLFile($a_file_id)
	{
		$file_dir = $this->files_dir."/file_".$a_file_id;
		ilUtil::makeDir($file_dir);
		
		include_once("./Modules/File/classes/class.ilObjFile.php");
		$file_obj = new ilObjFile($a_file_id, false);
		$source_file = $file_obj->getDirectory($file_obj->getVersion())."/".$file_obj->getFileName();
		if (!is_file($source_file))
		{
			$source_file = $file_obj->getDirectory()."/".$file_obj->getFileName();
		}
		if (is_file($source_file))
		{
			copy($source_file, $file_dir."/".$file_obj->getFileName());
		}
	}
	
	/**
	 * Export file from path
	 */
	function exportHTMLFileDirect($a_file_id, $a_source_file, $a_file_name)
	{
		$file_dir = $this->files_dir."/file_".$a_file_id;
		ilUtil::makeDir($file_dir);
								
		if (is_file($a_source_file))
		{
			copy($a_source_file, 
				$file_dir."/".ilUtil::getASCIIFilename($a_file_name));
		}
	}

}

?>
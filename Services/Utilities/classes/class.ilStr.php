<?php
/*
+-----------------------------------------------------------------------------+
| ILIAS open source                                                           |
+-----------------------------------------------------------------------------+
| Copyright (c) 1998-2005 ILIAS open source, University of Cologne            |
|                                                                             |
| This program is free software; you can redistribute it and/or               |
| modify it under the terms of the GNU General Public License                 |
| as published by the Free Software Foundation; either version 2              |
| of the License, or (at your option) any later version.                      |
|                                                                             |
| This program is distributed in the hope that it will be useful,             |
| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
| GNU General Public License for more details.                                |
|                                                                             |
| You should have received a copy of the GNU General Public License           |
| along with this program; if not, write to the Free Software                 |
| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
+-----------------------------------------------------------------------------+
*/


/**
* Multi byte sensitive string functions
*
* @author Alex Killing <alex.killing@gmx.de>
* @author Helmut Schottmüller <helmut.schottmueller@mac.com>
* @version $Id: class.ilStr.php 29301 2011-05-27 09:20:28Z smeyer $
*/
class ilStr
{
	public static function subStr($a_str, $a_start, $a_length = NULL)
	{
		if (function_exists("mb_substr"))
		{
			return mb_substr($a_str, $a_start, $a_length, "UTF-8");
		}
		else
		{
			return substr($a_str, $a_start, $a_length);
		}
	}
	
	function strPos($a_haystack, $a_needle, $a_offset = NULL)
	{
		if (function_exists("mb_strpos"))
		{
			return mb_strpos($a_haystack, $a_needle, $a_offset, "UTF-8");
		}
		else
		{
			return strpos($a_haystack, $a_needle, $a_offset);
		}		
	}

	/*function strrPos($a_haystack, $a_needle, $a_offset = NULL)
	{
		if (function_exists("mb_strrpos"))
		{
			// only for php version 5.2.0 and above
			if( version_compare(PHP_VERSION, '5.2.0', '>=') )
			{
				return mb_strrpos($a_haystack, $a_needle, $a_offset, "UTF-8");
			}
			else
			{
				@todo: We need an implementation for php versions < 5.2.0
				return mb_strrpos($a_haystack, $a_needle, "UTF-8");
			}
		}
		else
		{
			return strrpos($a_haystack, $a_needle, $a_offset);
		}		
	}*/
	
	public static function strLen($a_string)
	{
		if (function_exists("mb_strlen"))
		{
			return mb_strlen($a_string, "UTF-8");
		}
		else
		{
			return strlen($a_string);
		}		
	}

	public static function strToLower($a_string)
	{
		if (function_exists("mb_strtolower"))
		{
			return mb_strtolower($a_string, "UTF-8");
		}
		else
		{
			return strtolower($a_string);
		}		
	}

	function strToUpper($a_string)
	{
		$a_string = (string) $a_string;
		if (function_exists("mb_strtoupper"))
		{
			return mb_strtoupper($a_string, "UTF-8");
		}
		else
		{
			return strtoupper($a_string);
		}		
	}
	
	/**
	* Compare two strings
	*/
	function strCmp($a, $b)
	{
		global $ilCollator;

		if (is_object($ilCollator))
		{
			return ($ilCollator->compare(ilStr::strToUpper($a), ilStr::strToUpper($b)) > 0);
		}
		else
		{
			return (strcoll(ilStr::strToUpper($a), ilStr::strToUpper($b)) > 0);
		}
	}
	
	/**
	 * Shorten text to the given number of bytes.
	 * If the character is cutted within a character
	 * the invalid character will be shortened, too.
	 * 
	 * E.g: shortenText('€€€',4) will return '€'
	 * 
	 * @param string $a_string
	 * @param int $a_start_pos
	 * @param int $a_num_bytes
	 * @param string $a_encoding [optional]
	 * @return string
	 */
	public static function shortenText($a_string,$a_start_pos,$a_num_bytes,$a_encoding = 'UTF-8')
	{
		return mb_strcut($a_string, $a_start_pos, $a_num_bytes, $a_encoding);		
	}

	/**
	* Check whether string is utf-8
	*/
	function isUtf8($a_str)
	{
		if (function_exists("mb_detect_encoding"))
		{
			if (mb_detect_encoding($a_str, "UTF-8") == "UTF-8")
			{
				return true;
			}
		}
		else
		{
			// copied from http://www.php.net/manual/en/function.mb-detect-encoding.php
			$c=0; $b=0;
			$bits=0;
			$len=strlen($str);
			for($i=0; $i<$len; $i++){
				$c=ord($str[$i]);
				if($c > 128){
					if(($c >= 254)) return false;
					elseif($c >= 252) $bits=6;
					elseif($c >= 248) $bits=5;
					elseif($c >= 240) $bits=4;
					elseif($c >= 224) $bits=3;
					elseif($c >= 192) $bits=2;
					else return false;
					if(($i+$bits) > $len) return false;
					while($bits > 1){
						$i++;
						$b=ord($str[$i]);
						if($b < 128 || $b > 191) return false;
						$bits--;
					}
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * Get all positions of a string
	 *
	 * @param string the string to search in
	 * @param string the string to search for
	 * @return array all occurences of needle in haystack
	 */
	public static function strPosAll($a_haystack, $a_needle)
	{
		$positions = array();
		$cpos = 0;
		while(is_int($pos = strpos($a_haystack, $a_needle, $cpos)))
		{
			$positions[] = $pos;
			$cpos = $pos+1;
		}
		return $positions;
	}

	/**
	 * Replaces the first occurence of $a_old in $a_str with $a_new
	 */
	function replaceFirsOccurence($a_old, $a_new, $a_str)
	{
		if (is_int(strpos($a_str, $a_old)))
		{
			$a_str = substr_replace ($a_str, $a_new, strpos($a_str, $a_old), strlen($a_old));
		}
		return $a_str;
	}
}
?>

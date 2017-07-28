<?php
/**
* +-----------------------------------------------------------------------+
* | Copyright (C) 2017, http://beyond-language-skills.com                 |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
* | Author: Antoine de Poorter                                            |
* +-----------------------------------------------------------------------+
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-03-09
* @package
*

	Copies the already translated strings from the old .po file to the new .po file if the
	"msgid" string exists in the new .po file




2017-01-15		version 0.1
multi-language support added



*/
	error_reporting(2);
$inFileOld = "defaulttest.po";    									// the old po file
$inFileNew = "defaulttestnew.po"; 									// the new po file
$newFile = 	"newFile.po";											// the new translation table including the delta

/* load the new .po file */
	$handle = @fopen($inFileNew, "r");
	if ($handle) {
		$index = 0;
		
	    while (($buffer = fgets($handle, 4096)) !== false) {
			$type = getLineContentType($buffer); 
	        echo "type = $type and buffer = $buffer \n<br>";
			$newTranslationStrings[$index]['type'] = getLineContentType($buffer); 		// 0 = comment, empty. 1 = msgid, 2 = msgstr
			$newTranslationStrings[$index]['content'] = $buffer;
			$index = $index +1;
	    }
		
	    if (!feof($handle)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($handle);
//		print_r($newTranslationStrings);
			
	}
	
/* load the old .po file */	
	$handle = @fopen($inFileOld, "r");
	if ($handle) {
		$index = 0;
		$subIndex = 0;
		
	    while (($buffer = fgets($handle, 4096)) !== false) {
			$type = getLineContentType($buffer); 
	        echo "Type = $type and buffer = $buffer index = $index subIndex = $subIndex\n<br>";
			if ($type == 0) {
				continue;
			}
			$oldTranslationStrings[$index][$subIndex] = $buffer;
		
			if ($subIndex == 1) {
				$subIndex = 0;
				$index = $index + 1;
			}
			else {
				$subIndex = $subIndex + 1;
			}
	    }
		
	    if (!feof($handle)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($handle);
//		print_r($oldTranslationStrings);
	
	}	
	

// check if any of the "old" entires exist in the new file
	foreach ($oldTranslationStrings as $strings) {
		echo "STRING TO CHECK: old msgid = " . $strings[0] . " translated MGSTR = " . $strings[1] . "\n";
	
		foreach ($newTranslationStrings as $key => $tempString) {
			if ($tempString['type'] <> 1) {
				echo "discarting\n";
				continue;
			}
			echo "key found = $key and tempString = " . $tempString['content'] . "\n";
			if (strcasecmp($tempString['content'], $strings[0]) == 0) {
				echo "match found for " . $tempString['content'] . "\n";
				$newTranslationStrings[$key + 1]['content']  = $strings[1];
			}
		}
	}
	
	

print_r($newTranslationStrings);
	$newHandle = fopen($newFile, w);
// save resulting data to file	
	foreach ($newTranslationStrings as $string) {
		fwrite($newHandle , $string['content']);
	}
	fclose($newHandle);
	
echo "Finished";	
	
	
	
	
	
	
	

/*
*	
*	@param string	$buffer	string to be checked
* 	@return	boolean	true: 	
*	return integer:	type of string
*				0: comment of empty string
*				1: msgid
*				2: msgstr
*				
*/
function getLineContentType($buffer) {
	if (strlen(trim($buffer)) == 0) {
		return 0;
	}

	if ($buffer[0] == '"' OR $buffer[0] == '#') {
		return 0;		
	}

	$words = explode(" ", $buffer);
	if (strcasecmp($words[0], 'msgid') == 0) {
		return 1;
	}
	
	if (strcasecmp($words[0], 'msgstr') == 0) {
		return 2;
	}	
}




?>
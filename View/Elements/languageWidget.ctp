<?php
/**
 *
 *
 * language selection widget. It does not actually change the language of the webpage. This is done
 * in the function "changeDisplayLanguage" of User controller
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date
 * @package

 
 
2017-03-08		version 0.1



*/
?>

<?php
	$supportedLanguages = array("spa" => "Español",
								"eng" => "English",
					//			"fra" => "Francais",
					//			"nld" => "Nederlands",
					//			"de" => "Deutsch",
								"ita" => "Italiano"
								);
	
	$usedLanguage = $this->requestAction('users/readUsedLanguage');				// read language from Cookie
	
	$languageCount = count($supportedLanguages);	
	$tempArray = array($usedLanguage => $supportedLanguages[$usedLanguage]);
	unset ($supportedLanguages[$usedLanguage]);	
	$listSupportedLanguages = $supportedLanguages;
?>

    <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<img src="/img/flags/<?php echo $usedLanguage?>.png" class="flagvalue" id="<?php echo $usedLanguage?>"/>&nbsp;<?php echo $tempArray[$usedLanguage]?>
			<span class="caret"></span>
	</a>
    <ul style="width: 100%" class="dropdown-menu">
                                        
<?php
	foreach ($listSupportedLanguages as $key=>$language) {
?>
		<li>
			<a class="flag-language" href="/users/changeDisplayLanguage" id="<?php echo $key?>">
				<img src="/img/flags/<?php echo $key?>.png"/>
				&nbsp;<?php echo $language?>
			</a>
		</li>

<?php
	}
?>
	 </ul>
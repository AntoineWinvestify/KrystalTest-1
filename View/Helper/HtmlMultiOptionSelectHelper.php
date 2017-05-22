<?php 
/**
*	Provides a "fancy" multi-option select
*
*/
class HtmlMultiOptionSelect extends Helper {
    
    function createLink($class, $select_options, $ids_prefix, $html_options) {

        //build the mailto link
        $unencrypted_link = '<a href="mailto:'.$addr.'">'.$link_content.'</a>';
        //build this for people with js turned off
        $noscript_link = '<noscript><span style="unicode-bidi:bidi-override;direction:rtl;">'.strrev($link_content.' > '.$addr.' <').'</span></noscript>';
        //put them together and encrypt
        $encrypted_link = '<script type="text/javascript">Rot13.write(\''.str_rot13($unencrypted_link).'\');</script>'.$noscript_link;

        return $html;
    }
}
?>
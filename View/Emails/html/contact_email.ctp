<?php
/*

Available variables:
$name
$subject
$text

*/
 
?>
            <!-- 1 Column Text : BEGIN --> 
            <tr>
                <td bgcolor="#ffffff" style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                    <p><?php echo __('Dear') . " " . $name ?></p>
                    <p><?php echo __('Thank you for contacting Winvestify.') ?></p>                    
                    <p><?php echo __('We received your email with the following information') ?></p>
                    <p><strong><?php echo __('subject:') . " " . $subject ?></strong></p>
                    <p><em><?php echo $text ?></em></p>
                    <p><?php echo __('Kind Regards,<br/><strong>The Winvestify Team</strong>.') ?></p>
                </td>
            </tr>
            <!-- 1 Column Text : END -->

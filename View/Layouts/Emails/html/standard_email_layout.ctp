<!DOCTYPE html>
<html>
<head>
	<title>Winvestify</title>
	<meta name="description" content="The global crowdlending market place" />
	<meta name="keywords" content="WINVESTIFY S.L." />
	<meta name="author" content="Antoine de Poorter" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">


    <style>

        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What is does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: A work-around for iOS meddling in triggered links. */
        *[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        /* What it does: A work-around for Gmail meddling in triggered links. */
        .x-gmail-data-detectors,
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
        }

        /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img + div {
            display:none !important;
        }

        /* What it does: Prevents underlining the button text in Windows 10 */
        .button-link {
            text-decoration: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */
        /* Thanks to Eric Lepetit @ericlepetitsf) for help troubleshooting */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
                min-width: 375px !important;
            }
        }

    </style>

    <!-- Progressive Enhancements -->
    <style>

        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
            border-radius: 3px; 
            background: #87e14b; 
            text-align: center; 
            font-family: sans-serif; 
            font-size: 13px;
            line-height: 1.1; 
            text-decoration: none; 
            display: block; 
            font-weight: bold; 
            margin: 10px 20px;
            color: white;
        }
        .button-td:hover,
        .button-a:hover {
            background: #87e14b !important;
            border-color: gray !important;
            color: black !important;
        }

        /* Media Queries */
        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

        }

    </style>
</head>
<body>
<center style="width: 100%; background: #ffffff; text-align: left;">
        <!-- Email Body : BEGIN -->
        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">

            <!-- Hero Image, Flush : BEGIN -->
            <tr>
                <td>

                    <?php
                    $options = array("width" => "50",
                        "height" => "",
                        "alt" => "Winvestify Logo",
                        "border" => "0",
                        "align" => "center",
                        "style" => "z-index: 10; width: 100%; max-width: 50px; height: auto; padding: 5px;",
                        "class" => "g-img"
                    );
                    echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . '/img/emails/winvestify_logo.png', $options);
                    ?>
                </td>		
            </tr>
            <!-- Hero Image, Flush : END -->

<?php
echo $this->fetch('content');
?>
<!-- Email Body: END -->
<!-- Email Footer : BEGIN -->

        </table>
        <hr style="border-top: 1px solid gray;">
        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
            <tr>
                <td style="padding: 30px 10px;width: 100%;  font-size: 12px; font-family: sans-serif; line-height:18px; text-align: justify; color: #888888;" class="x-gmail-data-detectors">
                    <?php echo __('This message and its attached files are private and confidential and are addressed exclusively to its recipients. ') .
                        __('If you have received this message by error, you should not disclose, copy or distribute it in any way without prior written consent ') .
                        __('from Winvestify Asset Management. Please notify the sender and delete this message and any attached document ') .
                        __('that it may contain. Failure to do so may violate existing legislation'); ?>
                    <p class="center"><a class="footer" href="https://www.facebook.com/winvestify/">Facebook</a> · <a class="footer" href="https://twitter.com/Winvestify">Twitter</a> · <a class="footer" href="https://www.linkedin.com/company-beta/11000640/">LinkedIn</a></p>
                </td>
            </tr>
        </table>
        <!-- Email Footer : END -->

    </center>
</body>
</html>
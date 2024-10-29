<?php
$selectedLanguage = "";
function getSelectedLanguage()
{
    $locale = strval(get_locale());
    switch ($locale) {

        case "en_US":
            $selectedLanguage = "English";
            break;
        case "es_ES":
            $selectedLanguage = "Espanol";
            break;
        case "fr_FR":
            $selectedLanguage = "Francais";
            break;
        case 'it_IT':
            $selectedLanguage = "Italiano";
            break;
        case "pl_PL":
            $selectedLanguage = "Polski";
            break;
        case "pt_PT":
            $selectedLanguage = "Portugues";
            break;
        case "ja":
            $selectedLanguage = "日本語";
            break;
        case "zh_TW":
            $selectedLanguage = "繁體中文";
        case "zh_CN":
            $selectedLanguage = "繁體中文";
            break;
        default:
            $selectedLanguage = "English";
    }

    return $selectedLanguage;
}
function updateLanguage()
{

    $locale = strval(get_locale());
    delete_option('alfred-locker-selected-lang');

    switch ($locale) {

        case "en_US":
            $selectedLanguage = "English";
            break;
        case "es_ES":
            $selectedLanguage = "Espanol";
            break;
        case "fr_FR":
            $selectedLanguage = "Francais";
            break;
        case 'it_IT':
            $selectedLanguage = "Italiano";
            break;
        case "pl_PL":
            $selectedLanguage = "Polski";
            break;
        case "pt_PT":
            $selectedLanguage = "Portugues";
            break;
        case "ja":
            $selectedLanguage = "日本語";
            break;
        case "zh_TW":
            $selectedLanguage = "繁體中文";
        case "zh_CN":
            $selectedLanguage = "繁體中文";
            break;
        default:
            $selectedLanguage = "English";
    }


    if (!get_option('alfred-locker-selected-lang')) {
        add_option('alfred-locker-selected-lang',  $selectedLanguage);
    }
}
updateLanguage();


if (isset($_REQUEST['plugin_heading'])) {

    $selectedTitle = sanitize_text_field($_REQUEST['plugin_heading']);

    delete_option('plugin_heading');

    if (!get_option('plugin_heading')) {


        add_option('plugin_heading',  $selectedTitle);
        //var_dump( get_option('alfred-locker-shipping-name'));

    } else {

        update_option('plugin_heading', $selectedTitle);
    }
}






//include_once('wp-includes\option.php');
if (isset($_POST['alfred-plugin-username']) && isset($_POST['alfred-plugin-password'])) {

    $username =  sanitize_text_field($_POST['alfred-plugin-username']);
    $password =  sanitize_text_field($_POST['alfred-plugin-password']);

    update_option('alfred-plugin-username', $username);

    update_option('alfred-plugin-password', $password);



    $url = ALFREDWIDGET_LOGIN_URL;
    //$url = "https://alfred-uat-api.pakpobox.com/auth/v1/user";
    // $url = "https://alfredhk-api.pakpobox.com/auth/v1/user";
    $response = wp_remote_post(
        $url,
        array(
            'method' => 'POST',
            'headers' => array(
                "content-type" => "application/json"
            ),
            'body' =>
            json_encode(
                array(
                    "username" => $username,
                    "password" => $password
                )
            ),

        )
    );

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
    } else {

        $body =  json_decode($response["body"]);



        if (isset($body->errorCode)) {
            echo "<div style='color:red; text-align:center; margin:20px; font-size: 20px;'>" . $body->message . "</div>";
        }

        if (isset($body->token)) {

            $token = $body->token;

            //echo $token;
            if (!get_option('alfred-plugin-token-new')) {
                add_option('alfred-plugin-token-new', $token);
            } else {
                update_option('alfred-plugin-token-new', $token);
            }
        }

    }
}



if (
        isset($_POST['alfred-locker-shipping-rate']) 
        && isset($_POST['alfred-point-shipping-rate']) 
        // && isset($_POST['alfred-hkpost-shipping-rate'])
        // && isset($_POST['alfred-hkpost-office-rate'])
        && isset($_POST['alfred-circle-k-rate'])
        && isset($_POST['alfred-seven-eleven-rate'])
    ) {
    delete_option('alfred-locker-shipping-rate');
    delete_option('alfred-point-shipping-rate');
    // delete_option('alfred-hkpost-shipping-rate');
    // delete_option('alfred-hkpost-office-rate');
    delete_option('alfred-circle-k-rate');
    delete_option('alfred-seven-eleven-rate');

    if (!get_option('alfred-locker-shipping-rate')) {
        add_option('alfred-locker-shipping-rate', sanitize_text_field($_POST['alfred-locker-shipping-rate']));
    } else {
        update_option('alfred-locker-shipping-rate', sanitize_text_field($_POST['alfred-locker-shipping-rate']));
    }

    if (!get_option('alfred-point-shipping-rate')) {
        add_option('alfred-point-shipping-rate', sanitize_text_field($_POST['alfred-point-shipping-rate']));
    } else {
        update_option('alfred-point-shipping-rate', sanitize_text_field($_POST['alfred-point-shipping-rate']));
    }

    // if (!get_option('alfred-hkpost-shipping-rate')) {
    //     add_option('alfred-hkpost-shipping-rate', sanitize_text_field($_POST['alfred-hkpost-shipping-rate']));
    // } else {
    //     update_option('alfred-hkpost-shipping-rate', sanitize_text_field($_POST['alfred-hkpost-shipping-rate']));
    // }

    // if (!get_option('alfred-hkpost-office-rate')) {
    //     add_option('alfred-hkpost-office-rate', sanitize_text_field($_POST['alfred-hkpost-office-rate']));
    // } else {
    //     update_option('alfred-hkpost-office-rate', sanitize_text_field($_POST['alfred-hkpost-office-rate']));
    // }
    
    if (!get_option('alfred-circle-k-rate')) {
        add_option('alfred-circle-k-rate', sanitize_text_field($_POST['alfred-circle-k-rate']));
    } else {
        update_option('alfred-circle-k-rate', sanitize_text_field($_POST['alfred-circle-k-rate']));
    }

    if (!get_option('alfred-seven-eleven-rate')) {
        add_option('alfred-seven-eleven-rate', sanitize_text_field($_POST['alfred-seven-eleven-rate']));
    } else {
        update_option('alfred-seven-eleven-rate', sanitize_text_field($_POST['alfred-seven-eleven-rate']));
    }
}

if (
    isset($_POST['ALFRED_LOCKER_THRESHOLD']) 
    && isset($_POST['ALFRED_POINT_THRESHOLD']) 
    // && isset($_POST['HKPOST_LOCKER_THRESHOLD'])
    // && isset($_POST['HKPOST_OFFICE_THRESHOLD'])
    && isset($_POST['CIRCLE_K_THRESHOLD'])
    && isset($_POST['SEVEN_ELEVEN_THRESHOLD'])
){
    $allThreshold = [
        'ALFRED_LOCKER_THRESHOLD',
        'ALFRED_POINT_THRESHOLD',
        // 'HKPOST_LOCKER_THRESHOLD',
        // 'HKPOST_OFFICE_THRESHOLD',
        'CIRCLE_K_THRESHOLD',
        'SEVEN_ELEVEN_THRESHOLD'
    ];

    foreach($allThreshold as $field){
        delete_option($field);
        if (!get_option($field)) {
            add_option($field, sanitize_text_field($_POST[$field]));
        } else {
            update_option($field, sanitize_text_field($_POST[$field]));
        }
    }

}

//set shipping name

if (isset($_POST['alfred-locker-shipping-name']) && isset($_POST['selectLangOption'])) {


    $shippingName = sanitize_text_field($_POST['alfred-locker-shipping-name']);
    $shippinglang = sanitize_text_field($_POST['selectLangOption']);
    $shippingLanguageKey = 'alfred-locker-shipping-lang' . getSelectedLanguage();
    update_option('alfred-locker-shipping-lang' . $shippinglang, $shippingName);

    if (!get_option('alfred-locker-shipping-name')) {

        add_option($shippingLanguageKey, $shippingName);
        add_option('alfred-locker-shipping-name',  $shippingName);
    } else {

        update_option('alfred-locker-shipping-name', $shippingName);
    }
}

if (isset($_POST['alfred-point-shipping-name'])) {

    //why need to add this? not working without it lol?
    $shippinglang = sanitize_text_field($_POST['selectLangOption']);
    $shippingLanguageKey = 'alfred-point-shipping-lang' . getSelectedLanguage();
    update_option('alfred-point-shipping-lang' . $shippinglang,  sanitize_text_field($_POST['alfred-point-shipping-name']));
    if (!get_option('alfred-point-shipping-name')) {
        add_option($shippingLanguageKey, sanitize_text_field($_POST['alfred-point-shipping-name']));
        add_option('alfred-point-shipping-name', sanitize_text_field($_POST['alfred-point-shipping-name']));
    } else {

        update_option('alfred-point-shipping-name', sanitize_text_field($_POST['alfred-point-shipping-name']));
    }
}

// if (isset($_POST['alfred-hkpost-shipping-name'])) {

//     //why need to add this? not working without it lol?
//     $shippinglang = sanitize_text_field($_POST['selectLangOption']);
//     $shippingLanguageKey = 'alfred-hkpost-shipping-lang' . getSelectedLanguage();
//     update_option('alfred-hkpost-shipping-lang' . $shippinglang,  sanitize_text_field($_POST['alfred-hkpost-shipping-name']));
//     if (!get_option('alfred-hkpost-shipping-name')) {
//         add_option($shippingLanguageKey, sanitize_text_field($_POST['alfred-hkpost-shipping-name']));
//         add_option('alfred-hkpost-shipping-name', sanitize_text_field($_POST['alfred-hkpost-shipping-name']));
//     } else {

//         update_option('alfred-hkpost-shipping-name', sanitize_text_field($_POST['alfred-hkpost-shipping-name']));
//     }
// }
// if (isset($_POST['alfred-hkpost-office-name'])) {

//     $shippinglang = sanitize_text_field($_POST['selectLangOption']);
//     $shippingLanguageKey = 'alfred-hkpost-office-lang' . getSelectedLanguage();
//     update_option('alfred-hkpost-office-lang' . $shippinglang,  sanitize_text_field($_POST['alfred-hkpost-office-name']));
//     if (!get_option('alfred-hkpost-office-name')) {
//         add_option($shippingLanguageKey, sanitize_text_field($_POST['alfred-hkpost-office-name']));
//         add_option('alfred-hkpost-office-name', sanitize_text_field($_POST['alfred-hkpost-office-name']));
//     } else {

//         update_option('alfred-hkpost-office-name', sanitize_text_field($_POST['alfred-hkpost-office-name']));
//     }
// }
if (isset($_POST['alfred-circle-k-name'])) {

    $shippinglang = sanitize_text_field($_POST['selectLangOption']);
    $shippingLanguageKey = 'alfred-circle-k-lang' . getSelectedLanguage();
    update_option('alfred-circle-k-lang' . $shippinglang,  sanitize_text_field($_POST['alfred-circle-k-name']));
    if (!get_option('alfred-circle-k-name')) {
        add_option($shippingLanguageKey, sanitize_text_field($_POST['alfred-circle-k-name']));
        add_option('alfred-circle-k-name', sanitize_text_field($_POST['alfred-circle-k-name']));
    } else {

        update_option('alfred-circle-k-name', sanitize_text_field($_POST['alfred-circle-k-name']));
    }
}
if (isset($_POST['alfred-seven-eleven-name'])) {

    $shippinglang = sanitize_text_field($_POST['selectLangOption']);
    $shippingLanguageKey = 'alfred-seven-eleven-lang' . getSelectedLanguage();
    update_option('alfred-seven-eleven-lang' . $shippinglang,  sanitize_text_field($_POST['alfred-seven-eleven-name']));
    if (!get_option('alfred-seven-eleven-name')) {
        add_option($shippingLanguageKey, sanitize_text_field($_POST['alfred-seven-eleven-name']));
        add_option('alfred-seven-eleven-name', sanitize_text_field($_POST['alfred-seven-eleven-name']));
    } else {

        update_option('alfred-seven-eleven-name', sanitize_text_field($_POST['alfred-seven-eleven-name']));
    }
}

?>
<style>
    .body .field-group {

        display: flex !important;
        text-align: left !important;
        align-items: center !important;
        justify-content: start;

    }

    .field-group .control {
        flex-grow: 1 !important;
        border: solid 1px #444444 !important;

        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    .field-group .label {
        margin: 10px !important;
        width: 200px !important;
    }


    .wocp_wrapper2 .alert {
        box-shadow: none !important;
    }


    .wocp_wrapper2 .actions .button {
        background: black !important;
    }

    .wocp_wrapper2 .multitab-widget li a {
        background: black !important;
    }


    .wocp_wrapper2 .multitab-widget li a.multitab-widget-current {
        border: 1px solid black;
        color: #fff !important;
    }

    .wp-person a:focus .gravatar,
    a:focus,
    a:focus .media-icon img {
        box-shadow: none !important;
    }
    
    .half-rate{
        width: 350px;
        margin: 0px 10px;
    }

    .half-rate-title{
        text-align:center;
        font-size: 16px;
        font-weight: bold;
        padding-bottom: 15px;
    }

    @media only screen and (max-width: 480px) {
        .body .field-group {

            flex-direction: column !important;


        }
    }

</style>
<div class="wocp_wrapper2">
    <div align="center">
        <h1>alfred24 Click & Collect</h1>
    </div>
    <form method="post" class="woc-form" action="#">
        <div class='multitab-section' style="margin-top: 50px;">
            <ul class='multitab-widget multitab-widget-content-tabs-id'>
                <li class='multitab-tab'><a href='#multicolumn-widget-id1'><?php _e('Account', 'alfredtext'); ?></a></li>
                <li class='multitab-tab'><a href='#multicolumn-widget-id2'><?php _e('Settings', 'alfredtext'); ?></a></li>
                <li class='multitab-tab'><a href='#multicolumn-widget-id3'><?php _e('Shipping Rates', 'alfredtext'); ?></a></li>
                <li class='multitab-tab'><a href='#multicolumn-widget-id4'><?php _e('Languages', 'alfredtext'); ?></a></li>
                <li class='multitab-tab'><a href='#multicolumn-widget-id5'><?php _e('Error Shipments', 'alfredtext'); ?></a></li>
            </ul>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id1'>
                <div class="body">
                    <div class="alert" align="left">

                        <div class="alert__message">
                            <p><?php _e('Thank you for choosing alfred24.', 'alfredtext'); ?></p>
                            <p><?php _e('Enable the Click & Collect service with 2 simple steps:', 'alfredtext'); ?></p>
                            <ol>
                                <li><?php _e('Create your account on <a href="https://alfred.delivery/merchant-signup/">alfred24</a>', 'alfredtext'); ?></li>
                                <li><?php _e('Login beneath with your username and password', 'alfredtext'); ?></li>
                            </ol>
                            <p><?php _e('You can check the order status <a href="https://alfredhk.pakpobox.com/#/page/login">here</a>', 'alfredtext'); ?></p>
                        </div>

                    </div>
                    <div class="field-group">
                        <div class="label"><?php _e('Country', 'alfredtext'); ?></div>
                        <div class="select control">
                            <select>
                                <option><?php _e('Hong Kong', 'alfredtext'); ?></option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label"><?php _e('Username', 'alfredtext'); ?></div>
                        <?php $username = get_option('alfred-plugin-username'); ?>
                        <input type="text" class="control" value="<?php echo $username; ?>" id="alfred-plugin-username" name="alfred-plugin-username" autocomplete="chrome-off" />
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label"><?php _e('Password', 'alfredtext'); ?></div>
                        <?php $userpassword = get_option('alfred-plugin-password'); ?>
                        <input type="password" class="control" value="<?php echo $userpassword; ?>" id="alfred-plugin-password" name="alfred-plugin-password" />
                    </div>
                    <?php

                    if (get_option('alfred-plugin-token-new')) {
                    ?>
                        <div class="field-group">
                            <div class="label"><?php _e('Token', 'alfredtext'); ?></div>
                            <input type="text" disabled="disabled" class="control" value=<?php echo get_option('alfred-plugin-token-new') ?> id="alfred-plugin-token" name="alfred-plugin-token" />
                        </div>
                    <?php

                    }

                    ?>
                </div>
            </div>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id2'>
                <div class="body">
                    <div class="field-group" align="right" style="visibility: hidden; display:none;">
                        <div class="label"><?php _e('Status', 'alfredtext'); ?></div>
                        <div class="select control">
                            <select>
                                <option><?php _e('Disabled', 'alfredtext'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <div class="label"><?php _e('Heading', 'alfredtext'); ?></div>
                        <?php
                        if (get_option('plugin_heading')) {
                            $plugin_heading = get_option('plugin_heading');
                        } else {
                            $plugin_heading = "alfred24 Click & Collect";
                        }
                        ?>
                        <input type="text" name="plugin_heading" class="control" placeholder="" value="<?php echo $plugin_heading; ?>" />
                    </div>
                    <hr />

                    <div class="field-group" align="right">
                        <div class="label"><?php _e('Default Tax Class', 'alfredtext'); ?></div>
                        <div class="select control">
                            <select>
                                <option><?php _e('(Highest Product Tax Rate)', 'alfredtext'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id3'>
                <div class="body">
                    <div class="field-group" >
                        <div class="label"> </div>
                        <div class="half-rate half-rate-title">Rate</div>
                        <div class="half-rate half-rate-title">Threshold</div>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label">alfred24 Locker</div>
                        <div class="half-rate">
                            <input name="alfred-locker-shipping-rate" step="1" min="0" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            if (get_option('alfred-locker-shipping-rate')) {
                                                                                                                                echo get_option('alfred-locker-shipping-rate');
                                                                                                                            } else {
                                                                                                                                echo '""';
                                                                                                                            }
                                                                                                                            ?> />
                        </div>
                        <div class="half-rate">
                            <input name="ALFRED_LOCKER_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            if (get_option('ALFRED_LOCKER_THRESHOLD')) {
                                                                                                                                echo get_option('ALFRED_LOCKER_THRESHOLD');
                                                                                                                            }else{
                                                                                                                                echo '""';
                                                                                                                            }
                                                                                                                            ?> />
                        </div>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label">alfred24 Point</div>
                        <div class="half-rate">
                        <input name='alfred-point-shipping-rate' step="1" min="0" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                    if (get_option('alfred-point-shipping-rate')) {
                                                                                                                        echo get_option('alfred-point-shipping-rate');
                                                                                                                    } else {
                                                                                                                        echo '""';
                                                                                                                    }

                                                                                                                    ?> />
                        </div>
                        <div class="half-rate">
                            <input name="ALFRED_POINT_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            if (get_option('ALFRED_POINT_THRESHOLD')) {
                                                                                                                                echo get_option('ALFRED_POINT_THRESHOLD');
                                                                                                                            }else{
                                                                                                                                echo '""';
                                                                                                                            }
                                                                                                                            ?> />
                        </div>
                    </div>
                    <!-- <hr />
                    <div class="field-group">
                        <div class="label">Hongkong Post iPostal Station</div>
                        <div class="half-rate">
                        <input name='alfred-hkpost-shipping-rate' step="1" min="0" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                    // if (get_option('alfred-hkpost-shipping-rate')) {
                                                                                                                    //     echo get_option('alfred-hkpost-shipping-rate');
                                                                                                                    // } else {
                                                                                                                    //     echo '""';
                                                                                                                    // }

                                                                                                                    ?> />
                        </div>
                        <div class="half-rate">
                            <input name="HKPOST_LOCKER_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            // if (get_option('HKPOST_LOCKER_THRESHOLD')) {
                                                                                                                            //     echo get_option('HKPOST_LOCKER_THRESHOLD');
                                                                                                                            // }else{
                                                                                                                            //     echo '""';
                                                                                                                            // }
                                                                                                                            ?> />
                        </div>
                    </div> -->
                    <hr />
                    <!-- <div class="field-group">
                        <div class="label">Hongkong Post Office</div>
                        <div class="half-rate">
                        <input name='alfred-hkpost-office-rate' type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                    // if (get_option('alfred-hkpost-office-rate')) {
                                                                                                                    //     echo get_option('alfred-hkpost-office-rate');
                                                                                                                    // } else {
                                                                                                                    //     echo '""';
                                                                                                                    // }

                                                                                                                    ?> />
                        </div>
                        <div class="half-rate">
                            <input name="HKPOST_OFFICE_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            // if (get_option('HKPOST_OFFICE_THRESHOLD')) {
                                                                                                                            //     echo get_option('HKPOST_OFFICE_THRESHOLD');
                                                                                                                            // }else{
                                                                                                                            //     echo '""';
                                                                                                                            // }
                                                                                                                            ?> />
                        </div>
                    </div> -->
                    <!-- <hr /> -->
                    <div class="field-group">
                        <div class="label">Collect at Circle K</div>
                        <div class="half-rate">
                        <input name='alfred-circle-k-rate' step="1" min="0" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                    if (get_option('alfred-circle-k-rate')) {
                                                                                                                        echo get_option('alfred-circle-k-rate');
                                                                                                                    } else {
                                                                                                                        echo '""';
                                                                                                                    }

                                                                                                                    ?> />
                        </div>
                        <div class="half-rate">
                            <input name="CIRCLE_K_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            if (get_option('CIRCLE_K_THRESHOLD')) {
                                                                                                                                echo get_option('CIRCLE_K_THRESHOLD');
                                                                                                                            }else{
                                                                                                                                echo '""';
                                                                                                                            }
                                                                                                                            ?> />
                        </div>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label">Collect at 7-Eleven</div>
                        <div class="half-rate">
                        <input name='alfred-seven-eleven-rate' step="1" min="0" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                    if (get_option('alfred-seven-eleven-rate')) {
                                                                                                                        echo get_option('alfred-seven-eleven-rate');
                                                                                                                    } else {
                                                                                                                        echo '""';
                                                                                                                    }

                                                                                                                    ?> />
                                                                                                                    </div>
                        <div class="half-rate">
                            <input name="SEVEN_ELEVEN_THRESHOLD" type='number' class="control" placeholder="" step="0.01" value=<?php
                                                                                                                            if (get_option('SEVEN_ELEVEN_THRESHOLD')) {
                                                                                                                                echo get_option('SEVEN_ELEVEN_THRESHOLD');
                                                                                                                            }else{
                                                                                                                                echo '""';
                                                                                                                            }
                                                                                                                            ?> />
                        </div>
                    </div>
                </div>
            </div>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id4'>
                <div class="body">
                    <div class="field-group" align="left">
                        <div class="select control">
                            <select id="lang_select" name="selectLangOption" onchange="onChangeSelect()">
                                <option <?php
                                        if (get_option('alfred-locker-selected-lang') == "English") {

                                            echo "selected";
                                        }
                                        ?> value="English"><?php _e('English', 'alfredtext'); ?></option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "Espanol")
                                            echo "selected";

                                        ?> value="Espanol">Espanol</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "Francais")
                                            echo "selected";

                                        ?> value="Francais">Francais</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "Italiano")
                                            echo "selected";

                                        ?> value="Italiano">Italiano</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "Polski")
                                            echo "selected";

                                        ?> value="Polski">Polski</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "Portugues")
                                            echo "selected";

                                        ?> value="Portugues">Portuguese</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "日本語")
                                            echo "selected";

                                        ?> value="日本語">日本語</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "简体中文")
                                            echo "selected";

                                        ?> value="简体中文">简体中文</option>
                                <option <?php if (get_option('alfred-locker-selected-lang') == "繁體中文")
                                            echo "selected";

                                        ?> value="繁體中文"> 繁體中文</option>

                            </select>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", ready);

                        function ready() {
                            onChangeSelect()
                        }

                        var selectedLang

                        function onChangeSelect() {
                            selectedLang = document.getElementById("lang_select").value;

                            console.log("selected lang", selectedLang)

                            updateAlfLockerVal()
                            updateAlfPointVal()
                            // updateAlfHkpostVal()
                            // updateAlfHkpostOfficeVal()
                            updateAlfCircleKVal()
                            updateAlfSevenElevenVal()
                        }
                    </script>



                    <hr />

                    <div class="field-group">
                        <div class="label"><?php _e('alfred24 Locker', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-locker-shipping-name" id="alfred_locker_shipping" class="control" placeholder="Enter Label For alfred24 Locker" value="<?php
                                                                                                                                                                                $shippingLanguageKey = 'alfred-locker-shipping-lang' . getSelectedLanguage();
                                                                                                                                                                                if (get_option($shippingLanguageKey)) {
                                                                                                                                                                                    echo  htmlspecialchars(get_option($shippingLanguageKey));
                                                                                                                                                                                }
                                                                                                                                                                                ?>" />
                        <script>
                            console.log("1 ", selectedLang)

                            function updateAlfLockerVal() {

                                var alfredLockerLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-locker-shipping-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at alfred24 locker"
                                        <?php  } ?>
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-locker-shipping-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Recoger en el armario de alfred24"
                                        <?php  } ?>
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-locker-shipping-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Retirer à la consigne alfred24"
                                        <?php  } ?>
                                        break;

                                    case "Italiano":
                                        // code block 
                                        <?php $shippingLanguageITKey = 'alfred-locker-shipping-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Ritira da alfred24 locker"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-locker-shipping-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Odbiór w punkcie alfred24"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-locker-shipping-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Retira no CliqueRetire E-box"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-locker-shipping-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "アルフレッドロッカーで収集"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-locker-shipping-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "alfred24智能柜取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-locker-shipping-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "alfred24智能櫃取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredLockerLang = "Collect at alfred24 locker"
                                        // code block
                                }
                                document.getElementById("alfred_locker_shipping").value = alfredLockerLang;
                            }
                        </script>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label"><?php _e('alfred24 Point', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-point-shipping-name" class="control" id="alfred_point_shipping" placeholder="Enter Label For alfred24 Point" value="<?php

                                                                                                                                                                            $shippingLanguageKey = 'alfred-point-shipping-lang' . getSelectedLanguage();
                                                                                                                                                                            if (get_option($shippingLanguageKey)) {
                                                                                                                                                                                echo  htmlspecialchars(get_option($shippingLanguageKey));
                                                                                                                                                                            } else {
                                                                                                                                                                                echo "";
                                                                                                                                                                            }

                                                                                                                                                                            ?>" />

                        <script>
                            console.log("2", selectedLang)
                            
                            function updateAlfPointVal() {
                                var alfredLockerLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-point-shipping-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at alfred24 Point"
                                        <?php  } ?>
                                        console.log("401 break called")
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-point-shipping-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Recoge en el punto alfred24"
                                        <?php  } ?>
                                        console.log("40 break called")
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-point-shipping-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Retirer au point alfred24"
                                        <?php  } ?>
                                        break;
                                        console.log("402 break called")
                                    case "Italiano":
                                        // code block
                                        <?php $shippingLanguageITKey = 'alfred-point-shipping-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Ritira da alfred24 point"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-point-shipping-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Odbiór w punkcie alfred24"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-point-shipping-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Retira no CliqueRetire Point"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-point-shipping-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "アルフレッドポイントで収集"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-point-shipping-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "alfred24自提点取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-point-shipping-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "alfred24自提點取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredLockerLang = "Collect at alfred24 point"
                                        // code block
                                }

                                console.log("22", selectedLang)
                                console.log("alfredLockerLang", alfredLockerLang)
                                document.getElementById("alfred_point_shipping").value = alfredLockerLang;

                            }
                            
                        </script>
                    </div>
                    <!-- <hr /> -->
                    <div class="field-group" style="display:none !important;">
                        <div class="label"><?php _e('Hongkong Post iPostal Station', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-hkpost-shipping-name" class="control" id="alfred_hkpost_shipping" placeholder="Enter Label For alfred24 HK Post" value="<?php
                                                                                                                                                                            $shippingLanguageKey = 'alfred-hkpost-shipping-lang' . getSelectedLanguage();
                                                                                                                                                                            if (get_option($shippingLanguageKey)) {
                                                                                                                                                                                echo  htmlspecialchars(get_option($shippingLanguageKey));
                                                                                                                                                                            } else {
                                                                                                                                                                                echo "";
                                                                                                                                                                            }
                                                                                                                                                                            ?>" />
                        <script>
                            console.log("3", selectedLang)

                            function updateAlfHkpostVal() {
                                var alfredHkpostLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-hkpost-shipping-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        console.log("401 break called")
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-hkpost-shipping-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        console.log("40 break called")
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-hkpost-shipping-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        break;
                                        console.log("402 break called")
                                    case "Italiano":
                                        // code block
                                        <?php $shippingLanguageITKey = 'alfred-hkpost-shipping-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-hkpost-shipping-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-hkpost-shipping-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-hkpost-shipping-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-hkpost-shipping-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "香港郵政智郵站取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-hkpost-shipping-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "香港郵政智郵站取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        // code block
                                }

                                console.log("22", selectedLang)
                                console.log("alfredHkpostLang", alfredHkpostLang)
                                document.getElementById("alfred_hkpost_shipping").value = alfredHkpostLang;
                            }
                        </script>
                    </div>
                    <!-- <hr /> -->
                    <div class="field-group" style="display:none !important;">
                        <div class="label"><?php _e('Hongkong Post Office', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-hkpost-office-name" class="control" id="alfred_hkpost_office" placeholder="Enter Label For alfred24 HK Post Office" value="<?php
                                                                                                                                                                            $shippingLanguageKey = 'alfred-hkpost-office-lang' . getSelectedLanguage();
                                                                                                                                                                            if (get_option($shippingLanguageKey)) {
                                                                                                                                                                                echo  htmlspecialchars(get_option($shippingLanguageKey));
                                                                                                                                                                            } else {
                                                                                                                                                                                echo "";
                                                                                                                                                                            }
                                                                                                                                                                            ?>" />
                        <script>
                            console.log("3", selectedLang)

                            function updateAlfHkpostOfficeVal() {
                                var alfredHkpostLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-hkpost-office-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        console.log("401 break called")
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-hkpost-office-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        console.log("40 break called")
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-hkpost-office-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        break;
                                        console.log("402 break called")
                                    case "Italiano":
                                        // code block
                                        <?php $shippingLanguageITKey = 'alfred-hkpost-office-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-hkpost-office-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-hkpost-office-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-hkpost-office-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "Collect at Hongkong Post Office"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-hkpost-office-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "香港邮政局取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-hkpost-office-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredHkpostLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredHkpostLang = "香港郵局取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredHkpostLang = "Collect at Hongkong Post iPostal Station"
                                        // code block
                                }

                                console.log("22", selectedLang)
                                console.log("alfredHkpostLang", alfredHkpostLang)
                                document.getElementById("alfred_hkpost_office").value = alfredHkpostLang;
                            }
                        </script>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label"><?php _e('Circle K', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-circle-k-name" class="control" id="alfred_circle_k_shipping" placeholder="Enter Label For Circle K" value="<?php

                        $shippingLanguageKey = 'alfred-circle-k-lang' . getSelectedLanguage();
                        if (get_option($shippingLanguageKey)) {
                            echo  htmlspecialchars(get_option($shippingLanguageKey));
                        } else {
                            echo "";
                        }

                        ?>" />

                        <script>
                            console.log("2", selectedLang)
                            
                            function updateAlfCircleKVal() {
                                var alfredLockerLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-circle-k-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        console.log("401 break called")
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-circle-k-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        console.log("40 break called")
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-circle-k-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        break;
                                        console.log("402 break called")
                                    case "Italiano":
                                        // code block
                                        <?php $shippingLanguageITKey = 'alfred-circle-k-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-circle-k-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-circle-k-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at Circle K"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-circle-k-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "OKコンビニで収集"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-circle-k-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "OK便利店取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-circle-k-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "OK便利店取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredLockerLang = "Collect at Circle K"
                                        // code block
                                }

                                console.log("22", selectedLang)
                                console.log("alfredLockerLang", alfredLockerLang)
                                document.getElementById("alfred_circle_k_shipping").value = alfredLockerLang;

                            }
                            
                        </script>
                    </div>
                    <hr />
                    <div class="field-group">
                        <div class="label"><?php _e('7-Eleven', 'alfredtext'); ?></div>
                        <input type="text" name="alfred-seven-eleven-name" class="control" id="alfred_seven_eleven_shipping" placeholder="Enter Label For 7-Eleven" value="<?php

                        $shippingLanguageKey = 'alfred-seven-eleven-lang' . getSelectedLanguage();
                        if (get_option($shippingLanguageKey)) {
                            echo  htmlspecialchars(get_option($shippingLanguageKey));
                        } else {
                            echo "";
                        }

                        ?>" />

                        <script>
                            console.log("2", selectedLang)
                            
                            function updateAlfSevenElevenVal() {
                                var alfredLockerLang = ""
                                switch (selectedLang) {
                                    case "English":
                                        // code block
                                        <?php $shippingLanguageENKey = 'alfred-seven-eleven-langEnglish';
                                        if (get_option($shippingLanguageENKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageENKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        console.log("401 break called")
                                        break;
                                    case "Espanol":
                                        // code block
                                        <?php $shippingLanguageESKey = 'alfred-seven-eleven-langEspanol';
                                        if (get_option($shippingLanguageESKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageESKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        console.log("40 break called")
                                        break;
                                    case "Francais":
                                        // code block
                                        <?php $shippingLanguageFRKey = 'alfred-seven-eleven-langFrancais';
                                        if (get_option($shippingLanguageFRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageFRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        break;
                                        console.log("402 break called")
                                    case "Italiano":
                                        // code block
                                        <?php $shippingLanguageITKey = 'alfred-seven-eleven-langItaliano';
                                        if (get_option($shippingLanguageITKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageITKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        break;
                                    case "Polski":
                                        // code block
                                        <?php $shippingLanguagePLKey = 'alfred-seven-eleven-langPolski';
                                        if (get_option($shippingLanguagePLKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePLKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        break;
                                    case "Portugues":
                                        // code block
                                        <?php $shippingLanguagePRKey = 'alfred-seven-eleven-langPortugues';
                                        if (get_option($shippingLanguagePRKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguagePRKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "Collect at 7-Eleven"
                                        <?php  } ?>
                                        break;
                                    case "日本語":
                                        // code block
                                        <?php $shippingLanguageCHZKey = 'alfred-seven-eleven-lang日本語';
                                        if (get_option($shippingLanguageCHZKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHZKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "セブン‐イレブンで収集"
                                        <?php  } ?>
                                        break;
                                    case "简体中文":
                                        // code block
                                        <?php $shippingLanguageCHSMKey = 'alfred-seven-eleven-lang简体中文';
                                        if (get_option($shippingLanguageCHSMKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHSMKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "7-Eleven取件"
                                        <?php  } ?>
                                        break;
                                    case "繁體中文":
                                        // code block
                                        <?php $shippingLanguageCHKey = 'alfred-seven-eleven-lang繁體中文';
                                        if (get_option($shippingLanguageCHKey)) { ?>
                                            alfredLockerLang = "<?php echo htmlspecialchars(get_option($shippingLanguageCHKey)); ?>"
                                        <?php  } else { ?>
                                            alfredLockerLang = "7-Eleven取件"
                                        <?php  } ?>
                                        break;

                                    default:
                                        alfredLockerLang = "Collect at 7-Eleven"
                                        // code block
                                }

                                console.log("22", selectedLang)
                                console.log("alfredLockerLang", alfredLockerLang)
                                document.getElementById("alfred_seven_eleven_shipping").value = alfredLockerLang;

                            }
                            
                        </script>
                    </div>
                </div>
            </div>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id5'>
                <div class="body">

                </div>
            </div>
            <div class='multitab-widget-content multitab-widget-content-widget-id' id='multicolumn-widget-id6'>
                <div class="body">

                </div>
            </div>
        </div>
        <div class="actions" align="right">
            <button class="button"><?php _e('Cancel', 'alfredtext'); ?></button>
            <input type="submit" class="button button--success">
        </div>
    </form>
</div>
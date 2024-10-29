<?php
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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["shippingAddress1"])) {

    //set_billing_state
    WC()->customer->set_billing_address_1(wc_clean($_POST["shippingAddress1"]));
    WC()->customer->set_billing_address_2(wc_clean($_POST["shippingAddress2"]));
    WC()->customer->set_billing_city(wc_clean($_POST["region"]));
    WC()->customer->set_billing_state(wc_clean($_POST["region"]));

    //locationId

    $shippingAddress = wc_clean($_POST["locationType"]) . " " . wc_clean($_POST["shippingAddress1"]) . " " . wc_clean($_POST["shippingAddress2"]);

    WC()->customer->set_shipping_address(wc_clean($_POST["shippingAddress1"]));
    WC()->customer->set_shipping_address_2(wc_clean($_POST["shippingAddress2"]));
    WC()->customer->set_shipping_city(wc_clean($_POST["district"]));

    WC()->customer->set_shipping_state(wc_clean($_POST["region"]));

    if (!get_option('alfred-selected-locationtype')) {
        add_option('alfred-selected-locationtype', wc_clean($_POST["locationType"]));
    } else {
        update_option('alfred-selected-locationtype', wc_clean($_POST["locationType"]));
    }

    if (!get_option('alfred-selected-locationID')) {
        add_option('alfred-selected-locationID', wc_clean($_POST['locationId']));
    } else {
        update_option('alfred-selected-locationID', wc_clean($_POST['locationId']));
    }

    WC()->customer->save();

    
    die();
}
?>
<style>
.outlineAlfred {
    margin: 5px;
    padding: 15px;
    background: #eeeeee;
    display: flex;
    flex-direction: column;
}

#firstRadio {

    text-align: left;
    padding-top: 10px;
    width: 100%;
}

#secondRadio, #thirdRadio {
    text-align: left;
    padding-top: 10px;
    padding-bottom: 10px;
    width: 100%;

}

@media only screen and (max-width: 767px) and (min-width: 442px) {

    .outlineAlfred {
        flex-direction: row;

    }
}
</style>

<div class="outlineAlfred">
<div id="firstRadio">
    <input type="radio" id="alfred_locker_collect" name="alfred_locker_collect" value="alfred_locker_collect" onclick="on_change('alfred_locker')" <?php if(get_option('alfred-selected-locationtype') === 'ALFRED_LOCKER'){echo "checked";}?>>
    <label for="alfred_locker_collect">
        <?php
        $shippingLanguageKey = 'alfred-locker-shipping-lang' . getSelectedLanguage();
        if (get_option($shippingLanguageKey)) {
            echo  htmlspecialchars(get_option($shippingLanguageKey));
        } else {
            echo "Collect At alfred24 Locker";
        }
        ?>
    </label>
    </input>
</div>

<div id="secondRadio">
    <input type="radio" id="alfred_locker_point" name="alfred_locker_point" value="alfred_locker_point" onclick="on_change('alfred_point')" <?php if(get_option('alfred-selected-locationtype') === 'ALFRED_POINT'){echo "checked";}?>>
    <label for="alfred_locker_point">
        <?php

        $shippingLanguageKey = 'alfred-point-shipping-lang' . getSelectedLanguage();
        if (get_option($shippingLanguageKey)) {
            echo  htmlspecialchars(get_option($shippingLanguageKey));
        } else {
            echo "Collect At alfred24 Point";
        }
        ?>
    </label>
    </input>
</div>

<!-- <div id="thirdRadio">
    <input type="radio" id="alfred_locker_hkpost" name="alfred_locker_hkpost" value="alfred_locker_hkpost" onclick="on_change('hkpost_locker')" <?php //if(get_option('alfred-selected-locationtype') === 'HKPOST_LOCKER'){echo "checked";}?>>
    <label for="alfred_locker_hkpost" style="width:86%">
        <?php

        // $shippingLanguageKey = 'alfred-hkpost-shipping-lang' . getSelectedLanguage();
        // if (get_option($shippingLanguageKey)) {
        //     echo  htmlspecialchars(get_option($shippingLanguageKey));
        // } else {
        //     echo "Collect at Hongkong Post iPostal Station";
        // }
        ?>
    </label>
    </input>
</div> -->
<!-- <div id="fourthRadio">
    <input type="radio" id="alfred_locker_hkpost_office" name="alfred_locker_hkpost_office" value="alfred_locker_hkpost_office" onclick="on_change('hkpost_office')" <?php //if(get_option('alfred-selected-locationtype') === 'HKPOST_OFFICE'){echo "checked";}?>>
    <label for="alfred_locker_hkpost_office" style="width:86%">
        <?php
/* 
        $shippingLanguageKey = 'alfred-hkpost-office-lang' . getSelectedLanguage();
        if (get_option($shippingLanguageKey)) {
            echo  htmlspecialchars(get_option($shippingLanguageKey));
        } else {
            echo "Collect at Hongkong Post Office";
        }
        */
        ?>
    </label>
    </input>
</div> -->
<div id="fifthRadio">
    <input type="radio" id="alfred_circle_k" name="alfred_circle_k" value="alfred_circle_k" onclick="on_change('circle_k')" <?php if(get_option('alfred-selected-locationtype') === 'CIRCLE_K'){echo "checked";}?>>
    <label for="alfred_circle_k" style="width:86%">
        <?php

        $shippingLanguageKey = 'alfred-circle-k-lang' . getSelectedLanguage();
        if (get_option($shippingLanguageKey)) {
            echo  htmlspecialchars(get_option($shippingLanguageKey));
        } else {
            echo "Collect at Circle K";
        }
        ?>
    </label>
    </input>
</div>
<div id="sixthRadio">
    <input type="radio" id="alfred_seven_eleven" name="alfred_seven_eleven" value="alfred_seven_eleven" onclick="on_change('seven_eleven')" <?php if(get_option('alfred-selected-locationtype') === 'SEVEN_ELEVEN'){echo "checked";}?>>
    <label for="alfred_seven_eleven" style="width:86%">
        <?php

        $shippingLanguageKey = 'alfred-seven-eleven-lang' . getSelectedLanguage();
        if (get_option($shippingLanguageKey)) {
            echo  htmlspecialchars(get_option($shippingLanguageKey));
        } else {
            echo "Collect at 7-Eleven";
        }
        ?>
    </label>
    </input>
</div>

</div>

<script>
//inject mapbox 
let loader = document.createElement("div");
loader.className = "loader";
loader.id = "loader";

let mapBoxWP = document.createElement("div");
mapBoxWP.className = "mapBox";
mapBoxWP.id = "mapBox";
mapBoxWP.appendChild(loader);

let mapWrapper = document.createElement("div");
mapWrapper.className = "mapWrapper";
mapWrapper.id = "mapWrapper";
mapWrapper.appendChild(mapBoxWP)


let modalContainer = document.createElement("div");
modalContainer.className = "alfred-woo-plugin-modal";
modalContainer.id = "alfred-woo-plugin-modal";
modalContainer.appendChild(mapWrapper)

console.log("modalContainer", modalContainer)
document.body.appendChild(modalContainer);
</script>



<script>

let selection = ""

var modal = document.getElementById("alfred-woo-plugin-modal");

function on_change(val) {
    val = val.toUpperCase();
    modal.classList.add("alfred-woo-plugin-showModal");
    if (val === 'ALFRED_LOCKER') {
        document.getElementById('alfred_locker_collect').checked = true;
        document.getElementById('alfred_locker_point').checked = false;
        // document.getElementById('alfred_locker_hkpost').checked = false;
        // document.getElementById('alfred_locker_hkpost_office').checked = false;
        document.getElementById('alfred_circle_k').checked = false;
        document.getElementById('alfred_seven_eleven').checked = false;
        selection = "alfred24 Locker";

    } else if( val === 'ALFRED_POINT') {
        document.getElementById('alfred_locker_collect').checked = false;
        document.getElementById('alfred_locker_point').checked = true;
        // document.getElementById('alfred_locker_hkpost').checked = false;
        // document.getElementById('alfred_locker_hkpost_office').checked = false;
        document.getElementById('alfred_circle_k').checked = false;
        document.getElementById('alfred_seven_eleven').checked = false;
        selection = "alfred24 Point";

    }else if( val === 'HKPOST_LOCKER'){
        document.getElementById('alfred_locker_collect').checked = false;
        document.getElementById('alfred_locker_point').checked = false;
        // document.getElementById('alfred_locker_hkpost').checked = true;
        // document.getElementById('alfred_locker_hkpost_office').checked = false;
        document.getElementById('alfred_circle_k').checked = false;
        document.getElementById('alfred_seven_eleven').checked = false;
        selection = "Hongkong Post iPostal Station";

    }else if( val === 'HKPOST_OFFICE'){
        document.getElementById('alfred_locker_collect').checked = false;
        document.getElementById('alfred_locker_point').checked = false;
        // document.getElementById('alfred_locker_hkpost').checked = false;
        // document.getElementById('alfred_locker_hkpost_office').checked = true;
        document.getElementById('alfred_circle_k').checked = false;
        document.getElementById('alfred_seven_eleven').checked = false;
        selection = "Hongkong Post Office";
        
    }else if( val === 'CIRCLE_K'){
        document.getElementById('alfred_locker_collect').checked = false;
        document.getElementById('alfred_locker_point').checked = false;
        // document.getElementById('alfred_locker_hkpost').checked = false;
        // document.getElementById('alfred_locker_hkpost_office').checked = false;
        document.getElementById('alfred_circle_k').checked = true;
        document.getElementById('alfred_seven_eleven').checked = false;
        selection = "Circle K";

    }else if( val === 'SEVEN_ELEVEN'){
        document.getElementById('alfred_locker_collect').checked = false;
        document.getElementById('alfred_locker_point').checked = false;
        // document.getElementById('alfred_locker_hkpost').checked = false;
        // document.getElementById('alfred_locker_hkpost_office').checked = false;
        document.getElementById('alfred_circle_k').checked = false;
        document.getElementById('alfred_seven_eleven').checked = true;
        selection = "Seven Eleven";

    }
    initEasyWidget(val);
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function(event) {
    let data;
    var getData = (arg) => {

        let dataContainer = document.getElementById("dataContainer");
        dataContainer.innerHTML = JSON.stringify(arg, censor(arg));
    };
    function censor(censor) {
        var i = 0;

        return function(key, value) {
            if (i !== 0 && typeof(censor) === 'object' && typeof(value) == 'object' && censor == value)
                return '[Circular]';

            if (i >= 29) // seems to be a harded maximum of 30 serialized objects?
                return '[Unknown]';

            ++i; // so we know we aren't using the original object anymore

            return value;
        }
    }
    const toggleShowData = () => {
        let dataContainer = document.getElementById("dataContainer");
        if (dataContainer.style.display === "none") {
            dataContainer.style.display = "block"
        } else {
            dataContainer.style.display = "none"
        }
    };

});
</script>


<script>
function initEasyWidget(locationType) {
    var getData = (arg) => {
        jQuery.ajax({
            url: document.URL,
            type: "POST",
            processData: true,
            data: {
                shippingAddress1: arg.address1En,
                shippingAddress2: arg.address2En,
                locationId: arg.locationId,
                region: arg.province,
                district: arg.district,
                country: arg.country,
                locationType: arg.locationType
            },
            success: function(data) {
                console.log("data");
            }

        });
    }
    if (_mapType !== undefined) {
        easyWidget.reset({
            filter: {
                locationType: [locationType],
                onSelect: getData

            }
        })
    } else {
        console.log("easyWiget map 70", _mapType)
        //all properties are null at first
        easyWidget.init({
            mapType: "osm",
            defaultLocation: "HK",
            mode: "modal",
            locale: "en",
            /* userAuthObject:{
                username: "appit",
                password: "CFMah7f5dLYXuwQZ"
            },*/
            filter: {
                locationType: [locationType],

            },
            onSelect: getData
        });

    }

}
</script>
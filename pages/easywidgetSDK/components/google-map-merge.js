const { Observable, from, defer, BehaviourSubject, pluck, of, range, merge, Subject } = rxjs;
const { flatMap, flatMapLatest, toArray, finalize, takeLast, findIndex, takeUntil, defaultIfEmpty } = rxjs.operators;

var lockerLocations = []
let typeFilterData = [],
    districtFilterData = [],
    regionFilterData = [],
    areaFilterData = []
let reactiveFilterData, reactiveDistrictData, reactiveRegionData, reactiveAreaData


// let locDataObservable =  getResponse(baseURL+locationApi);
let locDataObservable = getResponse(baseURL, _userAuthObject);

let multiFilterObject = {};

var numberOfFilteredItems = []

// custom dropdown variable
let dropDownElement;
let dropDownElementList = [];
let clickedIndex = 0;
let selectedLocationNameId;

// Object pass to CallBack function
let callBackObject = {};

// google map markers array testing
let gmarkers = [];
let marker;
let infoWindow;

//check if need to add dropDown
console.log(_locale)
console.log(_mode)
if (_mode === 'dropdown') {
    // create custom dropdown component
    dropDownElement = document.createElement("div");
    dropDownElement.className = "customDropDown"
    let customDropDownSelect = document.createElement("div");
    Object.assign(customDropDownSelect, {
        className: "customDropDownSelect",
        onclick: () => {
            // onClick function: toggle dropdown open
            document.getElementsByClassName("customDropDownContainer")[0].classList.toggle("customDropDownOpen")
        }
    });

    // dropdown placeholder
    let customDropDownSelectText = document.createElement("p");
    if (_locale === "en") {
        Object.assign(customDropDownSelectText, {
            className: "customDropDownSelectText",
            innerHTML: "alfred24 Click & Collect Options"
        })
    } else if (_locale === "zh") {
        Object.assign(customDropDownSelectText, {
            className: "customDropDownSelectText",
            innerHTML: "alfred24智能櫃地點"
        })
    }

    customDropDownSelect.appendChild(customDropDownSelectText);
    dropDownElement.appendChild(customDropDownSelect);

    // Function on window click, click anywhere on window and close the dropdown
    window.onclick = function(e) {
        let box = document.getElementsByClassName("customDropDownContainer")[0];
        let selectBox = document.getElementsByClassName("customDropDownSelect")[0];
        let selectText = document.getElementsByClassName("customDropDownSelectText")[0];

        if (e.target != selectBox && e.target != selectText) {
            if (Object.values(box.classList).indexOf('customDropDownOpen') > -1) {
                console.log(1)
                box.classList.remove('customDropDownOpen')
            }
        }
    }
}

// Create modal unique elements
if (_mode === 'modal') {
    var modal = document.getElementById("alfred-woo-plugin-modal");
    const closeModal = () => {
        const modal = document.getElementById("alfred-woo-plugin-modal");
        modal.classList.remove("showModal")
    }
    const openModal = () => {
        const modal = document.getElementById("alfred-woo-plugin-modal");
        modal.classList.add("showModal")
    }

    var modalOpenBtn = document.getElementById("modalOpenBtn");
    modalOpenBtn.addEventListener("click", openModal)

    // create top bar elements
    var topBar = document.createElement('div');
    topBar.className = "modalTopBar";

    let closeBtn = document.createElement('span');
    Object.assign(closeBtn, {
        className: "modalCloseBtn",
        onclick: closeModal,
        innerHTML: "✕"
    });

    var modalTitle = document.createElement('span');
    if (_locale === "en") {
        modalTitle.innerHTML = "Shipping method";
    } else if (_locale === "zh") {
        modalTitle.innerHTML = "郵寄方式";
    }

    topBar.appendChild(modalTitle);
    topBar.appendChild(closeBtn);

    modal.insertBefore(topBar, modal.firstChild);
    // create top bar elements finish

    // create confirm button
    var modalConfirmBtnWrapper = document.createElement("div");
    modalConfirmBtnWrapper.className = "modalConfirmBtnWrapper"
    var modalConfirmBtn = document.createElement("button");
    if (_locale === "en") {
        Object.assign(modalConfirmBtn, {
            className: "modalConfirmBtn",
            innerHTML: "Confirm",
            onClick: () => {}
        })
    } else if (_locale === "zh") {
        Object.assign(modalConfirmBtn, {
            className: "modalConfirmBtn",
            innerHTML: "確定",
            onClick: () => {}
        })
    }
    modalConfirmBtnWrapper.appendChild(modalConfirmBtn)
    modal.appendChild(modalConfirmBtnWrapper)
}
// Create unique modal element End


//create top level container HTML
let containerDiv = document.createElement("div");
containerDiv.className = 'containerDiv'
let containerMapBox = document.getElementById('mapBox')
if (_mode === "dropdown") {
    containerMapBox.style = "width: 100%"
}
containerDiv.appendChild(containerMapBox);

//Added from the onset so map can be displayed
document.body.appendChild(containerDiv)

// create left side dropdown and list
let leftWrapper = document.createElement("div");
leftWrapper.className = "mapLeftWrapper";
let filtersWrapper = document.createElement("div");
filtersWrapper.className = "mapFiltersWrapper";
let typeFilter = document.createElement("SELECT");
typeFilter.className = "typeFilter mapFilter";
let districtFilter = document.createElement("SELECT");
districtFilter.className = "districtFilter mapFilter";
let regionFilter = document.createElement("SELECT");
regionFilter.className = "regionFilter mapFilter";
let areaFilter = document.createElement("SELECT");
areaFilter.className = "areaFilter mapFilter";

// Create section wrapper: 
// If dropdown, create top filters bar / If not dropdown, create left radio button list
if (_mode === "dropdown") {
    document.getElementById("mapBarWrapper").appendChild(filtersWrapper)
} else {
    leftWrapper.appendChild(filtersWrapper);
}

let containerParentDiv = document.createElement("div");
containerParentDiv.className = 'containerParentTextDiv'

// Create search bar
// TODO: Add init variable to control search bar creation
let searchBar;
if (_withSearchBar) {
    searchBar = document.getElementById("searchBar");
};

let mapBox;
let mapView = new ol.View({
    center: ol.proj.fromLonLat([114.177216, 22.302711]),
    zoom: 12
})

// looping locDataObservable
// show all data points
// Create dropdown / radio button list items
locDataObservable.subscribe({
    next: result => {

        // console.log(result)
        // console.log(result.services)

        // reformat coordinate to openLayer Map use flat coordinate
        let lat = result.latitude
        let long = result.longitude
        let coordinates = ol.proj.fromLonLat([long, lat]);

        numberOfFilteredItems.push(result)

        lockerLocations.push(new ol.Feature({
            type: 'click',
            address1: result.address1,
            address1En: result.address1En,
            address2: result.address2,
            address2En: result.address2En,
            city: result.city,
            companyId: result.companyId,
            companyName: result.companyName,
            country: result.country,
            description: result.description,
            district: result.district,
            province: result.province,
            latitude: lat,
            longitude: long,
            locationEn: result.locationEn,
            locationGroup: result.locationGroup,
            locationId: result.locationId,
            locationName: result.locationName,
            locationType: result.locationType,
            services: result.services,
            operatingTime: result.operatingTime,
            postalCode: result.postalCode,
            availableCompartments: result.availableCompartments,
            geometry: new ol.geom.Point(coordinates)
        }));

        //inner HTML elements
        var containerTextDiv = document.createElement("div");
        containerTextDiv.className = 'containerChildTextDiv'

        var locationContainer = document.createElement("INPUT");
        Object.assign(locationContainer, {
            type: "radio",
            name: "list",
            id: result.locationId,
            className: "mapRadioBtn"
        })

        var locationContentDiv = document.createElement("label");
        Object.assign(locationContentDiv, {
            htmlFor: result.locationId,
            className: "label"
        })

        var logo = document.createElement("img");
        Object.assign(logo, {
            src: "./images/alfred-logo.png",
            className: "alfred-logo"
        });
        locationContentDiv.appendChild(logo);

        var locationContentText = document.createElement("div");

        if (result.locationName || result.locationId) {
            var locationName = document.createElement("P"); // Create a <p> element
            locationName.setAttribute("class", "locationName");
            if (_locale === "en") {
                locationName.innerHTML = result.locationId + " - " + result.locationEn; // Insert text
            } else if (_locale === "zh") {
                locationName.innerHTML = result.locationId + " - " + result.locationName; // Insert text
            }
            locationContentText.appendChild(locationName);
        }

        if (result.operatingTime) {
            var operatingTime = document.createElement("P");
            operatingTime.setAttribute("class", "operatingTime")
            operatingTime.innerHTML = result.operatingTime;
            locationContentText.appendChild(operatingTime);
        }

        var address = document.createElement("P");
        if (_locale === "en") {
            address.innerHTML = result.address1En;
        } else if (_locale === "zh") {
            address.innerHTML = result.address1;
        }
        address.setAttribute("class", "contentAddress")
        locationContentText.appendChild(address)

        var city = document.createElement("P");
        if (result.locationType) {
            Object.assign(city, {
                innerHTML: result.locationType + "  " + result.city,
                className: "contentCity"
            })
        } else {
            Object.assign(city, {
                innerHTML: result.city,
                className: "contentCity"
            })
        }
        locationContentText.appendChild(city);

        var coords = document.createElement("P")
        coords.setAttribute("hidden", true);
        coords.className = "coords"
        coords.innerHTML = long + "-" + lat
        locationContentText.appendChild(coords)

        var howToGetThere = document.createElement("a");
        if (_locale === "en") {
            Object.assign(howToGetThere, {
                href: "#",
                className: "howToGetThere",
                innerHTML: "How To Get There"
            })
        } else if (_locale === "zh") {
            Object.assign(howToGetThere, {
                href: "#",
                className: "howToGetThere",
                innerHTML: "如何找到智能櫃？"
            })
        }
        var browseIcon = document.createElement("img");
        browseIcon.style.width = "18px";
        browseIcon.setAttribute("src", "./images/browse-icon-transparent.png")
        howToGetThere.appendChild(browseIcon);
        locationContentText.appendChild(howToGetThere);

        locationContentDiv.appendChild(locationContentText);

        containerTextDiv.appendChild(locationContainer)
        containerTextDiv.appendChild(locationContentDiv)
        containerParentDiv.appendChild(containerTextDiv)
        if (_mode !== "dropdown") {
            leftWrapper.appendChild(containerParentDiv)
            containerDiv.appendChild(leftWrapper)
        }
        containerDiv.appendChild(containerMapBox)



        //add to dropdownlist
        if (_mode === 'dropdown') {
            var sb = new StringBuilder();
            sb.append(result.locationId || "empty");
            sb.append(" - ");
            if (_locale === "en") {
                sb.append(result.locationEn);
            } else if (_locale === "zh") {
                sb.append(result.locationName);
            }
            var myString = sb.toString();

            let customDropDownItem = document.createElement("div");
            customDropDownItem.className = "customDropDownItem";
            customDropDownItem.id = result.locationId;
            let customDropDownItemLocationName = document.createElement("p");
            let customDropDownItemLocationTypeCity = document.createElement("p");
            let customDropDownItemCoords = document.createElement("p");

            Object.assign(customDropDownItemLocationName, {
                className: "customDropDownItemLocationName",
                innerHTML: myString
            });

            Object.assign(customDropDownItemLocationTypeCity, {
                className: "customDropDownItemLocationTypeCity",
                innerHTML: result.locationType + "  " + result.city
            });

            Object.assign(customDropDownItemCoords, {
                hidden: true,
                className: "customDropDownItemCoords",
                innerHTML: long + "-" + lat
            })

            customDropDownItem.appendChild(customDropDownItemLocationName);
            customDropDownItem.appendChild(customDropDownItemLocationTypeCity);
            customDropDownItem.appendChild(customDropDownItemCoords);

            dropDownElementList.push(customDropDownItem)
        }
    },
    complete: () => {
        if (_mode === 'dropdown') {
            let customDropDownContainer = document.createElement("div");
            customDropDownContainer.className = "customDropDownContainer";

            let customDropDownBox = document.createElement("div");
            customDropDownBox.className = "customDropDownBox";

            for (var i = 0; i < dropDownElementList.length; i++) {
                customDropDownBox.appendChild(dropDownElementList[i])
            };
            customDropDownContainer.appendChild(customDropDownBox);
            dropDownElement.appendChild(customDropDownContainer);
            document.getElementById("mapBarWrapper").appendChild(dropDownElement);
            assignOnClickToDropDownItems();
        }
        if (_mode === "modal") {
            document.getElementById("mapWrapper").appendChild(containerDiv)
        } else {
            document.body.appendChild(containerDiv)
        }

        typeFilterData = Array.from(new Set(typeFilterData))
        districtFilterData = Array.from(new Set(districtFilterData)),
            regionFilterData = Array.from(new Set(regionFilterData)),
            areaFilterData = Array.from(new Set(areaFilterData)),

            reactiveFilterData = from(typeFilterData).subscribe({
                next: result => {
                    //console.log(result)
                    let typeFilterItem = document.createElement("option");
                    typeFilterItem.innerHTML = result;
                    typeFilter.appendChild(typeFilterItem);
                },
                complete: () => {
                    var dropDownSelectionEmpty = document.createElement("OPTION");
                    Object.assign(dropDownSelectionEmpty, {
                        disabled: true,
                        selected: true
                    })
                    if (_locale === "en") {
                        dropDownSelectionEmpty.innerHTML = "Type"
                    } else if (_locale === "zh") {
                        dropDownSelectionEmpty.innerHTML = "智能櫃種類"
                    }
                    typeFilter.insertBefore(dropDownSelectionEmpty, typeFilter.firstChild)
                    filtersWrapper.appendChild(typeFilter);
                }
            })

        reactiveDistrictData = from(districtFilterData).subscribe({
            next: result => {
                let districtFilterItem = document.createElement("option");
                districtFilterItem.innerHTML = result;
                districtFilter.appendChild(districtFilterItem);
            },
            complete: () => {
                var dropDownSelectionEmpty = document.createElement("OPTION");
                Object.assign(dropDownSelectionEmpty, {
                    disabled: true,
                    selected: true
                })
                if (_locale === "en") {
                    dropDownSelectionEmpty.innerHTML = "District"
                } else if (_locale === "zh") {
                    dropDownSelectionEmpty.innerHTML = "地區"
                }
                districtFilter.insertBefore(dropDownSelectionEmpty, districtFilter.firstChild)
                filtersWrapper.appendChild(districtFilter);
            }
        })

        reactiveRegionData = from(regionFilterData).subscribe({

            next: result => {
                let regionFilterItem = document.createElement("option");
                regionFilterItem.innerHTML = result;
                regionFilter.appendChild(regionFilterItem);
            },
            complete: () => {
                var dropDownSelectionEmpty = document.createElement("OPTION");
                Object.assign(dropDownSelectionEmpty, {
                    disabled: true,
                    selected: true
                })
                if (_locale === "en") {
                    dropDownSelectionEmpty.innerHTML = "Region"
                } else if (_locale === "zh") {
                    dropDownSelectionEmpty.innerHTML = "區域"
                }
                regionFilter.insertBefore(dropDownSelectionEmpty, regionFilter.firstChild)
                filtersWrapper.appendChild(regionFilter);
            }
        })

        reactiveAreaData = from(areaFilterData).subscribe({

            next: result => {
                let areaFilterItem = document.createElement("option");
                areaFilterItem.innerHTML = result;
                areaFilter.appendChild(areaFilterItem);
            },
            complete: () => {
                var dropDownSelectionEmpty = document.createElement("OPTION");
                Object.assign(dropDownSelectionEmpty, {
                    disabled: true,
                    selected: true
                })
                if (_locale === "en") {
                    dropDownSelectionEmpty.innerHTML = "Area"
                } else if (_locale === "zh") {
                    dropDownSelectionEmpty.innerHTML = "範圍"
                }
                areaFilter.insertBefore(dropDownSelectionEmpty, areaFilter.firstChild)
                filtersWrapper.appendChild(areaFilter);
            }
        })

        console.log(lockerLocations.length)

        console.log("_with search bar", _withSearchBar)
        if (_withSearchBar) {
            document.getElementsByClassName("mapFiltersWrapper")[0].style.display = 'none';
        }
        // mapBox = showMap();
        showMap();
        mapOnclick()
    }
})

// Assign Onclick function to dropdown items
const assignOnClickToDropDownItems = () => {
    let dropdownItem = document.getElementsByClassName("customDropDownItem");
    let clickedIndex = 0;
    Object.keys(dropdownItem).forEach((key, index) => {
        dropdownItem[key].addEventListener("click", () => {
            document.getElementsByClassName("customDropDownContainer")[0].classList.toggle("customDropDownOpen");
            dropdownItem[clickedIndex].classList.remove("customDropDownItemSelected");
            dropdownItem[index].classList.add("customDropDownItemSelected");
            let selectedLocationNameId = dropdownItem[key].getElementsByClassName("customDropDownItemLocationName")[0].innerHTML
            document.getElementsByClassName("customDropDownSelectText")[0].innerHTML = selectedLocationNameId
            clickedIndex = index;

            var selocationId = selectedLocationNameId.split(" - ")[0];
            for (let i = 0; i < lockerLocations.length; i++) {
                //console.log(lockerLocations[i].values_.locationId)
                console.log(lockerLocations[i].values_.locationId)
                if (lockerLocations[i].values_.locationId === selocationId) {
                    console.log("match found")
                    callBackObject = lockerLocations[i].values_;
                    _onSelect(callBackObject);

                    setPopUpContent(callBackObject);
                    infoWindow.open(mapBox, gmarkers[key])
                    let long = lockerLocations[i].values_.longitude
                    let lat = lockerLocations[i].values_.latitude

                    mapBox.setCenter({ lat: Number(lat), lng: Number(long) });
                    mapBox.setZoom(18)
                } else {
                    console.log("match not found uu999")
                }
            }
        })
    })
}


var reactiveList = from(lockerLocations);

const filterByDistrictParams$ = fromEvent([districtFilter], 'change').pipe(
    map($event => {
        let selectElement = $event.target;
        var optionIndex = selectElement.selectedIndex;
        var optionText = selectElement.options[optionIndex];
        //console.log(event)
        return optionText.innerHTML
    }),
    debounceTime(1000),
    distinctUntilChanged(),
    filter(function(value) {
        return value.length > 2;
    }),
    //flatMap(filterListByDistrictParams),
)

const filterByRegionParams$ = fromEvent([regionFilter], 'change').pipe(
    map($event => {
        let selectElement = $event.target;
        var optionIndex = selectElement.selectedIndex;
        var optionText = selectElement.options[optionIndex];
        console.log(event)
        return optionText.innerHTML
    }),
    debounceTime(1000),
    distinctUntilChanged(),
    filter(function(value) {
        return value.length > 2;
    }),
    //flatMap(filterListByRegionParams),
)




const filterByTypeParams$ = fromEvent([typeFilter], 'change').pipe(
    map($event => {


        let selectElement = $event.target;
        var optionIndex = selectElement.selectedIndex;
        var optionText = selectElement.options[optionIndex];

        return optionText.innerHTML
    }),
    debounceTime(1000),
    distinctUntilChanged(),
    filter(function(value) {
        return value.length > 2;
    }),
    //flatMap(filterListByTypeParams),
)

const filterByAreaParams$ = fromEvent([areaFilter], 'change').pipe(
    map($event => {


        let selectElement = $event.target;
        var optionIndex = selectElement.selectedIndex;
        var optionText = selectElement.options[optionIndex];

        return optionText.innerHTML
    }),
    debounceTime(1000),
    distinctUntilChanged(),
    filter(function(value) {
        return value.length > 2;
    }),
    //flatMap(filterListByTypeParams),
)



//combine the above: 
//var end$ = new Subject();  ....use this if you want to end the observable
const multiFilter = merge(

    filterByTypeParams$.pipe(
        map(x => {

            clickedIndex = 0;
            numberOfFilteredItems = []
            containerParentDiv.innerHTML = "" //HACK!!!!!!
            removeMarkers();
            if (_mode === "dropdown") {
                document.getElementsByClassName("customDropDownBox")[0].innerHTML = "";
            }
            if (x !== "ALL LOCKER TYPES" && x !== "全部智能櫃種類") {
                multiFilterObject['locationType'] = x
            } else {
                delete multiFilterObject['locationType']
            }
            return multiFilterObject
        }),
        //takeUntil(end$)

    ),

    filterByRegionParams$.pipe(map(x => {

            clickedIndex = 0;
            numberOfFilteredItems = []
            containerParentDiv.innerHTML = "" //HACK!!!!!!!!
            removeMarkers();
            if (_mode === "dropdown") {
                document.getElementsByClassName("customDropDownBox")[0].innerHTML = "";
            }

            if (x !== "ALL REGIONS" && x !== "全部區域") {
                console.log(multiFilterObject)
                multiFilterObject['province'] = x
            } else {
                delete multiFilterObject['province']
            }
            return multiFilterObject

        }),
        //takeUntil(end$),

    ),
    filterByDistrictParams$.pipe(
        map(x => {

            clickedIndex = 0;
            numberOfFilteredItems = []
            containerParentDiv.innerHTML = "" //HACK!!!!!!!
            removeMarkers();
            if (_mode === "dropdown") {
                document.getElementsByClassName("customDropDownBox")[0].innerHTML = "";
            }
            containerParentDiv.innerHTML = ""
            if (x !== "ALL DISTRICTS" && x !== "全部地區") {
                multiFilterObject['city'] = x
            } else {
                delete multiFilterObject['city']
            }

            return multiFilterObject
                //return x
        }),
        //takeUntil(end$)

    ),

    filterByAreaParams$.pipe(map(x => {
            //NEW CODE
            clickedIndex = 0;
            numberOfFilteredItems = []
            containerParentDiv.innerHTML = "" //HACK!!!!!!!
            removeMarkers();
            if (_mode === "dropdown") {
                document.getElementsByClassName("customDropDownBox")[0].innerHTML = "";
            }
            containerParentDiv.innerHTML = ""
            if (x !== "ALL AREA" && x !== "全部地區") {
                multiFilterObject['district'] = x
            } else {
                delete multiFilterObject['district']
            }

            return multiFilterObject
                //return x

        }),
        //takeUntil(end$),

    ),

).pipe(

    flatMap(multiFilterList),

)

//get center from array of long lat
function GetCenterFromDegrees(data) {
    if (!(data.length > 0)) {
        return false;
    }

    var num_coords = data.length;

    var X = 0.0;
    var Y = 0.0;
    var Z = 0.0;

    for (i = 0; i < data.length; i++) {
        var lat = data[i][0] * Math.PI / 180;
        var lon = data[i][1] * Math.PI / 180;

        var a = Math.cos(lat) * Math.cos(lon);
        var b = Math.cos(lat) * Math.sin(lon);
        var c = Math.sin(lat);

        X += a;
        Y += b;
        Z += c;
    }

    X /= num_coords;
    Y /= num_coords;
    Z /= num_coords;

    var lon = Math.atan2(Y, X);
    var hyp = Math.sqrt(X * X + Y * Y);
    var lat = Math.atan2(Z, hyp);

    var newX = (lat * 180 / Math.PI);
    var newY = (lon * 180 / Math.PI);

    return new Array(newX, newY);
}


//lodash function (src:https://github.com/lodash/lodash), to check if filter criteria matches location object
function isSubset(obj1, obj2) {
    let matched = true;
    _.forEach(obj1, (value, key) => {
        if (!_.isEqual(value, obj2[key])) {
            matched = false;
            return;
        }
    });
    return matched;
}

function multiFilterList(filterCriteria) {
    return reactiveList.pipe(
        filter(locationObject => {
            let matchFound = isSubset(filterCriteria, locationObject.values_)
            if (matchFound) {
                console.log("locationObject", locationObject)
                return locationObject
            }
        }),
        defaultIfEmpty(undefined)

    )
}

multiFilter.subscribe({
    next: r => {



        //setTimeout(function updateMap(){
        if (r === null) {
            console.log("inside null")
            let noResultText = document.createElement("p");
            noResultText.setAttribute("style", "text-align: center");
            if (_locale === "en") {
                noResultText.innerHTML = "The location you search for does not have any alfred24 Locker service"
            } else if (_locale === "zh") {
                noResultText.innerHTML = "你搜尋的地方沒有任何智能櫃服務"
            };
            if (_mode === "dropdown") {
                document.getElementsByClassName("customDropDownBox")[0].appendChild(noResultText);
            } else {
                containerParentDiv.appendChild(noResultText)
            }
            console.log(r)
            mapBox.setCenter({ lat: 22.302711, lng: 114.177216 });
            mapBox.setZoom(12)
        } else {
            let result = r.values_

            /*let centerArr = []
            numberOfFilteredItems.forEach(element => {

              console.log(element.latitude)
              centerArr.push({
                  lat: element.latitude,
                  long: element.longitude
              })
               
            });

            var longLat = GetCenterFromDegrees(centerArr)
            console.log(longLat)*/
            updateList(result)

            marker = new google.maps.Marker({ position: { lat: result.latitude, lng: result.longitude }, map: mapBox });
            gmarkers.push(marker);
            console.log(gmarkers.length)
            setMarkers(marker, result);
            //calculate center based on long/lat values later
            if (numberOfFilteredItems.length > 1 && numberOfFilteredItems.length < 15) {
                mapBox.setCenter({ lat: result.latitude, lng: result.longitude });
                mapBox.setZoom(15)
            } else if (numberOfFilteredItems.length > 15) {
                mapBox.setCenter({ lat: result.latitude, lng: result.longitude });
                mapBox.setZoom(13)
            } else {
                mapBox.setCenter({ lat: result.latitude, lng: result.longitude });
                mapBox.setZoom(18)
            }

        }

        //}, 1000);



    },


    complete: () => {
        return console.log('done multi filter data');


    }
})


//update LIST also hack, fix
function updateList(result) {
    numberOfFilteredItems.push(result)
    let lat = result.latitude
    let long = result.longitude

    console.log("update list result")
        /****START**/

    //inner HTML elements
    var containerTextDiv = document.createElement("div");
    containerTextDiv.className = 'containerChildTextDiv'

    var locationContainer = document.createElement("INPUT");
    Object.assign(locationContainer, {
        type: "radio",
        name: "list",
        id: result.locationId,
        className: "mapRadioBtn"
    })

    var locationContentDiv = document.createElement("label");
    Object.assign(locationContentDiv, {
        htmlFor: result.locationId,
        className: "label"
    })

    var logo = document.createElement("img");
    Object.assign(logo, {
        src: "./images/alfred-logo.png",
        className: "alfred-logo"
    });
    locationContentDiv.appendChild(logo);

    var locationContentText = document.createElement("div");

    if (result.locationName || result.locationId) {
        var locationName = document.createElement("P"); // Create a <p> element
        locationName.setAttribute("class", "locationName");
        if (_locale === "en") {
            locationName.innerHTML = result.locationId + " - " + result.locationEn; // Insert text
        } else if (_locale === "zh") {
            locationName.innerHTML = result.locationId + " - " + result.locationName; // Insert text
        }
        locationContentText.appendChild(locationName);
    }

    if (result.operatingTime) {
        var operatingTime = document.createElement("P");
        operatingTime.setAttribute("class", "operatingTime")
        operatingTime.innerHTML = result.operatingTime;
        locationContentText.appendChild(operatingTime);
    }

    var address = document.createElement("P");
    console.log(result)
    if (_locale === "en") {
        address.innerHTML = result.address1En;
    } else if (_locale === "zh") {
        address.innerHTML = result.address1;
    }
    address.setAttribute("class", "contentAddress")
    locationContentText.appendChild(address)

    var city = document.createElement("P");
    if (result.locationType) {
        Object.assign(city, {
            innerHTML: result.locationType + "  " + result.city,
            className: "contentCity"
        })
    } else {
        Object.assign(city, {
            innerHTML: result.city,
            className: "contentCity"
        })
    }
    locationContentText.appendChild(city);

    var coords = document.createElement("P")
    coords.setAttribute("hidden", true);
    coords.className = "coords"
    coords.innerHTML = long + "-" + lat
    locationContentText.appendChild(coords)

    var howToGetThere = document.createElement("a");
    if (_locale === "en") {
        Object.assign(howToGetThere, {
            href: "#",
            className: "howToGetThere",
            innerHTML: "How To Get There"
        })
    } else if (_locale === "zh") {
        Object.assign(howToGetThere, {
            href: "#",
            className: "howToGetThere",
            innerHTML: "如何找到智能櫃？"
        })
    }
    var browseIcon = document.createElement("img");
    browseIcon.style.width = "18px";
    browseIcon.setAttribute("src", "./images/browse-icon-transparent.png")
    howToGetThere.appendChild(browseIcon);
    locationContentText.appendChild(howToGetThere);

    locationContentDiv.appendChild(locationContentText);

    containerTextDiv.appendChild(locationContainer)
    containerTextDiv.appendChild(locationContentDiv)
    containerParentDiv.appendChild(containerTextDiv)
    if (_mode !== "dropdown") {
        leftWrapper.appendChild(containerParentDiv)
        containerDiv.appendChild(leftWrapper)
    }
    containerDiv.appendChild(containerMapBox)



    //add to dropdownlist
    if (_mode === 'dropdown') {
        var sb = new StringBuilder();
        sb.append(result.locationId || "empty");
        sb.append(" - ");
        console.log(result.locationEn)
        if (_locale === "en") {
            sb.append(result.locationEn);
        } else if (_locale === "zh") {
            sb.append(result.locationName);
        }
        var myString = sb.toString();

        let customDropDownItem = document.createElement("div");
        customDropDownItem.className = "customDropDownItem";
        customDropDownItem.id = result.locationId;
        let customDropDownItemLocationName = document.createElement("p");
        let customDropDownItemLocationTypeCity = document.createElement("p");
        let customDropDownItemCoords = document.createElement("p");

        Object.assign(customDropDownItemLocationName, {
            className: "customDropDownItemLocationName",
            innerHTML: myString
        });

        console.log(customDropDownItemLocationName)

        Object.assign(customDropDownItemLocationTypeCity, {
            className: "customDropDownItemLocationTypeCity",
            innerHTML: result.locationType + "  " + result.city
        });

        Object.assign(customDropDownItemCoords, {
            hidden: true,
            className: "customDropDownItemCoords",
            innerHTML: long + "-" + lat
        })

        customDropDownItem.appendChild(customDropDownItemLocationName);
        customDropDownItem.appendChild(customDropDownItemLocationTypeCity);
        customDropDownItem.appendChild(customDropDownItemCoords);

        customDropDownItem.addEventListener("click", () => {
            let dropdownItem = document.getElementsByClassName("customDropDownItem");
            dropdownItem[clickedIndex].classList.remove("customDropDownItemSelected");
            customDropDownItem.classList.add("customDropDownItemSelected");
            selectedLocationNameId = customDropDownItemLocationName.innerHTML
            callBackObject = result;
            _onSelect(callBackObject);
            document.getElementsByClassName("customDropDownSelectText")[0].innerHTML = selectedLocationNameId;
            let selectedItemIndex = numberOfFilteredItems.findIndex(item => item.locationId === result.locationId);
            clickedIndex = selectedItemIndex;
            setPopUpContent(callBackObject);
            infoWindow.open(mapBox, gmarkers[selectedItemIndex])

            var selocationId = selectedLocationNameId.split(" - ")[0];
            for (let i = 0; i < lockerLocations.length; i++) {
                //console.log(lockerLocations[i].values_.locationId)
                console.log(lockerLocations[i].values_.locationId)
                if (lockerLocations[i].values_.locationId === selocationId) {
                    console.log("match found")
                    let long = lockerLocations[i].values_.longitude
                    let lat = lockerLocations[i].values_.latitude

                    mapBox.setCenter({ lat: Number(lat), lng: Number(long) });
                    mapBox.setZoom(18)
                } else {
                    console.log("match not found")
                }
            }
        })

        document.getElementsByClassName("customDropDownBox")[0].appendChild(customDropDownItem)
            // dropDownElementList.push(customDropDownItem)
            // assignOnClickToDropDownItems()
    }
}




function filterListByCity(filterVal) {

    return reactiveList.pipe(
        filter(x => {

            //console.log("lol")
            if (x.values_.city === filterVal) {
                //console.log(x) 
                return x
            }

        }),

        takeLast(1), //return only last value and set center to its coordinates

    )
}


if (_withSearchBar) {
    //filter data from search bar
    const filterData$ = fromEvent(searchBar, 'input')
        .pipe(
            map(e => {
                //console.log(e)
                return e.target.value
            }),
            debounceTime(1000),
            distinctUntilChanged(),
            filter(function(value) {
                return value.length > 2;
            }),
            flatMap(filterListByCity),


        )


    filterData$.subscribe({
        next: result => {
            console.log(result.values_)
            mapBox.setCenter({ lat: result.values_.latitude, lng: result.values_.longitude });
            mapBox.setZoom(18);
            let radioBtnList = document.getElementsByClassName("mapRadioBtn");
            // console.log(radioBtnList.length)
            Object.keys(radioBtnList).forEach((key, index) => {
                if (radioBtnList[key].id === result.values_.locationId) {
                    setFocus(index)
                }
            })

        },
        complete: () => {
            return console.log('done filter data');
        }
    });
}


//const filterDropDownData = fromEvent(dro)

function setFocus(num) {
    const textDiv = document.getElementsByClassName("containerChildTextDiv");
    Object.keys(textDiv).forEach((key, index) => {
        textDiv[key].classList.remove("selected-background");
        if (textDiv[key].getElementsByClassName("contentCity")[0] !== undefined) {
            textDiv[key].getElementsByClassName("contentCity")[0].classList.remove("display-none")
        }

        if (textDiv[key].getElementsByClassName("contentAddress")[0] !== undefined) {
            textDiv[key].getElementsByClassName("contentAddress")[0].classList.remove("display-block")
        }
        if (textDiv[key].getElementsByClassName("operatingTime")[0] !== undefined) {
            textDiv[key].getElementsByClassName("operatingTime")[0].classList.remove("display-block")
        }
        if (textDiv[key].getElementsByClassName("howToGetThere")[0] !== undefined) {
            textDiv[key].getElementsByClassName("howToGetThere")[0].classList.remove("display-flex")
        }
    });

    textDiv[num].getElementsByClassName("mapRadioBtn")[0].checked = true;
    textDiv[num].classList.add("selected-background");
    textDiv[num].getElementsByClassName("contentAddress")[0].classList.add("display-block");
    if (textDiv[num].getElementsByClassName("operatingTime")[0]) {
        textDiv[num].getElementsByClassName("operatingTime")[0].classList.add("display-block");
    }
    textDiv[num].getElementsByClassName("howToGetThere")[0].classList.add("display-flex");
    textDiv[num].getElementsByClassName("contentCity")[0].classList.add("display-none");
    // document.getElementsByTagName("input")[num].focus();
    textDiv[num].getElementsByClassName("mapRadioBtn")[0].focus();
}

function showMap() {
    infoWindow = new google.maps.InfoWindow;
    mapBox = new google.maps.Map(document.getElementById("mapBox"), {
        center: { lat: 22.302711, lng: 114.177216 },
        zoom: 12
    });
    console.log(locDataObservable);
    locDataObservable.subscribe({
        next: result => {
            marker = new google.maps.Marker({ position: { lat: result.latitude, lng: result.longitude }, map: mapBox });
            gmarkers.push(marker);
            setMarkers(marker, result)
        },
        complete: () => {}
    })
}

const onListClick$ = fromEvent(containerParentDiv, 'click').pipe(
    map(event => {

        const textDiv = document.getElementsByClassName("containerChildTextDiv");
        Object.keys(textDiv).forEach((key, index) => {
            textDiv[key].classList.remove("selected-background");
            if (textDiv[key].getElementsByClassName("contentCity")[0] !== undefined) {
                textDiv[key].getElementsByClassName("contentCity")[0].classList.remove("display-none")
            }

            if (textDiv[key].getElementsByClassName("contentAddress")[0] !== undefined) {
                textDiv[key].getElementsByClassName("contentAddress")[0].classList.remove("display-block")
            }
            if (textDiv[key].getElementsByClassName("operatingTime")[0] !== undefined) {
                textDiv[key].getElementsByClassName("operatingTime")[0].classList.remove("display-block")
            }
            if (textDiv[key].getElementsByClassName("howToGetThere")[0] !== undefined) {
                textDiv[key].getElementsByClassName("howToGetThere")[0].classList.remove("display-flex")
            }
        });
        console.log("before if")

        if (
            event.srcElement.parentNode.closest(".containerChildTextDiv") !== null
        ) {
            let selectedLocationId = event.srcElement.parentNode.getElementsByClassName("locationName")[0].innerHTML.split(" - ")[0];
            let selectedItemIndex = numberOfFilteredItems.findIndex(item => item.locationId === selectedLocationId);
            callBackObject = numberOfFilteredItems[selectedItemIndex];
            _onSelect(callBackObject);
            setPopUpContent(callBackObject);
            infoWindow.open(mapBox, gmarkers[selectedItemIndex])

            event.srcElement.parentNode.closest(".containerChildTextDiv").classList.add("selected-background");
            event.srcElement.parentNode.getElementsByClassName("contentAddress")[0].classList.add("display-block");
            if (event.srcElement.parentNode.getElementsByClassName("operatingTime")[0] !== undefined) {
                event.srcElement.parentNode.getElementsByClassName("operatingTime")[0].classList.add("display-block")
            };
            event.srcElement.parentNode.getElementsByClassName("howToGetThere")[0].classList.add("display-flex");
            event.srcElement.parentNode.getElementsByClassName("contentCity")[0].classList.add("display-none");
        }
        if (event.srcElement.parentNode.className !== "containerParentTextDiv") {
            // Special handle for clicking on no result text
            if (event.srcElement.parentNode.closest(".containerChildTextDiv") !== undefined && event.srcElement.parentNode.closest(".containerChildTextDiv") !== null) {
                return event.srcElement.parentNode.closest(".containerChildTextDiv").getElementsByClassName("coords")[0].innerHTML
            } else {
                return null
            }
        } else {
            if (event.srcElement.parentNode.getElementsByClassName("coords")[0] !== undefined && event.srcElement.parentNode.getElementsByClassName("coords")[0] !== null) {
                return event.srcElement.parentNode.getElementsByClassName("coords")[0].innerHTML
            } else {
                return null
            }
        }
    }),

)

onListClick$.subscribe({
    next: result => {
        if (result === null) return false
            //format => [long, lat]
        let coordArray = result.split("-");
        let long = coordArray[0]
        let lat = coordArray[1]

        //long lat are string so convert to float first
        mapBox.setCenter({ lat: Number(lat), lng: Number(long) });
        mapBox.setZoom(18)
    }
})

const removeMarkers = () => {
    for (i = 0; i < gmarkers.length; i++) {
        gmarkers[i].setMap(null)
    };
    gmarkers = [];
}

const setMarkers = (marker, result) => {
    // for (i = 0; i < gmarkers.length; i++) {
    marker.setMap(mapBox);
    marker.addListener('click', function() {
            let popUpWrapper = document.createElement("div");
            let popUpTitle = document.createElement("b");
            let popUpAddress = document.createElement("p");
            let popUpOperatingTime = document.createElement("p");
            let popUpHowToGetThere = document.createElement("a");
            if (_locale === "en") {
                popUpTitle.innerHTML = result.locationId + " - " + result.locationEn;
                popUpAddress.innerHTML = result.address1En;
                popUpOperatingTime.innerHTML = result.operatingTime;
                popUpHowToGetThere.innerHTML = "How to get there";
            } else if (_locale === "zh") {
                popUpTitle.innerHTML = result.locationId + " - " + result.locationName;
                popUpAddress.innerHTML = result.address1;
                popUpOperatingTime.innerHTML = result.operatingTime;
                popUpHowToGetThere.innerHTML = "如何找到智能櫃？";
            }
            let browseIcon = document.createElement("img");
            browseIcon.style.width = "15px";
            browseIcon.setAttribute("src", "./images/browse-icon-transparent.png")
            popUpHowToGetThere.appendChild(browseIcon);
            popUpAddress.style.color = "grey";
            popUpOperatingTime.style.color = "grey";
            // popUpHowToGetThere.style.color = "grey";
            // popUpHowToGetThere.style.cursor = "pointer";
            popUpHowToGetThere.setAttribute("style", "color: grey; text-decoration: none; cursor: pointer; display: flex; align-items: flex-start")
            popUpWrapper.appendChild(popUpTitle);
            popUpWrapper.appendChild(popUpAddress);
            popUpWrapper.appendChild(popUpOperatingTime);
            popUpWrapper.appendChild(popUpHowToGetThere);
            infoWindow.setContent(popUpWrapper)
            infoWindow.open(mapBox, marker);
            if (_mode === "dropdown") {
                let dropdownItemSelectedByMapMarker = document.getElementById(result.locationId);
                console.log("dropdownItemSelectedByMapMarker", dropdownItemSelectedByMapMarker)
                let dropdownItem = document.getElementsByClassName("customDropDownItem");
                dropdownItem[clickedIndex].classList.remove("customDropDownItemSelected");
                dropdownItemSelectedByMapMarker.classList.add("customDropDownItemSelected");
                selectedLocationNameId = dropdownItemSelectedByMapMarker.getElementsByClassName("customDropDownItemLocationName")[0].innerHTML;
                document.getElementsByClassName("customDropDownSelectText")[0].innerHTML = selectedLocationNameId;
                let selectedItemIndex = numberOfFilteredItems.findIndex(item => item.locationId === result.locationId);
                clickedIndex = selectedItemIndex;
                dropdownItemSelectedByMapMarker.scrollIntoView();
            }
            callBackObject = result;
            _onSelect(callBackObject);

            // setMarker onclick to move list
            let radioBtnList = document.getElementsByClassName("mapRadioBtn");
            console.log(radioBtnList.length)
            Object.keys(radioBtnList).forEach((key, index) => {
                if (radioBtnList[key].id === result.locationId) {
                    setFocus(index)
                }
            })
        })
        // };
}

const setPopUpContent = result => {
    let popUpWrapper = document.createElement("div");
    let popUpTitle = document.createElement("b");
    let popUpAddress = document.createElement("p");
    let popUpOperatingTime = document.createElement("p");
    let popUpHowToGetThere = document.createElement("a");
    if (_locale === "en") {
        popUpTitle.innerHTML = result.locationId + " - " + result.locationEn;
        popUpAddress.innerHTML = result.address1En;
        popUpOperatingTime.innerHTML = result.operatingTime;
        popUpHowToGetThere.innerHTML = "How to get there";
    } else if (_locale === "zh") {
        popUpTitle.innerHTML = result.locationId + " - " + result.locationName;
        popUpAddress.innerHTML = result.address1;
        popUpOperatingTime.innerHTML = result.operatingTime;
        popUpHowToGetThere.innerHTML = "如何找到智能櫃？";
    }
    let browseIcon = document.createElement("img");
    browseIcon.style.width = "15px";
    browseIcon.setAttribute("src", "./images/browse-icon-transparent.png")
    popUpHowToGetThere.appendChild(browseIcon);
    popUpAddress.style.color = "grey";
    popUpOperatingTime.style.color = "grey";
    // popUpHowToGetThere.style.color = "grey";
    // popUpHowToGetThere.style.cursor = "pointer";
    popUpHowToGetThere.setAttribute("style", "color: grey; text-decoration: none; cursor: pointer; display: flex; align-items: flex-start")
    popUpWrapper.appendChild(popUpTitle);
    popUpWrapper.appendChild(popUpAddress);
    popUpWrapper.appendChild(popUpOperatingTime);
    popUpWrapper.appendChild(popUpHowToGetThere);
    infoWindow.setContent(popUpWrapper)
}
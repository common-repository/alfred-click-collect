const { fromEvent, EMPTY, forkJoin } = rxjs;
const { fromFetch } = rxjs.fetch;
const { ajax } = rxjs.ajax;
const {
  catchError,
  debounceTime,
  distinctUntilChanged,
  filter,
  map,
  mergeMap,
  switchMap,
  tap,
  delay,
} = rxjs.operators;

let completeResultArray = [];
function getResponse(url, authUserObject) {
  const locationData$ = fromFetch(locationApiNew, {
    method: "get",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Bearer " + localStorage.getItem("token"),
    },
  }).pipe(
    switchMap((response) => {
      //console.log("restoken value is "+localStorage.getItem('token'))
      if (response.ok) {
        return response.json();
      } else {
        // Server is returning a status requiring the client to try something else.
        return of({ error: true, message: `Error ${response.status}` });
      }
    })
  );

  const data$ = fromFetch(loginApi, {
    method: "post",
    body: JSON.stringify(authUserObject),
    headers: {
      "Content-Type": "application/json",
    },
  }).pipe(
    switchMap((response) => {
      if (response.ok) {
        // OK return data

        return response.json();
      } else {
        // Server is returning a status requiring the client to try something else.
        return of({ error: true, message: `Error ${response.status}` });
      }
    }),
    map((response) => {
      localStorage.setItem("token", response.token);
      //return response.token
    }),
    switchMap(() => {
      return locationData$;
    }),
    map((response) => {
      return response.docs;
    }),
    mergeMap((locationObject) => locationObject),
    filter((locationObject) => {
      let lat = locationObject.latitude;
      let long = locationObject.longitude;

      //filtered out locations that have empty/missing values for lat/long

      if (
        lat % 1 != 0 &&
        long % 1 != 0 &&
        lat !== null &&
        long !== null &&
        !isNaN(lat) &&
        !isNaN(long)
      ) {
        let coordinates = ol.proj.fromLonLat([long, lat]);

        if (!isNaN(coordinates[0]) && !isNaN(coordinates[1])) {
          return locationObject;
        }
      }
    }),
    filter((locationObject) => {
      if (locationObject.city !== null) {
        return locationObject;
      }
    }),
    filter((locationObject) => {
      if (locationObject.address1En !== null) {
        return locationObject;
      }
    }),
    filter((locationObject) => {
      if (locationObject.locationType !== "THIRD_PARTY_LOCKER") {
        return locationObject;
      }
    }),
    filter((locationObject) => {
      //filter according to _filterCriteria
      //console.log(locationObject)
      if (_filterCriteria !== null) {
        let matchFound;
        for (var prop in _filterCriteria) {
          if (Object.prototype.hasOwnProperty.call(locationObject, prop)) {
            //check if locationObject has property defined in filterCriteria
            matchFound = false;
            for (var i = 0; i < _filterCriteria[prop].length; i++) {
              //check if locationObject[prop] is an array
              if (Array.isArray(locationObject[prop])) {
                console.log("is array");

                for (var j = 0; j < locationObject[prop].length; j++) {
                  if (locationObject[prop][j] === _filterCriteria[prop][i]) {
                    matchFound = true;
                  }
                }
              } else {
                if (locationObject[prop] === _filterCriteria[prop][i]) {
                  matchFound = true;
                }
              }
            }
          } else {
            console.log("spelling mistake in filter");
          }
        }
        if (matchFound) return locationObject;
      } else {
        return locationObject;
      }
    }),
    filter((locationObject) => {
      //console.log(locationObject);
      //typeFilterData.push(locationObject)
      //console.log(typeof locationObject.locationType);
      //typeFilterData.push(JSON.stringify(locationObject.locationType))
      // console.log(typeFilterData)

      /*typeFilterData = [];
      districtFilterData = [];
      areaFilterData = [];
      regionFilterData = [];*/

      if (_locale === "en") {
        typeFilterData.push("All Locker Types");
      } else if (_locale === "zh") {
        typeFilterData.push("全部地點類型");
      }
      //typeFilterData.push("ALL LOCKER TYPES")
      let locationtype = capitaliseString(locationObject.locationType);

      if (locationtype === "Fpx Locker") locationtype = "4PX Locker";

      if (locationtype === "Fpx Redemption") locationtype = "4PX Pickup Point";

      if (locationtype === "Seven Eleven") locationtype = "7 Eleven";

      if (locationtype === "Hkpost Locker") locationtype = "HKPost Locker";

      typeFilterData.push(locationtype);
      //typeFilterData.push(locationObject.locationType)
      //NEW CODE
      if (_locale === "en") {
        //districtfilterdata
        districtFilterData.push("All Districts");
      } else if (_locale === "zh") {
        districtFilterData.push("全部地區");
      }

      districtFilterData.push(locationObject.city);
      //NEW CODE
      if (_locale === "en") {
        regionFilterData.push("All Regions");
      } else if (_locale === "zh") {
        regionFilterData.push("全部區域");
      }
      regionFilterData.push(locationObject.province);

      if (_locale === "en") {
        areaFilterData.push("All Areas");
      } else if (_locale === "zh") {
        areaFilterData.push("全部範圍");
      }
      areaFilterData.push(locationObject.district);
      //NEW CODE

      return locationObject;
    }),
    filter((locationObject) => {
      completeResultArray = Array.from(new Set(completeResultArray));
      completeResultArray.push(locationObject);
      return locationObject;
    }),
    catchError((err) => {
      // Network or other error, handle appropriately
      //console.error(err);
      return of({ error: true, message: err.message });
    })
  );

  return data$;
}

function capitaliseString(str) {
  var res = str.split("_");
  var first = res[0][0].toUpperCase() + res[0].slice(1).toLowerCase();
  var last = res[1][0].toUpperCase() + res[1].slice(1).toLowerCase();

  return first + " " + last;
}

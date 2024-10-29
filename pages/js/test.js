let valTest = "val test";
console.log("insde test.js");
console.log("insde test.js 2");

async function loadScripts(scriptURL, obj) {
  await new Promise(function (resolve, reject) {
    var link = document.createElement("script");

    link.src = scriptURL;
    Object.assign(link, obj);
    document.body.appendChild(link);

    link.onload = function () {
      resolve();
    };
  });
}

async function load() {
  await loadScripts(
    "http://localhost:8888/alfred-plugin/wp-content/plugins/alfred-pick-drop-plugin/pages/easywidgetSDK/lib/constants.js"
  );
}

var path = scriptParams.path,
  secondUser = scriptParams.users[1]; /* index starts at 0 */

console.log("oath is ", path);
// iterate over users

//load();

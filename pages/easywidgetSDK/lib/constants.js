if (typeof locationConstant !== "undefined") {
  // the variable is defined
  console.log("all ready");
} else {
  var locationConstant = {
    HK: {
      urlProd: "https://alfredhk-api.pakpobox.com/",
      urlUAT: "https://alfred-uat-api.pakpobox.com/",
    },
    AU: {
      urlProd: "https://api.alfred24.com.au/",
      urlUAT: "https://api-stage.alfred24.com.au/",
    },
  };
}

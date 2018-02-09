/**
 * @file
 * Car to add cupcakes.
 */

function getCupcake(node) {
  response = httpGet(node, "add");
  jQuery(".amount-cupcakes").text("CUPCAKES ( " + response + " )");
}

function delCupcake(node) {
  response = httpGet(node, "del");
  jQuery(".amount-cupcakes").text("CUPCAKES ( " + response + " )");
}

function httpGet(node, type) {
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open( "GET", '/cupcake/sale?_format=json&node=' + node + "&type=" + type, false ); // false for synchronous request
  xmlHttp.send( null );
  return xmlHttp.responseText;
}
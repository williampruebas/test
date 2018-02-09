/**
 * @file
 * Car to add cupcakes.
 */

function downloadInvoice() {
  response = httpGetInvoice();
  if(response){
    window.location.href = '../';
  }
}

function httpGetInvoice() {
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open( "GET", '/cupcake/sale?_format=json&type=confirm', false ); // false for synchronous request
  xmlHttp.send( null );
  return xmlHttp.responseText;
}
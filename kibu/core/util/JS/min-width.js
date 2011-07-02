/*******************************************************************************
 min-width.js
 -> implements a window-resize layout switch for IE7 and IE8
 ------------------------------------------------------------------------------
*******************************************************************************/


var structure = document.styleSheets[document.styleSheets.length - 1];
function doQuery()
{
	structure.disabled = document.documentElement.offsetWidth < 481;
}
window.attachEvent('onresize', doQuery);
doQuery();
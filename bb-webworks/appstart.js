//this file is dynamically eval()d by the app at runtime
//it's about as big of security risk as the rest of the app, since other parts load and display raw html...
//besides, can webworks apps actually mess up your tablet? you should be suspect if this app ever starts asking for camera permissions I guess...
$(document).ready(function(){
	$("#lastUpdatedBlock").text("Data last updated May 30 2013");	
});
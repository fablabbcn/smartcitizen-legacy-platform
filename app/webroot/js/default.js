/* Author:






/* ------- Config --------*/
//Behaviour
ajaxEnabled=true;
$.support.cors = true; // cross-domaon ajax request in IE 8-9.

//Animation
var currentDiv = $('#principal')
var currentDivAside = $('#complementary')





$(document).ready( function() {

	makeHideable(currentDiv);
	makeHideableAside(currentDivAside);
	//$('#main').remove()
	if(ajaxEnabled){
		ajaxify(nav);
		ajaxify(currentDiv);
		ajaxify(currentDivAside);
	}
	
} );
/*------ AJAX SETUP -----*/
//- BASE Function to load content


function ajaxify (element){
	console.log('ajaxify');
	// OVERWRITING LINKS default behavior
	$(element).find("a").click(function(e){
		if($(this).attr('href').indexOf("posts/edit") == -1 ){ //if not an ection related to post/edit.
			e.preventDefault(); 				//if commented, html5 nonsupported browers will reload the page to the specified link.
			pageurl = $(this).attr('href'); 	//get the link location that was clicked
			getPage(pageurl);					//ajax call to server + animations

			return false;//stop refreshing to the page given in
		}else
		window.location.href=$(this).attr('href');//do the normal action, reloading the page. Not really necessary!
	});

	// OVERRIDING FORMS default behavior
	$(element).find("form").submit(function(e) {
		if($(this).attr('action').indexOf("posts/edit") == -1){ //if not an ection related to post/edit.
			console.log($(this).attr('action'));
			e.preventDefault();
			$(this).ajaxSubmit({
					dataType: 'json',
					beforeSubmit:  loading, // pre-submit callback  
					complete: loadingFinish,
					success:  loadPage
			});
			return false;
		}
	});
}

function getPage(pageurl){
	if('/'+pageurl!=window.location.pathname){
/*		
		if(pageurl.indexOf("?") != -1)
			var ajaxurl=pageurl+'&ajax=1';
		else
			var ajaxurl=pageurl+'?ajax=1';
*/
		loading();
		$.ajax({
			url: pageurl,
			type: "GET", 
			dataType: 'json',
			beforeSend : loading,
			complete : loadingFinish,
			success : loadPage,
		});
//		$.getJSON( ajaxurl, loadPage ); //short equivalent.
		//Change the browser URL to the given link location
		window.history.pushState({path:pageurl},'',pageurl);
    }
}
function loading(jqXHR, settings) {
//	currentDiv.animate({alpha: .5}, 500);
}
function loadingFinish(jqXHR, textStatus) {
//	currentDiv.animate({alpha: 1}, 500);
	console.log(textStatus);
	if(textStatus=='parsererror') // Response is not a json, like php errors
		$('#log').html('<div>'+jqXHR.responseText+'</div>');
}

function loadPage (response, textStatus, xhr) {
	
//	if(response.url)
//		window.history.pushState({path:response.url},'',response.url);
		
	if(response.flash){
		var logDiv=$('<div>')
		logDiv.hide();
		$('#flash').append(logDiv);
		logDiv.html(response.flash)
		ajaxify(logDiv);
		logDiv.slideDown(500).delay(4000).slideUp(1000,function(){
			$(this).remove()}
		);
	}
	
	if(response.content){	//change div if data without error
		//move the older div away
		currentDiv.animate({right: - currentDiv.width()}, 500, function() {
			$(this).remove();
		});
		//currentDiv.remove()
		//create and animate the new div
		var newDiv =$('<div>');
		newDiv.hide();
		newDiv.append($('<div>').addClass('inside_content').html(response.content));
		$('#content').append(newDiv);
		ajaxify(newDiv);						//formating content
		newDiv.css('right',- newDiv.width()); //caculating starting point animation
		newDiv.show();							
		newDiv.animate({right: 0}, 500);
		currentDiv=newDiv;						//storing the div location for next animation
		//to change the browser URL to the given link location
		makeHideable(newDiv);
		
	}
	
	if(response.aside){
		currentDivAside.animate({left: - currentDivAside.width()}, 500, function() {
			$(this).remove();
		});
		//create and animate the new div
		var newDivA =$('<div>');
		newDivA.hide();
		$('#aside').append(newDivA);
		newDivA.html(response.aside);
		ajaxify(newDivA);						//formating content
		newDivA.css('left',- newDivA.width()); //caculating starting point animation
		newDivA.show();							
		newDivA.animate({left: 0}, 500);
		
		currentDivAside=newDivA;						//storing the div location for next animation
		makeHideableAside(newDivA);
	}
	
	if(response.script){
		$('#scripts').html(response.script);
		if(response.script.indexOf('tinymce')!=-1){
				console.log('loading tinymce');
				tinymce.execCommand('mceAddControl',true,'editor_id');
			}
	}
	if(response.inlinescript){
		setTimeout(function(){
			eval(response.inlinescript);
		}, 600);
	}
}



//- MANAGING URL READING 
// the below code is to override back button to get the ajax content without page reload
$(window).bind('popstate', function() {
	if(ajaxEnabled)
	  getPage(location.pathname);
});

//- MANAGING URL READING v2 (support of html4 browser thanks to history.js)
/*
(function(window,undefined){

	// Establish Variables
	var
		History = window.History, // Note: We are using a capital H instead of a lower h
//		State = History.getState(),
//		$log = $('#log');
	// Log Initial State
//	History.log('initial:', State.data, State.title, State.url);

	// Bind to State Change
	History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
		// Log the State
//		var State = History.getState(); // Note: We are using History.getState() instead of event.state
//		History.log('statechange:', State.data, State.title, State.url);
	});
	
})(window);
*/






/*---- OTHER INTERFACE ANIMATION ----*/

function makeHideable(element){
	
	var newBtn =$('<div>');		
    newBtn.addClass('closeBtn');
	newBtn.click(function(e){
		if($(this).parent().css('right')=='0px'){
			$(this).parent().animate({right: - (parseInt($(this).parent().width()) + parseInt($(this).parent().css('padding-right'))) +'px'}, 500);
			$(this).removeClass('closeBtn');
			$(this).addClass('openBtn');				
		}else{
			$(this).parent().animate({right: 0}, 500);
			$(this).removeClass('openBtn');
			$(this).addClass('closeBtn');
		}
	
	//	alert(e.target === this);
	//	e.stopPropagation();
	});
	
	element.append(newBtn);
	element.scroll(function() {
	  newBtn.css('top', element.scrollTop());
	});
}

function makeHideableAside(element){
	var newBtn =$('<div>');		
    newBtn.addClass('closeBtnAside');
	newBtn.click(function(e){
		if($(this).parent().css('left')=='0px'){
			$(this).parent().animate({left: -(parseInt($(this).parent().width())+ parseInt($(this).parent().css('padding-left'))) +'px'}, 500);
			$(this).removeClass('closeBtnAside');
			$(this).addClass('openBtnAside');				
		}else{
			$(this).parent().animate({left: 0}, 500);
			$(this).removeClass('openBtnAside');
			$(this).addClass('closeBtnAside');
		}
	
	//	alert(e.target === this);
	//	e.stopPropagation();
	});
	
	element.append(newBtn);
		
}



/*----- MAP SETUP -----*/

var cloudmadeUrl = "http://{s}.tile.cloudmade.com/c1782add755b47c791ad8bbb376cad0b/{styleId}/256/{z}/{x}/{y}.png";
var cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade';

var blue   = L.tileLayer(cloudmadeUrl, {styleId: 71883, attribution: cloudmadeAttribution, minzoom: 5, maxzoom: 12}),
    light  = L.tileLayer(cloudmadeUrl, {styleId: 71869,   attribution: cloudmadeAttribution}),
    minimal  = L.tileLayer(cloudmadeUrl, {styleId: 22677,   attribution: cloudmadeAttribution}),
    red = L.tileLayer(cloudmadeUrl, {styleId: 71881, attribution: cloudmadeAttribution});
	
var grey = L.tileLayer('http://{s}.tiles.mapbox.com/v3/adrelanex.map-aq9bz8ek/{z}/{x}/{y}.png', {
	maxZoom: 18,
	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; <a href="http://smartcitizen.me">SmartCitizen</a>'
});
/*	
var map = L.TileJSON.createMap('map', osmTileJSON);
	*/	  
  
var map = L.map('map', {
    center: new L.LatLng(41.383, 2.171),
    zoom: 12,
    minzoom: 5,
    maxzoom: 12,
    layers: [grey]
});


var baseMaps = {"Graphite": grey, "Dark Blue": blue, "Light-color": light,"Light-Minimal":minimal,"Red": red};
L.control.layers(baseMaps,null,{position : 'topleft'}).addTo(map);

/*
L.tileLayer('http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png', {
	maxZoom: 18,
	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>'
}).addTo(map);

*/
// dark 		aq9bz8ek
//map graphite  oxzm78wl
//map bleu    9a66pcvi




//layer for holding future markers
var markersLayer = L.layerGroup().addTo(map);





/*------ LOADING DATA ON MAP------*/

function loadMarkers(){

	tag = "temperature"; //default = temp
	unit = "celcius";
	var query="lat="+map.getCenter().lat+"&lon="+map.getCenter().lng+"&distance="+40000/Math.pow(2,map.getZoom())*3+"&tag="+tag+"&status=live";
	console.log("Loading new datas");

	//----- Collecting new data
	$.ajax({
		url: "http://api.cosm.com/v2/feeds.json?"+query,
		headers: {'X-ApiKey': "v3anu3yj1aadeAP-S5c7lXJEiqCSAKxBQ0J3K2F1RFFkOD0g"},
		dataType: "json" //run a $.parseJSON function to output preformated data
	}).done(function(data) { 
//		console.log(data);
		//----- Cleaning vars
		var count = 0;
		var median = 0;
		markersLayer.clearLayers();
		$('#live_data').text('');
		//----- filtrating feeds
		$.each(data.results, function(key, feed) {
			if(feed.datastreams && feed.location.lat && typeof feed.location.lon != 'undefined'){
				//----- searching the good datastreams
				var value=0;
				var text='<table style="border:0;"><tr><td></td><td style="width:10px"></td><td></td></tr>';
				$.each(feed.datastreams, function(key, datastream) {
					if($.inArray(tag,datastream.tags)){
						value=datastream.current_value;
					}
					text+='<tr><td>';
					if(datastream.tags)
						text+=datastream.tags.join(' ');
					else
						text+=datastream.id;
					text+=' : </td><td></td><td style="text-align:right; color:#dd7711;">'+datastream.current_value+' ';
					if(datastream.unit && datastream.unit.symbol)
						text+=datastream.unit.symbol;
					text+='</td></tr>';
				});
				text+='</table>';
				if(value>100)
					value/=100;
				median+=Number(value);
				//-----  generating the marker
				var marker = L.circleMarker([feed.location.lat, feed.location.lon], {
					color: getScaledColour(value, 50),
//					color: '#dd7711',
					weight: 10,
					opacity: .5,
					fillColor: '#dd7711',
					fillOpacity: .9
				});
				marker.setRadius(10);
				marker.bindPopup(
				'<h4>'+feed.title+'</h4><br/>'
				+ text
				+ feed.location.exposure +'<br/><i> by :' + feed.creator.substr(23)  + '</i>');
//				marker.bindPopup("<a href='#' onClick=\"getPage('/feeds/view/" + feed.id +"')\" > <h4>"+feed.title+"</h4><br/>"+value+"°C "+ feed.location.exposure +"<br/><i> by :" + feed.creator.substr(23)  + "</i></a>");
				marker.on('click', function(e) { getPage("/feeds/view/" + feed.id) });
				
//				marker.on('mouseout', function(e) { this.openPopup() });
//				marker.on('mouseover', function(e) { map.closePopup() });

				markersLayer.addLayer(marker);
				count++;
				
			}
		})
		median/=count;
		//ajaxify (markersLayer);
		//$('#live_data').append("<b class='fatDot' style='background-color:"+getScaledColour(median, 50)+";border-color:"+getScaledColour(median*1.2, 50)+"'> </b>"+median.toFixed(2) + "°C average temperature <br/>");
		//$('#live_data').append(count + " live sensors displayed on a total of "+data.totalResults+" scaned"); // show what we got back

	});

}

// - Init
loadMarkers();



/*------ MAP ITERACTION -----*/
//Actualise markers
//map.on("moveend",loadMarkers);





/*----- LIbrary -----*/


function ColorLuminance(hex, lum) {  
	// validate hex string  
	hex = String(hex).replace(/[^0-9a-f]/gi, '');  
	if (hex.length < 6) {  
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];  
	}  
	lum = lum || 0;  
	// convert to decimal and change luminosity  
	var rgb = "#", c, i;  
	for (i = 0; i < 3; i++) {  
		c = parseInt(hex.substr(i*2,2), 16);  
		c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);  
		rgb += ("00"+c).substr(c.length); 
	}  
	return rgb;  
}  

function getScaledColour(index, maximum) {
 
  // determine starting colour components
  var redHex   = "dd";
  var greenHex = null;
  var blueHex  = "11";
 
  // define values for formula
  var startGreen = 150;
  var endGreen   = 50;
 
  // calculate the green value
  var greenVal = startGreen + ((endGreen - startGreen) * (index / (maximum -1)));
 
  // round the green value to an integer
  greenVal = Math.round(greenVal);
 
  // convert from decimal to hexadecimal
  greenHex = greenVal.toString(16);
 
  // pad the hexadecimal number if required
  if(greenHex.length < 2) {
    greenHex = "0" + greenHex;
  }
 
  // return the final colour
  return "#" + redHex + greenHex + blueHex;
}
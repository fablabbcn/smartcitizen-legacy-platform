// cosmGraph
// version 1.0.0a
// (c) 2012 pete correia [contact@petecorreia.com]
// https://github.com/petecorreia/cosm-graph
// released under the MIT license

(function( window, document, version, callback ) {
  //"use strict";
 // log('pouet');
  /* ----------------------------------------------------------------
  *
  *  LOAD JQUERY 1.7, if necessary
  *
  */
  
  var j, d;
  var loaded = false;
  if (!(j = window.jQuery) || version > j.fn.jquery || callback(j, loaded)) {
    var script = document.createElement("script");
    script.onload = script.onreadystatechange = function() {
      if (!loaded && (!(d = this.readyState) || d == "loaded" || d == "complete")) {
        callback((j = window.jQuery).noConflict(), loaded = true);
        j(script).remove();
      }
    };
    script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js";
    document.getElementsByTagName("body")[0].appendChild(script);
  }
  else {
    callback( window.jQuery, true );
  }
})( window, document, "1.7", function($, jquery_loaded ) {
  "use strict";
  
  /* ----------------------------------------------------------------
  *
  *  Date() ISO 8601 polyfill
  *
  */

  if ( !Date.prototype.toISOString ) {
    Date.prototype.toISOString = function () {
      function pad(n) { return n < 10 ? '0' + n : n }
      return this.getUTCFullYear() + '-'
        + pad(this.getUTCMonth() + 1) + '-'
        + pad(this.getUTCDate()) + 'T'
        + pad(this.getUTCHours()) + ':'
        + pad(this.getUTCMinutes()) + ':'
        + pad(this.getUTCSeconds()) + 'Z';
    };
  }  
  
  /* ----------------------------------------------------------------
  *
  *  LOAD NECESSARY DEPENDENCIES, if necessary
  *
  */
  
  var hasR    = window.Raphael,
      hasM    = window.Morris,
      hasC    = window.cosm;
      
  (function ( window, callback ){
    
    var d,
        bundle  = '',
        loaded = false,
        script = document.createElement("script");
        
    // Which bundle?
    if      ( !hasR && !hasM && !hasC ) { bundle = "scripts/bundle-r-m-c.js"; } // load all
    else if ( hasR  && !hasM && !hasC ) { bundle = "scripts/bundle-m-c.js";   } // load morris and cosmjs
    else if ( !hasR && !hasM && hasC  ) { bundle = "scripts/bundle-r-m.js";   } // load raphael and morris
    else if ( hasR  && hasM  && !hasC ) { bundle = "scripts/cosmjs.js";       } // load cosmjs
    else if ( hasR  && !hasM && hasC  ) { bundle = "scripts/morris.js";       } // load morris
     
    // load it   
    script.onload = script.onreadystatechange = function() {
      if (!loaded && (!(d = this.readyState) || d == "loaded" || d == "complete")) {
        callback( window );
        $(script).remove();
      }
    };
    script.src = bundle;
    document.getElementsByTagName("body")[0].appendChild(script);
    
  })( window, function( window ) {
  
    /* ----------------------------------------------------------------
    *
    *  DEFINITION
    *
    */

    // EXTEND cosm namespace
    
    cosm.graph = function ( target, opt ) {
      "use strict";
      
      // PRIVATE vars
      
      // DATE helpers
      var now         = new Date(),
          lastWeek    = (function () {
            var d = new Date();
            return new Date( d.setDate( d.getDate() - 5 ) ).getTime();
          })(),
          localDate   = function ( date ) {
            var d = date.getTime ? date : new Date( date );
            return new Date( d.getTime() - (d.getTimezoneOffset() * 60000) ).toISOString();
          },
          
          // OPTIONS
          
          options     = $.extend( {
            request : {
              end       : new Date().toISOString(),
              duration  : "60minutes",
              interval  : 60
            }
          }, opt ),
          
          // GRAPH helpers
          
          history     = {},
          graph,
          $target     = $( target );
      
      
      // PROCESS resource
      
      options.resource  = (function () {
        var data = {
              full : options.resource
            };
            
        if ( data.full && data.full !== "" ) {
          data.feed = data.full.replace( /^.*?feeds\//, "" ).replace( /\/.*$/, "" );
          
          if ( data.full.indexOf( 'datastreams' ) != -1 ) {
            data.datastream = data.full.replace( /^.*?datastreams\//, "" ).replace( /\/.*$/, "" );
          }
        }
        
        return data;
      })();
      
      
      // SET dimensions
      
      options.width   && $target.css( 'width', options.width );
      options.height  && $target.css( 'height', options.height );
      
      
      // CHECK for required cosm info
      
      if ( options.resource.feed && options.resource.datastream && options.key ) {
      
        // SET API key
      
        cosm.setKey( options.key );
        
        // GET DATAPOINTS
        
        cosm.datastream.history( 
          options.resource.feed, 
          options.resource.datastream, 
          options.request, 
          
          // HANDLE data
          
          function ( datastream ) {
			//Temporary hack for ajax errors.
/*			var datastream = {
				"version":"1.0.0",
				"datapoints":[
					{"value":"0","at":"2012-09-24T18:27:59.393643Z"},
					{"value":"0","at":"2012-09-24T18:32:59.393643Z"}
				],
				"max_value":"100.0",
				"at":"2012-09-24T19:27:40.169931Z",
				"min_value":"0.0",
				"tags":[
					""
				  ],
				"id":"",
				"unit":"",
				"current_value":"0"};
			$.extend(datastream, datastreamRecup);
*/
			console.debug(datastream);
            var i = datastream.datapoints.length;
            
            // SAVE datapoints
            
            history = datastream.datapoints;
            
            // REMOVE time part from datapoints date 8601 because Morris can't handle them
                
            while ( i-- ) {
              history[i].at = history[i].at.replace(/T/," ").replace(/\..*$/, "");
            }
            
            // CREATE GRAPH
            
            graph = new Morris.Line({
              element           : options.id,
              data              : history,
              xkey              : 'at',
              ykeys             : [ 'value' ],
              ymax              : 'auto',
              ymin              : 'auto',
              postUnits         : datastream.unit.symbol,
              labels            : [ datastream.id ],
              lineColors        : [ '#808080' ],
              pointGrowColor    : "#069dbc",
              pointSize         : 1,
              gridStrokeWidth   : 0.3,
              gridLineColor     : '#ccc',
              gridTextColor     : '#888',
              gridTextSize      : 11,
              marginTop         : 25,
              marginRight       : 15,
              marginBottom      : 30,
              marginLeft        : 15,
              hoverPaddingX     : 13,
              hoverPaddingY     : 8,
              hoverBorderWidth  : 3,
              hoverBorderColor  : '#069dbc',
              hoverOpacity      : 1,
              hoverMargin       : 10,
              hoverFillColor    : '#069dbc',
              hoverFontSize     : 11,
              hoverXFontSize    : 20,
              hoverLabelColor   : '#fff',
              hoverValueColor   : '#fff',
              smooth            : true,
              hideHover         : true,
              xLabels           : "day"
            });
            
            // ADD initialised control class to target element
            
            $target.addClass( "cosm-initialised" );
        });
        
        
        // SUBSCRIBE to realtime updates
        //cosm.datastream.subscribe(
        cosm.datastream.get( 
          options.resource.feed, 
          options.resource.datastream, 
          
          // HANDLE data
          
          function ( event, datastream ) {
            var new_value = {
                  "at"    : datastream.at.replace(/T/," ").replace(/\..*$/, ""),
                  "value" : datastream.current_value
                },
                newVal    = parseFloat(new_value.value);
                
            // ADD point to datapoint history
            
            history.push( new_value );
            
            if ( $target.hasClass("cosm-initialised") ) {
                
              // REWORK graph scale, if necessary
              
              if ( graph.options.ymin > newVal ) {
                graph.options.ymin = newVal;
              }
              else if ( graph.options.ymax < newVal ) {
                graph.options.ymax = newVal;
              }
                
              // UDPATE graph values with new point
              
              graph.setData( history, true );
            }
        });
         
      }
  
      // PUBLIC VARS & METHODS
      
      return {
        
        graph       : graph,
        
        datapoints  : history,
        
        resource    : options.resource
  
      };
    
    };
    
  
    /* ----------------------------------------------------------------
    *
    *  INSTANTIATE
    *
    */
    
    // GET ALL SCRIPT tags that aren't loaded
    
    var scriptName  = "cosm-graph-1.0.0",
        $scripts    = $( 'script[src*="'+ scriptName +'"]:not([data-loaded])' ),
        counter = 0;
          
    // LOOP ALL INSTANCES
    
    if ( $scripts.length ) {
    
      $scripts.each( function ( index, el ) {
        var $el     = $( el ),
            options = $el.data(),
            $target,
            graph;
      
        if ( options.id ) {
          options.id = options.id.replace(/^#/,"");
          $target = $( "#"+ options.id );
        }
        else {
          options.id = "cosm-graph-"+ Math.floor(Math.random()*10001) + "-" + counter++;
          $target = $("<div/>", { "id": options.id, "class": "cosm-graph" });
          $target.insertAfter( $el );
        }
        
        $el
          .attr('data-loaded', 'true')
          .remove();
        
        graph = new cosm.graph( $target, options );
      });
    
    }

  });
  
  
});
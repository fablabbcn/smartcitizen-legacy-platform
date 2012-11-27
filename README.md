smartcitizen.me
===============
[![SmartCitizen](http://beta.smartcitizen.me/img/logo_200x200.png)](http://beta.smartcitizen.me)

Currently : Beta Release, available on http://beta.smartcitizen.me

## Purpose

Website for the Smart Citizen Project : A software to connect users and hardware, mainly using the Smart Citzen Kit (Arduino Sesnsor Kit, ASK).


## Scripting language and File organisation

Server side script are written in php and hosted in our server. Their organisation follow a MVC (Model–View–Controller) architecture.
Client side script are written in javascript exchanging data in json via ajax queries. Alternatively, the webpage can be displayed without javascript (but with no animation, no map, no live feeds and more downloading time)
Visualisation are using HTML5, CSS3 and SVG (Raphael) for optimal display on computer browsers. First version will be compliant with mobile browser for a possible (but not optimised) access. We avoid Flash technology for this purpose. The website is build on HTML5 Boilerplate template for fully cross-browser compatibility.


## Data organisation
Data are stored partly on third party server, partly on our custom server, depending on the purpose :

Sensors datas are stored on COSM. The kit upload data continuesly on Cosm server while we retrieve and aggregate them live on client side for each preview. A possibile optimisation for page to display faster will be to store aggregated data on our server in order to serve pre-calculated data for public display (calculation of thousands of live data each minutes on each client computer is a repeated task that could be centralised...)

Maps base come from openstreetdata but need a server to host the image vistors download (possibly with custom style). Several service are available worlwide and for now  we choose an open-source sollution : mapbox.com
images are then are retrieved and displayed thanks to Leaflet map framework

Users data are stored on our server and connected to cosm, facebook and twitter Api trhough Oauth method. (OAuth is a method for allowing third-party application access to your resources without giving them access to your username and password.)


## API and Library used

In order to speed up the development and reduce the web platform cost, we made a large use of public library and third party API. In counterpart the platform have a great dependency to their limit (no full control over data), availability (up-time, only for API), stability (difficult debuging) and evolution (release of new version retro-compatible).

Open source API services :
- Cosm for arduino connection and storing sensors datas. (+ networking ?)
- Gisgraphy for geocoding (not used yet)
- CartoDB for other geo-localized data storage (not used yet)
- WorldWideLexicon for text translation (not used yet)

Proprietary API services (used for their popularity, not found in open-source project)
- Facebook and twitter for social networking
- MapBox as a customised map provider (payant solution)

Javascript library :
- Leaflet for the map rendering and interaction.(similar to google maps but open-source and mobile compliant)
- jQuery a javascript framework for reducing code developpment (“write less, do more” slogan) + support for cross-browser.
- Raphael and gRaphael for vector drawings over leaflet's map and for enhanced charting.

Php library / framwork
- CakePhp for the MVC framework
- Piwik for visitors analytics (similar to google analytics but open-source)

html 5 / CSS5
- HTML5 Boilerplate + modernizer for Enabling html5/css3 on older browser while being respecfull of W3C standards.


## Contact 

alex [at] fablabbcn.org

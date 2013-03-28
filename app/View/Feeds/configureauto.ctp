









<section style='width:410px'>
	<header>
		<div class='action'>
			<?php 
					echo $this->Html->link('< '.__('Back to sensor'), array('action' => 'view', $data['Feed']['id']), array('class'=>'button light')); 
			?>
		</div>
		<h1>Configuration of the Sensor</h1>

	</header>
	<article>
			<p>Automatic system using a java applet. Be sure to authorize the Java plugin and have your board connected to your computer through USB</p>
			
			<div id="scanning">
				<button id="send">Send Serial</button>
				<select id="ports"></select>
				<span id="loader">Loading...</span>
			</div>

            <applet width="1" height="1" id="sckapplet" name="Smart Citizen - SCK config tool" code="eu.amaxilatis.codebender.CodeBenderApplet" archive="./sck_loader.jar"></applet>

		<footer><p>Powered by the <a href="https://github.com/codebendercc/ardoSerial">ardoSerial</a> moduleby <a href="http://codebender.cc/">Code Bender</a>.</footer>
	</article>
<section>


<script type="text/javascript"><?php $this->start('inline-script');?>

	var configFile = "{message: 'hello world!'}" // The data do be send to the arduino. Any string.


	function getIds() {
		window.applet = $("#sckapplet")[0];
		window.portslist = $("#ports")[0];
		window.rateslist = $("#baudrates")[0];
		window.oldPorts = "";
	}

	/* Triggered at launch. Sets the UI and triggers the Scan(); function every 5sec. */

	function launch() { 
	
		if (typeof applet.isActive === "function") {

			$("#scanning").toggle(1000);

			$("#send").off('click').click(function() {
				sendAuto(configFile);
				$("#loader").hide();
			}).mousedown(function() {
		alert('test');
				$("#loader").show();
			});
			
			scan();

			setInterval(function () {
				scan();
			}, 5000);

		} else {

			setTimeout(function () {
				launch();
			}, 5000);

		}

	}

	/* Connects to the selected PORT send the data passed to it and closes the serial. No cheking done, must be implemented */


	function sendAuto(data) {
		try {
			open();
			send(data);
			setTimeout(close(), 3000);
		} catch(e) {
			console.warn(e)
			alert("Upload failed! Try it again, please.");
		}
	}

	/* Connects to the selected PORT */

	function open() {
		if ($("#ports").val() != null && $("#ports").val() != "") {
			
			var port = portslist.selectedIndex;
			var rate = 9600;

			applet.openSerial(port, rate);

		} else {
			alert("Please, select a valid port or enable the Java Applet!");
		}
	}

	/* Closes to the selected PORT */


	function close() {
			applet.closeSerial();
	}

	/* Sends STRING to arduino with adding a line break '\n' at the end. */

	function send(data) {
		if ($("#ports").val() != null && $("#ports").val() != "" && data) {
			applet.writeSerial(data);
		} else {
			 alert("Please, select a valid port or enable the Java Applet!");
		}
	}

	/* Scans for serial ports and fills the options list */

	function scan() {
		var ports = applet.probeUsb();
		if (ports != oldPorts) {
			$('#ports').find('option').remove();
			portsAvail = ports.split(",");
			
			console.log(portsAvail);
			for (var i = 0; i < portsAvail.length; i++) {
				portslist.options[i] = new Option(portsAvail[i], portsAvail[i], true, false);
			}
			oldPorts = ports;
		}
	}


	window.onload = function () {
		getIds();
		launch();
	};
	$(document).ready(function () {
		$("#progress").hide();
		$("#scanning").hide();
		$("#loader").hide();
	});
<?php $this->end();?></script>
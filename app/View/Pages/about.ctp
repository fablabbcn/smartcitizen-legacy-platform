<?php
/**
 *
 * @copyright     
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

?>
<section style='width:500px;'>
	<header>
		<h1>Smart Citizen - Sensores ciudadanos</h1>
    </header>
	<article>
		<p>
			What are the real  levels of air pollution around your home or business? and what about noise pollution? and humidity?
		</p><p>
			Now imagine that you could know them, share instantly and compare with other places in your  city, in real time ... How could this information help to improve our environment quality?
		</p><p>
			From Smart Citizen we want to answer to these questions and many more, through the development of low-cost sensors. Now you can be one of these sensors in a network by supporting this project. But we will not stop here ... How can we built a real Smart City...by Smart Citizens?
		</p>
		<h3>What ?</h3>
		<p>
			Smart Citizen is a platform to generate participatory processes of people in the cities. Connecting data, people and knowledge, the objective of the platform is to serve as a node for building productive and open indicators, and distributed tools, and thereafter the collective construction of the city for its own inhabitants.
		</p><p>
			The Smart Citizen project is based on geolocation, Internet and free hardware and software for data collection and sharing, and (in a second phase) the production of objects; it connects people with their environment and their city to create more effective and optimized relationships between resources, technology, communities, services and events in the urban environment. Currently it is being deployed as initial phase in Barcelona city.
		</p>
		<h3>Who ?</h3>
		<p>
			The project is born within <a href='http://fablabbcn.org'>Fab Lab Barcelona</a> at the <a href='http://www.iaac.net'>Institute for Advanced Architecture of Catalonia </a>, both focused centers on the impact of new technologies at different scales of human habitat, from the bits to geography. It is developed in collaboration with <a href="http://www.hangar.org"> Hangar </a>.
		</p>
		<p>
			This project get founded thanks to lovely bakers on <a href='http://goteo.org/project/smart-citizen-sensores-ciudadanos/'>Goteo</a>
		</p>
		<p> The web platform have been developped thanks to 
				<a href='htpp://cosm.com/'>Cosm</a>,
				<a href='htpp://openstreetmap.org/'>OpenStreetMap</a>,
				<a href='http://leaflet.cloudmade.com/'>Leaflet</a>,
				<a href='http://raphaeljs.com/'>Raphaël</a>,
				<a href='http://jquery.com/'>jQuery</a>,
				<a href='http://cakephp.org/'>CakePHP</a>, and many more... The actual project is open source and available on <a href='https://github.com/fablabbcn/SmartCitizen.me'>github</a>.
		</p>
	</article>
	<footer>
	</footer>
</section>

<section>
	<header>
		<h2>Folow us :</h2>
	</header>
	<article>
		<a href='http://smartcitizen.me/posts.rss'>Rss</a><br/>
		<a href='https://www.facebook.com/smartcitizenBCN'>Facebook</a><br/>
		<a href='https://twitter.com/fablabbcn'>Twitter</a><br/>
		<a href='https://github.com/fablabbcn/'>Github</a><br/>
	</article>
<section>

<section>
	<header>
		<h2>Contact us</h2>
	</header>
	<article>
		<?php
			echo $this->Form->create('Contactform.Contactform');

			echo $this->Form->input('Contactform.Name', array('label' => __d('contactform', 'name')));
			echo $this->Form->input('Contactform.Mail', array('label' => __d('contactform', 'email')));
			echo $this->Form->input('Contactform.Message', array('type' => 'textarea', 'label' => __d('contactform', 'message')));

			echo $this->Form->submit(__('Send'), array('label' => __d('contactform', 'submit')));

			echo $this->Form->end();
		?>
	</article>
<section>
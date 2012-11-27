<div style='width:350px'>
	<section>
		<header>
			<h1>Develop the comunity</h1>
		</header>
		<article>
			<h4>Spread the word around you</h4>
			<p>
				This project is possible thanks to thousand of people like you. The more we are, the smarter we can act. So share it !
			</p>
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
				<a class="addthis_button_preferred_1"></a>
				<a class="addthis_button_preferred_2"></a>
				<a class="addthis_button_preferred_3"></a>
				<a class="addthis_button_preferred_4"></a>
				<a class="addthis_button_compact"></a>
				<a class="addthis_counter addthis_bubble_style"></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50689230539ba640"></script>
			<!-- AddThis Button END -->
		</article>
	</section>
	<section class='links list'>
		<header>
			<h2>Folow us</h2>
		</header>
		<a href='https://www.facebook.com/smartcitizenBCN'>
			<article class='list-item link'>
				<h4>Facebook</h4>
				https://www.facebook.com/smartcitizenBCN
			</article>
		</a>
		<a href='https://www.facebook.com/smartcitizenBCN'>
			<article class='list-item link'>
				<h4>Twitter</h4>
				https://twitter.com/fablabbcn
			</article>
		</a>
		<a href='http://smartcitizen.me/posts.rss'>
			<article class='list-item link'>
				<h4>Rss Feed</h4>
				http://smartcitizen.me/posts.rss
			</article>
		</a>
	</section>
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
	<section>
		<header>
			<h2>Come visit us</h2>
		</header>
		<article>
		<p>
			Fab Lab Bcn, Institut d´arquitectura avançada de Catalunya<br/>
			C/Pujades 102 baixos. Poble Nou<br/>
			Barcelona 08005 España<br/>
			Tel: (+34) 93 3209520<br/>
			Fax: (+34) 93 3004333<br/>
			<a href='http://www.fablabbcn.org'>www.fablabbcn.org</a>
		</p>
		</article>
	</section>
</div>
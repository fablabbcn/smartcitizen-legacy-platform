<h1>Kontaktformular</h1>
<?php
 echo $this->Html->css(array(
    '/Contactform/css/Contactform.css'
));
echo $this->Form->create('Contactform.Contactform');

echo $this->Form->input('Contactform.Name', array('label' => __d('contactform', 'name')));
echo $this->Form->input('Contactform.Mail', array('label' => __d('contactform', 'email')));
echo $this->Form->input('Contactform.Message', array('type' => 'textarea', 'label' => __d('contactform', 'message')));

echo $this->Form->submit('Absenden', array('label' => __d('contactform', 'submit')));

echo $this->Form->end();
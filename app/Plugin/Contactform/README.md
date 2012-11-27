#Contact form for CakePHP 2

##features
* multi language
* no tables needed
* custom routes
* validation

## installation
* clone github repository into your CakePHP Application plugin folder:

```bash
git clone https://github.com/patrickhafner/ContactForm-CakePHP-2.git Plugin/Contactform
```

* add following code into your Config/email.php and configure:

```php
public $contactform = array(
	    'transport' => 'Mail',
	    'from' => array('mail@example.com' => 'example.com | contact form'),
	    'bcc' => 'yourmail@example.com',
	    'charset' => 'utf-8',
	    'headerCharset' => 'utf-8',
	);
```

* if you'd like to use an existing email configuration, please change Controller/ContactformController.php:

```php
$email->config('YOURCONFIG_IN_EMAIL_PHP');
```

* add following code in {APP_DIR}/Config/bootstrap.php

```php
CakePlugin::load('Contactform', array('routes' => true));
```

or use this code to load all plugins, located in your application
```php
CakePlugin::loadAll(); // Loads all plugins at once
```

* test contact form with following url: **http://yourapp.example.com/contact**
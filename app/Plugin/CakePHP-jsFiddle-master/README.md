# CakePHP JsFiddle Api Plugin
Feel free to refer to the [JsFiddle Documentation](http://doc.jsfiddle.net/api/index.html)

## Installation

1. Clone or download to `Plugin/Jsfiddle`

3. Add your configuration to `database.php` and set it to the model

```
:: database.php ::
var $jsfiddle = array(
	'datasource' => 'Jsfiddle.Jsfiddle'
);

:: my_model.php ::
var $useDbConfig = 'Jsfiddle';
```

## Commands

### Read: 
Use `find('all', $params)` for all read commands.
Pass the `fields` param as a string only, do not pass an array of fields.

#### List of fiddles
http://doc.jsfiddle.net/api/fiddles.html

**Field:** fiddles

**Conditions:**

* **user:** required
* callback: function name for the Xdomain (default: None). if no callback provided standard JSON will be returned
* start: offset element (default: 0)
* limit: number of elements to return (default: 10)
* sort: sorting type - date, alphabetical or framework (default: ‘date’)
* order: desc or asc (default: ‘asc’)
* framework: filter framework (default: None)
		
**Example:**

```
$data = $this->Model->find('all', array(
	'conditions' => array(
		'user' => 'proloser'
	),
	'fields' => 'fiddles',
));
```

#### Get Current Logged-In Username
http://doc.jsfiddle.net/api/get_username.html

**Field:** user

**Conditions:**
none
		
**Example:**

```
$data = $this->Model->find('all', array(
	'fields' => 'user',
));
```


### Create
Bold items are required

**Create Fiddle**
http://doc.jsfiddle.net/api/post.html

Fields:

* **framework** => the desired framework name. Which framework should be loaded with the fiddle (vanilla for plain JavaScript)
* **version** => substring of the framework version - the last passing will be used. If 1.3 will be given, jsFiddle will use the latest search result. it will favorize 1.3.2 over 1.3.1 and 1.3
* dependencies => comma separated list of dependency substrings. It would mark any dependency containing the substring.
* html, js, css => code for specific panels
* resources => a comma separated list of external resources
* title => title of the fiddle
* description => description of the fiddle
* normalize_css => yes or no - should normalize.css be loaded before any CSS declarations?
* dtd => substring of the chosen DTD (i.e. "html 4")

**Example:**
[http://jsfiddle.net/zalun/sthmj/embedded/result/](http://jsfiddle.net/zalun/sthmj/embedded/result/)
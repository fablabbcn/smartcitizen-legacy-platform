# NOTE:

When you register your app with Twitter, make sure you create create a placeholder callback in your app settings, or you will be locked to OOB auth. The callback can be anything, it's simply a placeholder.

# Installation

### Step 1: Download / clone the following plugins: 

 * **LinkedIn** to _Plugin/Twitter_
 * [HttpSocketOauth plugin](https://github.com/ProLoser/http_socket_oauth) (ProLoser fork) to _Plugin/HttpSocketOauth_
 * [Apis plugin](https://github.com/ProLoser/CakePHP-Api-Datasources) to _Plugin/Apis_

### Step 2: Setup your `database.php`

```
var $twitter = array(
	'datasource' => 'Twitter.Twitter',
	'login' => '<twitter api key>',
	'password' => '<twitter api secret>',
);
```

### Step 3: Install the Apis-OAuth Component for authentication

```
MyController extends AppController {
	var $components = array(
		'Apis.Oauth' => 'twitter',
	);
	
	function connect() {
		$this->Oauth->connect();
	}
	
	function twitter_callback() {
		$this->Oauth->callback();
	}
}
```

### Step 4: Use the datasource normally 
Check the [config file](https://github.com/ProLoser/CakePHP-Twitter/blob/master/Config/Twitter.php) for a list of available 'sections' and the parameters they take

```
Class MyModel extends AppModel {
	
	public function afterSave($created) {
		if ($created) {
			// Auto Tweet
			$this->setDataSource('twitter');
			$data = array(
				'section' => 'tweets',
				'status' => $this->data['Bookmark']['name'] . ' ' . $this->data['Bookmark']['url']
			);
			$this->create();
			$this->save($data);
			$this->setDataSource('default');
		}
	}
}
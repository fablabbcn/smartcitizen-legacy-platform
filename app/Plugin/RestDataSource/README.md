# Installation

## app/Config/bootstap.php

```php
<?php
CakePlugin::load('Rest');
?>
```

## Database config

```php
<?php
class DATABASE_CONFIG {

        public $sample = array(
                'datasource'            => 'Rest.RestSource',
                'host'                  => 'http://api.example.com',
                'format'                => 'json',
                'encoding'              => 'utf-8',
        );

}
?>
```

# Examlpes

## Cake inflection REST endpoint

```php
<?php
class User extends AppModel {

        public $useDbConfig = 'sample';

        public $remoteResource = 'users';

}
?>
```

```
User::find('all')               == GET    http://api.example.com/users.json
User::read(null, $id)           == GET    http://api.example.com/users/$id.json
User::save()                    == POST   http://api.example.com/users.json
User::save(array('id' => $id))  == PUT    http://api.example.com/users/$id.json
User::delete($id)               == DELETE http://api.example.com/users/$id.json
```

More to come later
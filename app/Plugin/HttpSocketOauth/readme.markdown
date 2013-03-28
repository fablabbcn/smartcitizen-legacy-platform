Usage instructions (we'll take twitter as an example):

1. Grab the code from my <a href="http://github.com/neilcrookes/http_socket_oauth">github account</a> and add it to app/vendors/http_socket_oauth.php

2. <a href="http://twitter.com/oauth_clients">Register your application with Twitter</a> (See below if you are developing locally and twitter grumbles about your call back url containing 'localhost')

3. Note the consumer key and secret (I add them to Configure in bootstrap)

4. Add the following to a controller:

        public function twitter_connect() {
          // Get a request token from twitter
          App::import('Lib', 'HttpSocketOauth.HttpSocketOauth');
          $Http = new HttpSocketOauth();
          $request = array(
            'uri' => array(
              'host' => 'api.twitter.com',
              'path' => '/oauth/request_token',
            ),
            'method' => 'GET',
            'auth' => array(
              'method' => 'OAuth',
              'oauth_callback' => '<enter your callback url here>',
              'oauth_consumer_key' => Configure::read('Twitter.consumer_key'),
              'oauth_consumer_secret' => Configure::read('Twitter.consumer_secret'),
            ),
          );
          $response = $Http->request($request);
          // Redirect user to twitter to authorize  my application
          parse_str($response, $response);
          $this->redirect('http://api.twitter.com/oauth/authorize?oauth_token=' . $response['oauth_token']);
        }

    ... replacing <enter your callback url here> with your callback url, i.e. the URL of the page in your application that twitter will redirect the user back to, after they have authorised your application to access their account. In this example, it's the url of the action in the next step. (Note, when you register your app, if you are developing locally, and you tried to enter your callback url with localhost in it, twitter might grumble. A little gem I read somewhere said you can actually create a bit.ly link, add your local callback URL in there, and then add the bit.ly link as the call back url in the twitter application settings. I still add my localhost url in this place though).

    This action fetches a request token from twitter, which it then adds as a query string param to the authorize URL on twitter.com that the user is redirected that prompts them to authorise your app.

5. Next add the action for the call back:

        public function twitter_callback() {
          App::import('Lib', 'HttpSocketOauth.HttpSocketOauth');
          $Http = new HttpSocketOauth();
          // Issue request for access token
          $request = array(
            'uri' => array(
              'host' => 'api.twitter.com',
              'path' => '/oauth/access_token',
            ),
            'method' => 'POST',
            'auth' => array(
              'method' => 'OAuth',
              'oauth_consumer_key' => Configure::read('Twitter.consumer_key'),
              'oauth_consumer_secret' => Configure::read('Twitter.consumer_secret'),
              'oauth_token' => $this->params['url']['oauth_token'],
              'oauth_verifier' => $this->params['url']['oauth_verifier'],
            ),
          );
          $response = $Http->request($request);
          parse_str($response, $response);
          // Save data in $response to database or session as it contains the access token and access token secret that you'll need later to interact with the twitter API
          $this->Session->write('Twitter', $response);
        }

    After the user authorises your app, twitter redirects them back to this action, the callback you specified in the previous request. In the querystring are 2 params called 'oauth_token' and 'oauth_verifier'. These, and the consumer key and secret are then sent back to twitter, this time requesting an access token.

    At the end of this action, $response contains an associative array with keys for: 'oauth\_token', 'oauth\_token\_secret', 'user\_id', 'screen\_name'. You should save 'oauth\_token' and 'oauth\_token\_secret' to the session or the database as you need them when you want to access the Twitter API. Then redirect the user to another action, or display a thanks message or tweet to their account or whatever.

Now if you link to the twitter_connect() action or hit it in your browser address bar, you should be directed off to twitter to authorise your application, and once done, be back within your app with some twitter accounts access tokens.

Finally, I guess it's useful to know how to do something with the twitter API with this new found power:

          App::import('Lib', 'HttpSocketOauth.HttpSocketOauth');
          $Http = new HttpSocketOauth();
          // Tweet "Hello world!" to the twitter account we connected earlier
          $request = array(
            'method' => 'POST',
            'uri' => array(
              'host' => 'api.twitter.com',
              'path' => '1/statuses/update.json',
            ),
            'auth' => array(
              'method' => 'OAuth',
              'oauth_token' => $oauthToken, // From the $response['oauth_token'] above
              'oauth_token_secret' => $oauthTokenSecret, // From the $response['oauth_token_secret'] above
              'oauth_consumer_key' => Configure::read('Twitter.consumer_key'),
              'oauth_consumer_secret' => Configure::read('Twitter.consumer_secret'),
            ),
            'body' => array(
              'status' => 'Hello world!',
            ),
          );
          $response = $Http->request($request);


Hope you like it. Any issues, please leave on github issue tracker. Any comments, let me know on my <a href="http://www.neilcrookes.com/2010/04/12/cakephp-oauth-extension-to-httpsocket/">blog</a>. Thanks.
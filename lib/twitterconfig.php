<?php

/**
 * @file
 * A single location to store configuration.
 */

define('CONSUMER_KEY', getenv('TWITTER_CONSUMER_KEY'));
define('CONSUMER_SECRET', getenv('TWITTER_CONSUMER_SECRET'));
define('OAUTH_CALLBACK', 'http://democracylab.herokuapp.com/loginviatwitter2.php');

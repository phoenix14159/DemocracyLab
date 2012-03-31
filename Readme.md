DemocracyLab app on Heroku
==========================

This is the [DemocracyLab](http://democracylab.org/) application. It's a PHP application
using various Facebook APIs running on Heroku. 

Source Code
-----------
The source code is both on Github and (because
of the way Heroku works) on Heroku. Set up your .git/config with
multiple remotes so that you can push and pull from both:

* push to Github: git push origin master
* pull from Github: git pull origin master
* deploy to Heroku: git push heroku master

The [Github issues system](https://github.com/bnfb/DemocracyLab/issues) is used to keep
track of the planned enhancements and any bugs.

Facebook Integration
--------------------
The [Facebook application](https://developers.facebook.com/apps/152864681481200) defines the 
appid and application secret: these are stored as environment variables in the Heroku application
and are picked up by AppInfo.php.

Currently the only Facebook integration is using the Facebook login.

Run Locally
-----------
To run the app locally, you must set up your local web server, your local database, and set up a Facebook
application to point to your local server. On my development Mac, I added this to /private/etc/apache2/httpd.conf:

    <VirtualHost local.democracylab.com:80>
        DocumentRoot /Users/bjorn/Sites/morning-ocean-5589/
        ServerName local.democracylab.com
        SetEnv FACEBOOK_APP_ID xxx
        SetEnv FACEBOOK_SECRET zzz
    </VirtualHost>

The local database is a PostgreSQL instance with the user and database name "postgres" and the
password stored in a local .database_password file. When new database migrations are created in
the lib/migrations.php file, they are applied by running "php lib/migrations.php".

I added "127.0.0.1 local.democracylab.com" to my /etc/hosts and set up a run-locally Facebook
application named "DemocracyLab-dev" whose Site URL and Site Domain is "http://local.democracylab.com/".
Then I can run the site locally as "http://local.democracylab.com/" complete with Facebook
integration.

Run on Heroku
-------------
Deploying to Heroku automatically runs the latest code as well. The one caveat is how I set up the 
database migrations: I suppose I could have used some framework to do that but instead I just 
add the migrations to the lib/migrations.php file. This means that after each git push to Heroku
for a deploy, you must use [https://democracylab.herokuapp.com/lib/migrations.php](https://democracylab.herokuapp.com/lib/migrations.php)
to run the migrations before the database will be consistent for the new code.



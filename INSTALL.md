
Installing the framework
==

There's no installing the framework. There's no application.

There is one requirement: a dedicated 'vhost'. The framework
will not work if it can't be reached on /. Why? Because every
app will have its own vhost and like this, the framework needs
no URL configuration. And that's gooood.

There is an example app
--

The example app use a database. Because it uses a database (and
all applications always will), part of the config is connecting
to a database and exit() if that connection fails.

Which means in order to actually see the example app, you will
have to import the attached MySQL database and change the
config in <u>config/database.php</u>.

The framework uses APC if available and doesn't if not avaiable.
No config required.

The framework uses PSR-0
(http://groups.google.com/group/php-standards/web/psr-0-final-proposal)
for file structure and it's very important that you keep that
structure! The example app is freely located anywhere.

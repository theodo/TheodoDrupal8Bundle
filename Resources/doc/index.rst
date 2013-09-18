TheodoDrupal8Bundle
==================

This ``TheodoDrupal8Bundle`` aims to build a bridge between Symfony2 and
Drupal 8, the new version of Drupal. It has been developed and widely
inspired from the ``EkinoDrupalBundle`` (http://github.com/ekino/EkinoDrupalBundle),
another open-source project created by Thomas Rabaix to enable the
integration of Drupal 7 in Symfony2.

Installation
------------

To set up a Drupal 8 application in a Symfony 2 project, please follow the
instructions below:

Step 1: Create a Symfony2 repository
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
First of all, you need a working Symfony2 project.
The easiest way is to follow the official documentation:
https://github.com/symfony/symfony-standard

basically run these commands:

    curl -s http://getcomposer.org/installer | php

    php composer.phar create-project symfony/framework-standard-edition path/to/install

Step 2: Include TheodoDrupal8Bundle
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
In your composer file, you need to require the ``TheodoDrupal8Bundle``. For that add the following lines to your ``composer.json``::

	"require": {
	    # other packages... ,
        "theodo/drupal8-bundle": "dev-master"
	}

At the time of writing this, you will also need to change the value of ``"minimum-stability"`` from ``"stable"`` to ``"dev"`` in your ``composer.json`` to be compatible with the current stability of Drupal8.

Then run ``composer update`` in your command shell to add all the necessary bundles to your project.

You will also need to add the declaration in your
``AppKernel.php`` file, like this::

	public function registerBundles()
    {
        $bundles = array(
            # other bundles... ,
            new Theodo\Bundle\Drupal8Bundle\TheodoDrupal8Bundle(),
        );

        return $bundles;
    }

Step 3: Configure Symfony2 to work
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Check http://symfony.com/doc/current/book/installation.html for the last steps of Symfony2's installation.

For Ubuntu, you will need to type the following commands to make the cache and logs directories writable:

	APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd' | grep -v root | head -1 | cut -d\  -f1`
	sudo setfacl -R -m u:$APACHEUSER:rwX -m u:`whoami`:rwX app/cache app/logs
	sudo setfacl -dR -m u:$APACHEUSER:rwX -m u:`whoami`:rwX app/cache app/logs


Step 4: Configure Drupal8 to work
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Autoloading has two conflicts today which can be resolved with a hack for the moment.

Comment the two following lines in the vendor/drupal/drupal/core/vendor/composer/autoload_files.php file:

    //$vendorDir . '/kriswallsmith/assetic/src/functions.php',
    //$baseDir . '/core/lib/Drupal.php',
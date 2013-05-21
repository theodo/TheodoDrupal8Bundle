TheodoDrupalBundle
==================

This ``TheodoDrupalBundle`` aims to build a bridge between Symfony2 and
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

Step 2: Copy the Drupal files into Symfony2
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Once you have a clean Symfony2 project, you need to copy the Drupal core files
into your web directory. Get the Drupal archive file following this link:
http://ftp.drupal.org/files/projects/drupal-8.x-dev.tar.gz.
Then, uncompress the archive and move its content to your ``web/`` directory::

    $ tar xzvf drupal-8.x-dev.tar.gz
    $ cp -r drupal-8.x-dev/* [PROJECT_ROOT]/web
    $ cp -r drupal-8.x-dev/.htacess [PROJECT_ROOT]/web

However, this installation is a bit dirty, because all the Drupal files will be
contained in the ``web/`` directory. That's why it would be interesting to
improve this file inclusion; for instance, maybe there is a way to get these
files directly into a bundle via Composer.

Step 3: Include TheodoDrupalBundle
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
In your composer file, you'll need to require the ``TheodoDrupalBundle``, as well
as ``FOSUserBundle`` which is needed by the first bundle to work. That's why
you have to add the following lines to ``composer.json``::

	"require": {
        "theodo/drupal-bundle": "dev-master"
	}

Then, all you have to do is to run ``composer update`` in your command shell to
add these bundles to your project. To complete this task, you might need to
change the value of ``"minimum-stability"`` from ``"alpha"`` to ``"dev"`` in your
``composer.json``.

You also may have to add declarations of these bundles in your
``AppKernel.php`` file, like this::

	public function registerBundles()
    {
        $bundles = array(
            # other bundles...
            new Theodo\Bundle\DrupalBundle\TheodoDrupalBundle(),
        );

        return $bundles;
    }

Step 4: Update your ``config.yml`` file to fit all dependancies
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
To match the requirements of these new bundles, add these lines to your
``config.yml`` file::

	theodo_drupal:
		root:          %kernel.root_dir%/../web

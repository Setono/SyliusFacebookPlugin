# Sylius Facebook Tracking Plugin

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

Track user behavior in Facebook.

## Installation

### Step 1: Download the plugin

This plugin uses the [TagBagBundle](https://github.com/Setono/TagBagBundle) to inject scripts onto your page.

Open a command console, enter your project directory and execute the following command to download the latest stable version of this plugin:

```bash
$ composer require setono/sylius-facebook-tracking-plugin

# Omit this line if you want to override layout.html.twig as described at https://github.com/Setono/TagBagBundle#usage
$ composer require setono/sylius-tag-bag-plugin

```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.


### Step 2: Enable the plugin

Then, enable the plugin by adding it to the list of registered plugins/bundles
in `config/bundles.php` file of your project before (!) `SyliusGridBundle`:

```php
<?php
$bundles = [
    Setono\TagBagBundle\SetonoTagBagBundle::class => ['all' => true],
    
    // Omit this line if you didn't install the SyliusTagBagPlugin in step 1
    Setono\SyliusTagBagPlugin\SetonoSyliusTagBagPlugin::class => ['all' => true],
    
    Setono\SyliusFacebookTrackingPlugin\SetonoSyliusFacebookTrackingPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
];
```

### Step 3: Configure plugin

```yaml
# config/packages/_sylius.yaml
imports:
    # ...
    - { resource: "@SetonoSyliusFacebookTrackingPlugin/Resources/config/app/config.yaml" }
    # ...
```

### Step 4: Import routing

```yaml
# config/routes/setono_sylius_facebook_tracking.yaml
setono_facebook_tracking:
    resource: "@SetonoSyliusFacebookTrackingPlugin/Resources/config/routing.yaml"
```

### Step 5: Update your database schema

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

### Step 6: Create a pixel
When you create a pixel in Facebook you receive a pixel id.

Now create a new pixel in your Sylius shop by navigating to `/admin/pixels/new`.
Remember to enable the pixel and enable the channels you want to track. 

### Step 7: You're ready!
The events that are tracked are located in the [EventListener folder](src/EventListener).

## Contribute
Ways you can contribute:
* Translate [messages](src/Resources/translations/messages.en.yaml) and [validators](src/Resources/translations/validators.en.yaml) to your mother tongue
* Create Behat tests that verifies the scripts are outputted on the respective pages
* Create new event subscribers that handle [Facebook events](https://developers.facebook.com/docs/facebook-pixel/reference/) which are not implemented

Thank you!

[ico-version]: https://poser.pugx.org/setono/sylius-facebook-tracking-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-facebook-tracking-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-facebook-tracking-plugin/license
[ico-travis]: https://travis-ci.com/Setono/SyliusFacebookTrackingPlugin.svg?branch=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusFacebookTrackingPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-facebook-tracking-plugin
[link-travis]: https://travis-ci.com/Setono/SyliusFacebookTrackingPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusFacebookTrackingPlugin

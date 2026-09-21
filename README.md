# Sylius Facebook Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

Track ecommerce events in your store and send them to Facebook to enable your marketing efforts inside Facebook.

## Installation

### Step 1: Download the plugin

```bash
composer require setono/sylius-facebook-plugin
```

The plugin builds on the [Meta Conversions API bundle](https://github.com/Setono/MetaConversionsApiBundle) and the
[Meta Conversions API PHP SDK](https://github.com/Setono/meta-conversions-api-php-sdk). Until their next stable
releases are out you have to allow their pre-releases in your own `composer.json`, because a stability flag on a
dependency's requirement is not inherited:

```bash
composer require setono/sylius-facebook-plugin:^3.0@beta \
    setono/meta-conversions-api-bundle:^1.0@alpha \
    setono/meta-conversions-api-php-sdk:^2.0@alpha
```

The SDK depends on [php-http/discovery](https://github.com/php-http/discovery), which contains a Composer plugin.
Composer asks whether to allow it, and either answer works. To skip the prompt, e.g. in CI, declare it in your
`composer.json`:

```json
{
    "config": {
        "allow-plugins": {
            "php-http/discovery": false
        }
    }
}
```

### Step 2: Enable the plugin

Then, enable the plugin by adding it to the list of registered plugins/bundles
in `config/bundles.php` file of your project before (!) `SyliusGridBundle`:

```php
<?php
$bundles = [
    // the plugin must be added ABOVE the SyliusGridBundle
    Setono\SyliusFacebookPlugin\SetonoSyliusFacebookPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    
    // used for filtering bot requests
    Setono\BotDetectionBundle\SetonoBotDetectionBundle::class => ['all' => true],
    
    // this is the underlying bundle we use to track events
    Setono\MetaConversionsApiBundle\SetonoMetaConversionsApiBundle::class => ['all' => true],
    
    // OPTIONAL: See note below
    // Setono\ConsentBundle\SetonoConsentBundle => ['all' => true],
];
```

**OPTIONAL**: If you want to enable consent (i.e. cookie / GDPR) you can install the [consent bundle](https://github.com/Setono/ConsentBundle).

### Step 3: Configure plugin

```yaml
# config/packages/setono_sylius_facebook.yaml
imports:
    - { resource: "@SetonoSyliusFacebookPlugin/Resources/config/app/config.yaml" }
    
    # Uncomment next line if you want to load some example pixels via fixtures
    # - { resource: "@SetonoSyliusFacebookPlugin/Resources/config/app/fixtures.yaml" }
```

### Step 4: Import routing

```yaml
# config/routes/setono_sylius_facebook.yaml
setono_sylius_facebook:
    resource: "@SetonoSyliusFacebookPlugin/Resources/config/routes.yaml"
```

### Step 5 (recommended): Send the events asynchronously

Events are sent to Meta with your application's HTTP client (`psr18.http_client`), which a Sylius application already
ships with, so requests show up in the profiler and honour your timeouts.

By default an event is sent while the page that raised it is rendered. To take that request off your visitors' page
loads, route the command to a Messenger transport:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'Setono\MetaConversionsApiBundle\Message\Command\SendEvent': main
```

Neither the access token nor any unhashed personal data is written to the transport. The access tokens are added back
from the pixels you have created in the admin when the worker sends the event, so an event for a pixel that was
disabled or removed in the meantime is not sent.

The command is dispatched on your default bus, `sylius.command_bus` in a Sylius application. Set
`setono_meta_conversions_api.server_side.message_bus` to use another one. See the
[bundle's documentation](https://github.com/Setono/MetaConversionsApiBundle#configuration) for all the options:
consent, client side tracking, user agent filters and a dedicated HTTP client.

### Step 6: Update your database schema

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Step 7: Create a pixel
When you create a pixel in Facebook you receive a pixel id.

Now create a new pixel in your Sylius shop by navigating to `/admin/facebook/pixels/new`.
Remember to enable the pixel and enable the channels you want to track. 

### Step 8: You're ready!
The events that are tracked are located in the [EventSubscriber folder](src/EventSubscriber), but here's a list:
- `ViewContent` (i.e. product page views)
- `AddToCart`
- `InitiateCheckout`
- `Purchase`
- `ViewCategory` (this is a custom event that tracks taxon views)

## Test the integration

Find the test event code in the _Test events_ tab of Meta's Events Manager and append it to any URL of your shop:
`https://example.com/?_testEventCode=TEST12345`. It is stored in the session, so the events of the following page
views show up in Events Manager too. `?_testEventCode=` (empty) clears it again.

The query parameter is only honoured when `kernel.debug` is true. To use it in production, enable it explicitly:

```yaml
# config/packages/setono_meta_conversions_api.yaml
setono_meta_conversions_api:
    test_event_code:
        query_parameter: true
```

Events are sent to the Graph API version of the installed `facebook/php-business-sdk` (v26.0 with 26.x). Run
`composer update facebook/php-business-sdk` to move to a newer version.

## Related links
- https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
- https://developers.facebook.com/docs/marketing-api/conversions-api
- https://developers.facebook.com/docs/marketing-api/conversions-api/using-the-api
- https://developers.facebook.com/docs/marketing-api/conversions-api/guides/business-sdk-features
- https://github.com/facebook/facebook-php-business-sdk

## Contribute
Ways you can contribute:
* Translate [messages](src/Resources/translations/messages.en.yaml) to your mother tongue
* Create new event subscribers that handle [Facebook events](https://developers.facebook.com/docs/facebook-pixel/reference/) which are not implemented

Thank you!

[ico-version]: https://poser.pugx.org/setono/sylius-facebook-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-facebook-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusFacebookPlugin/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/sylius-facebook-plugin
[link-github-actions]: https://github.com/Setono/SyliusFacebookPlugin/actions

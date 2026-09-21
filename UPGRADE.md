# UPGRADE FROM `v3.0.0-beta`

Applies to every release after `v3.0.0-beta`.

The plugin now builds on `setono/meta-conversions-api-bundle` `^1.0` (was `~0.1.5`) and
`setono/meta-conversions-api-php-sdk` `^2.0` (was `~0.2.4`). Events are sent to Graph API **v26.0** instead of v14.0;
Meta retired everything up to and including v20.0 on 24 September 2026. The payloads are unchanged.

Most of what changed lives in those two packages. Their upgrade guides describe every break:
[bundle](https://github.com/Setono/MetaConversionsApiBundle/blob/master/UPGRADE.md) and
[SDK](https://github.com/Setono/meta-conversions-api-php-sdk/blob/2.x/UPGRADE-2.0.md). This is what it means for a shop
that uses the plugin:

1. **Allow the pre-releases.** Until the bundle and the SDK have stable releases, require them in your own
   `composer.json`, because a stability flag on a dependency's requirement is not inherited:

    ```bash
    composer require setono/sylius-facebook-plugin:^3.0@beta \
        setono/meta-conversions-api-bundle:^1.0@alpha \
        setono/meta-conversions-api-php-sdk:^2.0@alpha
    ```

   The SDK pulls in `php-http/discovery`, which contains a Composer plugin. Add `"php-http/discovery": false` (or
   `true`) to `config.allow-plugins` to avoid the interactive prompt.

1. **Let the Messenger transport drain before you deploy**, if you route
   `Setono\MetaConversionsApiBundle\Message\Command\SendEvent` to a transport. The command changed shape, so a message
   written by the old release cannot be handled by the new one. Stop the workers once the transport is empty, deploy,
   and start them again.

   The command no longer carries the access token or any unhashed personal data. The access tokens are added back
   when the event is sent, from the pixels in the admin, so an event for a pixel that was disabled or removed in the
   meantime is not sent.

1. **The `setono_meta_conversions_api.command_bus` bus is gone.** The command is dispatched on your default bus,
   `sylius.command_bus` in a Sylius application. Routing by message class is unchanged. If you consume with
   `--bus=setono_meta_conversions_api.command_bus` or registered middleware on that bus, point it at the bus you
   want, or set `setono_meta_conversions_api.server_side.message_bus`.

1. **You no longer need Buzz.** Events are sent with your application's HTTP client (`psr18.http_client`), which a
   Sylius application already ships with. If you installed `kriswallsmith/buzz` only for this plugin, you can remove
   it. `Client::setResponseFactory()` was removed from the SDK; drop the call if you configured the client yourself.

1. **The `?_testEventCode=` query parameter only works when `kernel.debug` is true.** Set
   `setono_meta_conversions_api.test_event_code.query_parameter: true` if you rely on it in production.

1. **A failed send no longer breaks the page.** When the event is sent while the page is rendered and Meta answers
   with an error, an expired access token for instance, it is logged on the `setono_meta_conversions_api` Monolog
   channel instead of returning a 500. Every event the bundle decides not to track is logged there too, at debug
   level, which answers most "why did my event not show up" questions.

1. **Service ids of the bundle are now FQCNs.** The plugin replaces the pixel provider by aliasing
   `Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface` (was
   `setono_meta_conversions_api.pixel_provider.default`). An override or decorator of your own on one of the old
   `setono_meta_conversions_api.*` ids silently stops applying, so check them against the table in the bundle's
   upgrade guide.

1. **`DoctrineBasedPixelProvider::__construct()` takes the `RequestStack` as its third argument.** While a request is
   handled it returns the enabled pixels of the current channel, as before. Without a request, i.e. in a Messenger
   worker where the bundle asks for the access tokens, it returns every enabled pixel. If you replaced the pixel
   provider with your own, it has to work without a request too.

   To do that, `PixelRepositoryInterface` gained `findEnabled()`. A repository of your own that extends
   `PixelRepository` inherits it; one that implements the interface from scratch has to add it.

# UPGRADE FROM `1.0.x` to `2.0.x`

1. As we're moving to server side tracking - we no longer need `SetonoTagBagBundle` 
   and `SetonoSyliusTagBagPlugin`. 
   
- Remove them from `config/bundles.php` and add ones we're using:

    ```diff
         $bundles = [
     -       Setono\TagBagBundle\SetonoTagBagBundle::class => ['all' => true],
     -       Setono\SyliusTagBagPlugin\SetonoSyliusTagBagPlugin::class => ['all' => true],
     +       Setono\ClientIdBundle\SetonoClientIdBundle::class => ['all' => true],
     +       Setono\ConsentBundle\SetonoConsentBundle::class => ['all' => true],
     +       Setono\BotDetectionBundle\SetonoBotDetectionBundle::class => ['all' => true],

             Setono\SyliusFacebookPlugin\SetonoSyliusFacebookPlugin::class => ['all' => true],
             Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
         ];
    ```
   
- Remove `setono/sylius-tag-bag-plugin` if it used
    
    ```bash
    composer remove setono/sylius-tag-bag-plugin
    ```

1. Remove custom event listeners extended from `Setono\SyliusFacebookPlugin\EventListener\TagSubscriber`
   from your application:

    ```php
     -  namespace App\EventListener;
   
     -  use Setono\SyliusFacebookPlugin\EventListener\AbstractSubscriber;
    
     -  final class SomeTagSubscriber extends AbstractSubscriber
     -  {
     -  ...
     -  }
    ```
1. Configure plugin

    ```yaml
    # config/packages/setono_sylius_facebook.yaml
    ...
    setono_sylius_facebook:
        access_token: '%env(FACEBOOK_ACCESS_TOKEN)%'
    ```
    
    ```dotenv
    # .env
    ###> setono/sylius-facebook-plugin ###
    FACEBOOK_ACCESS_TOKEN=<YOUR TOKEN>
    ###< setono/sylius-facebook-plugin ###
    ```

    Warning! This plugin uses
    https://github.com/Setono/ConsentBundle
    and data will not be sent to Facebook by default.
   
    To workaround that on dev environment - you have to configure ConsentBundle like this:

    ```yaml
    # config/packages/dev/setono_consent.yaml
    setono_consent:
        marketing_granted: true
    ```

1. Update path to routes at `config/routes/setono_sylius_facebook.yaml`:

    ```diff
    setono_sylius_facebook:
    - resource: "@SetonoSyliusFacebookPlugin/Resources/config/routing.yaml"
    + resource: "@SetonoSyliusFacebookPlugin/Resources/config/routes.yaml"
    ```
   
1. Add `bin/console setono:sylius-facebook:send-pixel-events`
   command call to your CRON (hourly or more frequently)

1. Add `bin/console setono:sylius-facebook:cleanup`
   command call to your CRON (daily or less frequent)

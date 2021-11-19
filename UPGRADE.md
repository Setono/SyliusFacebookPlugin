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

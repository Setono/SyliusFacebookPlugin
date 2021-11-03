# UPGRADE FROM `1.0.x` to `2.0.x`

1. As we're moving to server side tracking - we no longer need `SetonoTagBagBundle` 
   and `SetonoSyliusTagBagPlugin`. 
   
- Remove them from `config/bundles.php`:

    ```diff
         $bundles = [
     -       Setono\TagBagBundle\SetonoTagBagBundle::class => ['all' => true],
     -       Setono\SyliusTagBagPlugin\SetonoSyliusTagBagPlugin::class => ['all' => true],
     +       Setono\ClientIdBundle\SetonoClientIdBundle::class => ['all' => true],
     +       Setono\ConsentBundle\SetonoConsentBundle::class => ['all' => true],

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

    ```yaml
    # config/packages/setono_consent.yaml
    setono_consent:
        marketing_granted: true
    ```

1. Add `bin/console setono:sylius-facebook:send-pixel-events`
   command call to your CRON

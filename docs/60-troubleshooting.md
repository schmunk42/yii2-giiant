
Troubleshooting
---------------

### `Calling unknown method: yii\console\Application::getSession()`

You need to disable eg. auth components and modules for the CLI. They don't have an effect there anyway.
Or more correctly: you need to disabled any component, which is called and which uses a PHP-session.

[see issue 115](https://github.com/schmunk42/yii2-giiant/issues/115#issuecomment-136284039)

### composer package

You can also add

    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/schmunk42/yii2-giiant.git"
      }
    ],
    "require": {
        .....(your required modules)....
        "schmunk42/yii2-giiant":"dev-master"
    },

to your *** composer.json ***  file and run

    composer update

if you are having trouble with "Not found" errors using the preferred method.

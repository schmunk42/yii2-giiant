
Troubleshooting
---------------

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


Use custom generators, model and crud templates
-----------------------------------------------

```
$config['modules']['gii'] = [
    'class'      => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1'],
    'generators' => [
        // generator name
        'giiant-model' => [
            //generator class
            'class'     => 'schmunk42\giiant\generators\model\Generator',
            //setting for out templates
            'templates' => [
                // template name => path to template
                'mymodel' =>
                    '@app/giiTemplates/model/default',
            ]
        ]
    ],
];
```
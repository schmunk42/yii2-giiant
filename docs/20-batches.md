Usage
-----

> ### Important Notice!
> It is strongly recommended, to get familiar with the CLI usage of *Gii* and *Giiant*, since the code-generation process may be repeated several times in the inital development phase and using the CLI in conjunction with a script will save you a lot of time and also reduce your error rate!

### Command Line Batches

You can run batches of base-model and CRUD generation with the build in batch command:

    ./yii giiant-batch --tables=profile,social_account,user,token

It will process the given tables, for more details see `./yii help giiant-batch`. See the [Sakila example](docs/generate-sakila-backend.md) for a detailed example.


### Alternative 

Visit your application's *Gii*-module (eg. `index.php?r=gii`) and choose one of the generators from the main menu screen.

For basic usage instructions see the [Yii2 Guide section for Gii](http://www.yiiframework.com/doc-2.0/guide-tool-gii.html).




### Extended batch-command example

```
./yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --modelNamespace=app\\models \
    --crudControllerNamespace=app\\modules\\crud\\controllers \
    --crudSearchModelNamespace=app\\modules\\crud\\models\\search \
    --crudViewPath=@app/modules/crud/views \
    --crudPathPrefix= \
    --crudSkipRelations=Variant,Variants \
    --crudProviders=schmunk42\\giiant\\crud\\providers\\optsProvider \
    --tables=account,article,variation_status,variation_x_storage,business_unit,category,\
condition,manufacturer,section,shop,storage,trading_group,user,variation,core_log,basket,basket-item,delivery
```
# CLI

## Batch command

```
$ yii giiant-batch -h
```

```
DESCRIPTION

SUB-COMMANDS

- giiant-batch/cruds            Run batch process to generate CRUDs all given tables.
- giiant-batch/index (default)  Run batch process to generate models and CRUDs for all given tables.
- giiant-batch/models           Run batch process to generate models all given tables.

To see the detailed information about individual sub-commands, enter:

  yii help <sub-command>
```

Example: `yii giiant-batch/models` will generate only model classes.


## Model (Gii template)

```
$ yii gii/giiant-model -h
```

### Help

```
DESCRIPTION

This generator generates an ActiveRecord class and base class for the
specified database table.


USAGE

yii gii/giiant-model [...options...]


OPTIONS

--appconfig: string
  custom application configuration file path.
  If not set, default application configuration is used.

--baseClass: string (defaults to 'yii\db\ActiveRecord')
  This is the base class of the new ActiveRecord class. It should be a fully
  qualified namespaced class name.

--baseClassSuffix: string (defaults to '')

--baseTraits: string

--color: boolean, 0 or 1
  whether to enable ANSI color in the output.
  If not set, ANSI color will only be enabled for terminals that support it.

--createdAtColumn: string (defaults to 'created_at')
  The column name where the created at timestamp is stored.

--createdByColumn: string (defaults to 'created_by')
  The column name where the record creator's user ID is stored.

--db: string (defaults to 'db')
  This is the ID of the DB application component.

--enableI18N: boolean, 0 or 1 (defaults to 0)
  This indicates whether the generator should generate strings using Yii::t()
  method. Set this to true if you are planning to make your application
  translatable.

--generateHintsFromComments: boolean, 0 or 1 (defaults to 1)
  This indicates whether the generator should generate attribute hints by
  using the comments of the corresponding DB columns.

--generateLabelsFromComments: boolean, 0 or 1 (defaults to 0)
  This indicates whether the generator should generate attribute labels by
  using the comments of the corresponding DB columns.

--generateModelClass: boolean, 0 or 1 (defaults to 0)
  This indicates whether the generator should generate the model class, this
  should usually be done only once. The model-base class is always generated.

--generateQuery: boolean, 0 or 1 (defaults to 0)
  This indicates whether to generate ActiveQuery for the ActiveRecord class.

--generateRelations: string (defaults to 'all')
  This indicates whether the generator should generate relations based on
  foreign key constraints it detects in the database. Note that if your
  database contains too many tables, you may want to uncheck this option to
  accelerate the code generation process.

--help, -h: boolean, 0 or 1
  whether to display help information about current command.

--interactive: boolean, 0 or 1 (defaults to 1)
  whether to run the command interactively.

--languageCodeColumn: string (defaults to 'language')
  The column name where the language code is stored.

--languageTableName: string (defaults to '{{table}}_lang')
  The name of the table containing the translations. {{table}} will be
  replaced with the value in "Table Name" field.

--messageCategory: string (defaults to 'models')
  This is the category used by Yii::t() in case you enable I18N.

--modelClass: string
  This is the name of the ActiveRecord class to be generated. The class name
  should not contain the namespace part as it is specified in "Namespace".
  You do not need to specify the class name if "Table Name" ends with
  asterisk, in which case multiple ActiveRecord classes will be generated.

--ns: string (defaults to 'app\models')
  This is the namespace of the ActiveRecord class to be generated, e.g.,
  app\models

--overwrite: boolean, 0 or 1 (defaults to 0)
  whether to overwrite all existing code files when in non-interactive mode.
  Defaults to false, meaning none of the existing code files will be overwritten.
  This option is used only when `--interactive=0`.

--queryBaseClass: string (defaults to 'yii\db\ActiveQuery')
  This is the base class of the new ActiveQuery class. It should be a fully
  qualified namespaced class name.

--queryClass: string
  This is the name of the ActiveQuery class to be generated. The class name
  should not contain the namespace part as it is specified in "ActiveQuery
  Namespace". You do not need to specify the class name if "Table Name" ends
  with asterisk, in which case multiple ActiveQuery classes will be
  generated.

--queryNs: string (defaults to 'app\models')
  This is the namespace of the ActiveQuery class to be generated, e.g.,
  app\models

--removeDuplicateRelations: boolean, 0 or 1 (defaults to 0)

--savedForm: string
  Choose saved form ad load it data to form.

--singularEntities: boolean, 0 or 1 (defaults to 0)

--tableName (required): string
  This is the name of the DB table that the new ActiveRecord class is
  associated with, e.g. post. The table name may consist of the DB schema
  part if needed, e.g. public.post. The table name may end with asterisk to
  match multiple table names, e.g. tbl_* will match tables who name starts
  with tbl_. In this case, multiple ActiveRecord classes will be generated,
  one for each matching table name; and the class names will be generated
  from the matching characters. For example, table tbl_post will generate
  Post class.

--tableNameMap: array

--tablePrefix: string
  Custom table prefix, eg app_.<br/><b>Note!</b> overrides yii\db\Connection
  prefix!

--template: string (defaults to 'default')

--updatedAtColumn: string (defaults to 'updated_at')
  The column name where the updated at timestamp is stored.

--updatedByColumn: string (defaults to 'updated_by')
  The column name where the record updater's user ID is stored.

--useBlameableBehavior: boolean, 0 or 1 (defaults to 1)
  Use BlameableBehavior for tables with column(s) for created by and/or
  updated by user IDs.

--useSchemaName: boolean, 0 or 1 (defaults to 1)
  This indicates whether to include the schema name in the ActiveRecord class
  when it's auto generated. Only non default schema would be used.

--useTablePrefix: boolean, 0 or 1 (defaults to 0)
  This indicates whether the table name returned by the generated
  ActiveRecord class should consider the tablePrefix setting of the DB
  connection. For example, if the table name is tbl_post and
  tablePrefix=tbl_, the ActiveRecord class will return the table name as
  {{%post}}.

--useTimestampBehavior: boolean, 0 or 1 (defaults to 1)
  Use TimestampBehavior for tables with column(s) for created at and/or
  updated at timestamps.

--useTranslatableBehavior: boolean, 0 or 1 (defaults to 1)
  Use 2amigos/yii2-translateable-behavior for tables with a relation to a
  translation table.
```

## CRUD (Gii template)

```
$ yii gii/giiant-crud -h
```

### Help

```
DESCRIPTION

This generator generates an extended version of CRUDs.


USAGE

yii gii/giiant-crud [...options...]


OPTIONS

--accessFilter: boolean, 0 or 1 (defaults to 0)

--actionButtonClass: string (defaults to 'yii\web\grid\ActionColumn')

--appconfig: string
  custom application configuration file path.
  If not set, default application configuration is used.

--baseControllerClass: string (defaults to 'yii\web\Controller')
  This is the class that the new CRUD controller class will extend from. You
  should provide a fully qualified class name, e.g., yii\web\Controller.

--baseTraits: string

--color: boolean, 0 or 1
  whether to enable ANSI color in the output.
  If not set, ANSI color will only be enabled for terminals that support it.

--controllerClass (required): string
  This is the name of the controller class to be generated. You should
  provide a fully qualified namespaced class (e.g.
  app\controllers\PostController), and class name should be in CamelCase with
  an uppercase first letter. Make sure the class is using the same namespace
  as specified by your application's controllerNamespace property.

--controllerNs: string

--enableI18N: boolean, 0 or 1 (defaults to 0)
  This indicates whether the generator should generate strings using Yii::t()
  method. Set this to true if you are planning to make your application
  translatable.

--enablePjax: boolean, 0 or 1 (defaults to 0)
  This indicates whether the generator should wrap the GridView or ListView
  widget on the index page with yii\widgets\Pjax widget. Set this to true if
  you want to get sorting, filtering and pagination without page refreshing.

--fixOutput: boolean, 0 or 1 (defaults to 0)

--formLayout: string (defaults to 'horizontal')

--generateAccessFilterMigrations: boolean, 0 or 1 (defaults to 0)

--gridMaxColumns: integer (defaults to 8)

--gridRelationMaxColumns: integer (defaults to 8)

--help, -h: boolean, 0 or 1
  whether to display help information about current command.

--indexGridClass: string (defaults to 'yii\grid\GridView')

--indexWidgetType: string (defaults to 'grid')
  This is the widget type to be used in the index page to display list of the
  models. You may choose either GridView or ListView

--interactive: boolean, 0 or 1 (defaults to 1)
  whether to run the command interactively.

--messageCategory: string (defaults to 'cruds')
  This is the category used by Yii::t() in case you enable I18N.

--migrationClass: string

--modelClass (required): string
  This is the ActiveRecord class associated with the table that CRUD will be
  built upon. You should provide a fully qualified class name, e.g.,
  app\models\Post.

--modelMessageCategory: string (defaults to 'models')
  Model message categry.

--moduleNs: string

--overwrite: boolean, 0 or 1 (defaults to 0)
  whether to overwrite all existing code files when in non-interactive mode.
  Defaults to false, meaning none of the existing code files will be overwritten.
  This option is used only when `--interactive=0`.

--overwriteControllerClass: boolean, 0 or 1 (defaults to 0)

--overwriteRestControllerClass: boolean, 0 or 1 (defaults to 0)

--overwriteSearchModelClass: boolean, 0 or 1 (defaults to 0)

--pathPrefix: string
  Customized route/subfolder for controllers and views eg. crud/.
  <b>Note!</b> Should correspond to viewPath.

--providerList: array
  Choose the providers to be used.

--requires: array

--savedForm: string
  Choose saved form ad load it data to form.

--searchModelClass: string (defaults to '')
  This is the name of the search model class to be generated. You should
  provide a fully qualified namespaced class name, e.g.,
  app\models\PostSearch.

--singularEntities: boolean, 0 or 1 (defaults to 0)

--skipRelations: array

--tablePrefix: string

--template: string (defaults to 'default')

--tidyOutput: boolean, 0 or 1 (defaults to 1)

--viewPath: string (defaults to '@backend/views')
  Output path for view files, eg. @backend/views/crud.


```





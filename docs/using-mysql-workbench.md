## MySQL Workbench model update and sync to databse

Requirements
------------

```
composer require dmstr/yii2-db
```

Model-Schema Update Process
---------------------------

- Make sure you have a clean model (sync database to *MySQL Workbench* model)
- Apply changes to *MySQL Workbench* model

*Switch to Console*

- Prepare a migration file for the sql dump with `./yii migrate/create sync_model_with_database`
- Open the newly created PHP file of the migration
  - extend the class from `\common\components\MysqlFileMigration`
  - remove `up()` and `down()` methods
  - Save the file
- Copy the generated filename, eg. `m140709_123938_sync_model_with_database.php`

*Switch to MySQL Workbench*

-  Select `Database > Synchronize Model`
  - Continue to **Model and Database Differences**
  - Preview changes, do NOT click `Execute`
- , click `Save to File...`
  - rename the file to `m140709_123938_sync_model_with_database.sql`
- Save the file,  do NOT click `Execute`

--------

**MAKE SURE TO REMOVE ANY SCHEMA QUALIFIERS FROM YOUR TABLE NAMES IN YOUR DUMP.**

--------

*Switch to Console*

- Use `./yii migrate` to test and apply the migration
- You should see `Migrated up successfully.` on the console.

> Hint! To test whether all your changes have been applied, you can sync your model again, but should see no changes in *MySQl Workbench*.
 
  
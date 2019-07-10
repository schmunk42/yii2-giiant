Changelog
=========

### 0.11.0

 - *no changes*

### 0.11.0-beta3

 - fixed multi-table json generation table names
 - fixed module labels for json files
 - pager align in GridView
 - changed icons in index view
 - fixed enum values
 - fixed multi-table model json generation

### 0.11.0-beta2
 - generateAccessFilterMigrations option
 - dropdown list in GridView filed search filter optimization
 - select position ActionColumn in GridView (left or right)
 - fix view icon in ActionColumn
 - pager align in GridView
  
### 0.11.0-beta1
 - added dropdown list in GridView filed search filter
 - allow usage with `yiisoft/yii2-gii:^2.1`
 
### 0.10.8
 - fixed error with php 7.3
 - Correct message category for view action button
 
### 0.10.7
 - add baseClassPrefix to model generator.
 
### 0.10.6
 - typo of class_exists
 
### 0.10.5
 - Update SaveForm.php
 - Additional validation
 - removes reprecated yii2-codeception package deps
 - Update README.md
 - fixed path
 
### 0.10.4
 - Fix singularEntities not working when using cli
 
### 0.10.3
 - Fix XSS Vulnerability in CRUD views generated
 
### 0.10.2
 - Close DB after each model generation
 - added test form view, fixes #230
 - updated docs
 
### 0.10.1  
 - added tables & skipTables check, refactored skipTables to run in beforeAction
 - Fix #196
 - update for skip db tables
 
### 0.10.0
 - added "formLayout" property
 
### 0.10.0-beta2 
 - updated Object to BaseObject, fixes #220
 - updated dependencies
 - fixed phptidy usage
 - updated yii2-gii constraint to ~2.0.6
 
### 0.10.0-beta1  
 - applied fixes from #197
 - Fixed spacing in model extended template.
 - updated example database image
 - updated db-test image
 - refactored paths; added missing dependency
 - added legacy yii2-codeception dependency
 - removed generated tester classes
 - refactoring tests
 - specify DB connection component in generated model, if using non-default 'db' (feature of yii2-gii 2.0.0)
 - include modelGenerateRelation attribute into BatchController
 - include attributes for model-generator's timestampBehavior and blameableBehavior into BatchController
 - get has_one relations as tab in model view
 - if no types to filter are given to ModelTrait::getModelRelations (like in generators/crud/default/views/index.php) we should return all types.
 - use relation property instead of relation getter to avoid multiple db queries.
 - moved messages in ‚yii‘ catalog to ‚crud‘ and ‚giiant‘
 - Fix broken URLs on crud relations
 - Fix CallbackProvider examples
 - improved detection of text columns
 - Fixed namespaces in documentation about providers
 - Merge branch 'master' into feature/fix-relation-detection
 - fixed relation detection
 - updated page titles & form-layout

### 0.9.2
 - updated constraint to allow phptidy on PHP 7
 - updated CLI command infos
 - fixed bootstrap alert class

### 0.9.1

 - improved detection of database text columns in callbacks
 - fixed #191
 - added basic NOSQL support (tested with elasticsearch)

### 0.9.0

*beta3-beta6*

 - added color column example
 - apply cutom model name in UI giiant
 - added icons, relation buttons
 - wrap action column buttons in div
 - enable tidyOuput in batch controller by default
 - generate Access Filter Migrations #179
 - do not overwrite search model class by default; added parameter
 - splitted providers into core and extension providers, getCoreProviders should return core providers only
 - removed hardcoded layout from grid, use DI to configure grid
 - added static callback functions for field and column
 - removed logic from model-extended template, see also #170
 - added kartik gridview to modules in test config
 - updated tests

### 0.9.0-beta2

  * added catalogue for model related translations to CRUD generator

### 0.9.0-beta1

  * fixed missing spaces in headlines
  * fixed #141 - removed dependency to dmstr\helpers\Html
  * fixed `crud/default/view` template, create new related record link param
  * fixed #162
  * added empty merge-with-parent methods for model
  * added separate message categories for model and cruds (batch controller)
  * added route param to permission check - see also https://github.com/dmstr/yii2-web/blob/master/src/User.php
  * added parameter to enable access filter migrations
  * added parameter for controller base traits

### 0.8.4

  * fixed `crud/default/view` template, create new related record link param

### 0.8.3

  * fixed `$actionColumnTemplateString` in crud default view index.php

### 0.8.2

  * fixes for message catalogue / translatables
  * SavedForms fix

### 0.8.1

  * added mission option `$useTimestampBehavior`, default is `true` to control the use of `yii\behaviors\Timestampbehavior` in CLI BatchController for model generation

### 0.8.0

  * added giiant-extension generator
  * :warning: removed `getAliasModel()`
  * updated module template
  * fixed form/cli validations

### 0.7.2

  * fixed copy button

### 0.7.0-0.7.1

*see git log*

### 0.6.1 (2015-12-28)

  * improved test stack isolation
  * fixed and improved tests in CI
  * added access control to REST controller

### 0.6.0 (2015-12-26)

  * added separate style for default controller (giiant module index view)
  * added upgrading info
  * updated requirements
  * updated dockerized potemkin testing
  * added build message, fixed ENV handling
  * fixed php image version in testing stack
  * Merge pull request #128 from sebathi/master
  * fixed #118
  * added gitlab-ci config
  * Merge pull request #126 from pawelryznar/patch-1
  * create search model directory in batch command
  * fixed model namespace errors; added test generator to module bootstrapping
  * added cmrcx/phptidy dependency
  * removed panel class from views to allow better customization
  * fixed error, when extended controller class contained contents of the base controller
  * fixed #123
  * Merge pull request #119 from gradosevic/master
  * Merge pull request #124 from christophmuth94/master
  * fixed crudProviders paths
  * updated provider example
  * updated installation instructions
  * Added unit tests for GiiantFaker class
  * Implemented crud unit test generator. fixed issue on search model
  * added copy button for records
  * fixed flash messages - use add instead of set (do not overwrite)
  * added phptidy output option
  * fixed detection for database from DI-container
  * flush logs after every loop in batch
  * Merge pull request #117 from christophmuth94/master
  * Editor Provider updated
  * don't skip virtualAttributes by default, added property to change behavior
  * removed legacy code
  * added overwrite check for api controller
  * updated docs
  * added controller namespace property
  * refactored CRUD controllers - split into Controller and BaseController - added REST-ActiveController
  * updated tag for crud navigation buttons - using div, since <ul,ol>s are not allowed inside <p>

Changelog
=========

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

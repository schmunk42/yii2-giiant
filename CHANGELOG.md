Changelog
=========

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

## Create unit tests for models 

-----------------------------

/!\ This section is under development

-----------------------------

Example, inside phundament/app docker container: 

    yii gii/giiant-test \
        --template=default \
        --tableName=app_machine \
        --modelNs=app\\models\\ \
        --modelClass=Machine \
        --ns=\\tests\\gen \
        --codeceptionPath=/


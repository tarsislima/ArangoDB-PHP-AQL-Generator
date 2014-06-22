# README #

AqlParser Beta 

This is a experimental parser to generate Aql Queries and is in beta
don´t use in production

### What is this repository for? ###

* Quick summary
* Version 0.2
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Summary of set up
 use a Statement Class of Aql Driver to run the query

```
#!php

<?php

namespace triagens\ArangoDb;

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'init.php';


try {
    $bindVars = [];

    $query1 = new Aql();
    $query1->query('u', 'users');
  // returns:  FOR u IN users RETURN u
    echo $query1->get();


    $connection = new Connection($connectionOptions);
    $statement = new Statement($connection, array(
                                                     "query"     => $query->get(),
                                                     "count"     => true,
                                                     "batchSize" => 1000,
                                                     "bindVars"  => $bindVars,
                                                     "sanitize"  => true,
                                                ));



        $cursor = $statement->execute();
        var_dump($cursor->getAll());

} catch (ConnectException $e) {
    print $e . PHP_EOL;
} catch (ServerException $e) {
    print $e . PHP_EOL;
} catch (ClientException $e) {
    print $e . PHP_EOL;
}
```

use /arangoDb
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact
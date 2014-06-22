# README #

AqlParser For ArangoDb  [Beta]

This is a experimental parser to generate Aql Query Strings more easy and is in beta.Don´t use in production!

### What is this repository for? ###

* Quick summary
* Version 0.2
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

*  To run queries use a Statement Class of Arangodb Driver available in [Github ArangoDB-PHP](https://github.com/triAGENS/ArangoDB-PHP)

```
#!php

<?php

namespace triagens\ArangoDb;

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'init.php';


try {
    $bindVars = [];

//SIMPLE QUERY

    $query1 = new Aql();
    $query1->query('u', 'users');
  // Generate:  FOR u IN users RETURN u
    echo $query1->get();

  //WITH CONDITION
    $query1 = new Aql();
    $query1->query('u', 'users')->filter('u.name == @name', ['name'=>'Jhon']);

  /* Generate: 
    FOR u IN users 
    FILTER
    RETURN u
*/
    echo $query1->get();


    $connection = new Connection($connectionOptions);
    $statement = new Statement($connection, array(
                          "query"     => $query1->get(),
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
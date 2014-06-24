# README #

AqlParser For ArangoDb   [Beta]

This is a experimental parser to generate Aql Query Strings more easy and is in beta.Don´t use in production!

### What is this repository for? ###

* Quick summary
* Version 0.2
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### Important? ###

* This interface only generates the string of AQL. To run this queries you can use  the Statement Class of Arangodb Driver available on [Github ArangoDB-PHP](https://github.com/triAGENS/ArangoDB-PHP)

### Setup Statement
```
#!php
<?php
$connection = new Connection($connectionOptions);
$statement = new Statement($connection, array(
                          "query"     => '',
                          "count"     => true,
                          "batchSize" => 1000,
                          "sanitize"  => true,
                      ));


```
###


### Examples ###
* Simple query
```
#!php

<?php

//SIMPLE QUERIES

    $query1 = new Aql();
    $query1->query('u', 'users');

    echo $query1->get();
  // Generate:  FOR u IN users RETURN u

  //WITH filter
    $query1 = new Aql();
    $query1->query('u', 'users')->filter('u.yearsOld == 20');

  
    echo $query1->get();
/* Generate: 
    FOR u IN users 
    FILTER u.yearsOld == 20
    RETURN u
*/

//use 
$statement->setQuery($query1->get());
$statement->bind($query1->getParams());

$cursor = $statement->execute();


```

* Composite query
```
#!php

<?php

    $mainQuery = new Aql();

    $query2 = new Aql();
    $query2->query('l', 'locations')->filter('u.id == l.id');

    $mainQuery->query('u', 'users')
              ->subquery($query2) 
              ->serReturn(['user'=>'u', 'location'=>'l']);

    echo $mainQuery->get();
  /* Generate: 
    FOR u IN users 
       FOR l IN locations 
          FILTER u.id == l.id
    RETURN {"user":u, "location":l}
*/

//use 

$statement->setQuery($mainQuery->get());
$statement->bind($mainQuery->getParams());

$cursor = $statement->execute();
//:::::::::::::::::::::::::::::::::::::::

BIND VARS

$filter = new Filter('u.id == @id',['id'=> 19]);

if(!empty($myvar['name'])) {
   $filter->andFilter('u.name == @name',['name'=>$myvar['name']]);
   //  OR 
   //  $filter->andFilter('u.name == @name');
   //  $filter->addParams(['name'=>'jose']);
   //
}

 $query3 = new Aql();
    $query3->query('l', 'locations')
            ->filter($filter);


//use 

$statement->setQuery($query3->get());
$statement->bind($query3->getParams());

```

* Using  Statement to run Query
```
#!php

<?php

namespace triagens\ArangoDb;

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'init.php';

try {
   
    $mainQuery = new Aql();


    $query2 = new Aql();
    $query2->query('l', 'locations')->filter('u.id == l.id');

    $mainQuery->query('u', 'users')
              ->subquery($query2)
              ->serReturn(['user'=>'u', 'location'=>'l']);

  echo $mainQuery->get();

  /* Generate: 
    FOR u IN users 
       FOR l IN locations 
          FILTER u.id == l.id
    RETURN {"user":u, "location":l}
  */


    $connection = new Connection($connectionOptions);
    $statement = new Statement($connection, array(
                          "query"     => $mainQuery->get(),
                          "count"     => true,
                          "batchSize" => 1000,
                          "bindVars"  => $mainQuery->getParams(),
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

*ex query

```
#!php
<?php
$connection = new Connection($connectionOptions);
$statement = new Statement($connection, array(
                          "query"     => '',
                          "count"     => true,
                          "batchSize" => 1000,
                          "sanitize"  => true,
                      ));

//example 1
  $mainQuery = new Aql();

  $query2 = new Aql();
  $query2->query('l', 'locations')->filter('u.id == l.id');

  $mainQuery->query('u', 'users')
              ->subquery($query2)
              ->serReturn(['user'=>'u', 'location'=>'l']);

  echo $mainQuery->get();
 /* Generate this string: 
    FOR u IN users 
       FOR l IN locations 
          FILTER u.id == l.id
    RETURN {`user`:u, `location`:l}
  */

//  use
$statement->setQuery($mainQuery->get());
$statement->bind($mainQuery->getParams()); 


//Example 2 : filter

$mainQuery = new Aql();
$filter = new Filter('u.id == @id && 1=1',['id'=> 19]);

if(!empty($_POST['name'])) {
   $filter->andFilter('u.name == @name', ['name'=>$_POST['name']]);

   /*  other way 
       $filter->andFilter('u.name == @name');
       $mainQuery->addParams(['name'=>'jose']); 
   */
}


 $mainQuery->query('u', 'users')
            ->filter($filter);


//  use
$statement->setQuery($mainQuery->get());
$statement->bind($mainQuery->getParams());

```


* Configuration
* Dependencies


### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact
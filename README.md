# README #

AQL Generator For ArangoDb-PHP   [Beta]

This is a experimental parser to generate Aql Query Strings and is in beta. Don´t use in production!

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


### Examples ###
* Simple query
```
#!php
<?php

 use Aqlgen/AqlGen;

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
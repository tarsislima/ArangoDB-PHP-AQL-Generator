# README #

AQL Generator For ArangoDb-PHP   [Beta]

This is a experimental parser to generate Aql Query Strings and is in beta. Don´t use in production!

### What is this repository for? ###

* Quick summary
* Version 1.0.0
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### Important ###

* This interface only generates the string of AQL. To run this queries you can use  the Statement Class of Arangodb Driver available on [Github ArangoDB-PHP](https://github.com/triAGENS/ArangoDB-PHP)

### Setup and basic 
```

<?php
//configure statement
$connection = new Connection($connectionOptions);
$statement = new Statement($connection, array(
                          "query"     => '',
                          "count"     => true,
                          "batchSize" => 1000,
                          "sanitize"  => true,
                      ));
                      
use tarsys\AqlGen\AqlGen;
//use tarsys\AqlGen\AqlFilter; // if use filter

  //mount the query
  $query1 = new AqlGen();
  $query1->query('u', 'users'); //
    
    
// execute 
$statement->setQuery($mainQuery->get());
//$statement->bind($mainQuery->getParams()); //if some params has passed


```


### Examples ###
* Simple query
```

<?php

    //SIMPLE QUERIES

    $query1 = new AqlGen();
    $query1->query('u', 'users');

     echo $query1->get();
  // Generate:  FOR u IN users RETURN u

  //WITH filter
    $query1 = new AqlGen();
    $query1->query('u', 'users')->filter('u.yearsOld == 20');

  
    echo $query1->get();
/* Generate: 
    FOR u IN users 
    FILTER u.yearsOld == 20
    RETURN u
*/

```

* More examples 

```

<?php

//Example 1: subquery

  $mainQuery = new AqlGen();

  $query2 = new AqlGen();
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


//Example 2 : filter

use tarsys\Aqlgen\AqlGen;
use tarsys\Aqlgen\AqlFilter;

$mainQuery = new AqlGen();
$filter = new AqlFilter('u.id == @id && 1=1',['id'=> 19]); 
$filter->orFilter('u.group == @group', ['group'=>'11']);
$filter->andFilter('u.name == @name', ['name'=>$_POST['name']]);

   /*  other way to pass bind params
       $filter->andFilter('u.name == @name');
       $mainQuery->addParams(['name'=>'jose']); 
   */
}


 $mainQuery->query('u', 'users')
            ->filter($filter);



//Example 3 : LET


$mainQuery = new AqlGen();

$mainQuery->query('u', 'users')
            ->let('myvar', 'hello')
            ->let('myfriends', AqlGen::instance()->query('f','friends') );
 
 echo $mainQuery->get();
 
 /* Generate this string: 
    FOR u IN users 
       LET  `myvar` = `hello`
       LET `myfriends` = ( 
          FOR f IN friends 
          RETURN f
        )
    RETURN u
  */


//Example 4 : COLLECT

$mainQuery = new AqlGen();

$mainQuery->query('u', 'users')
            ->collect('myvar', 'u.city', 'g');

echo $mainQuery->get();
 
 /* Generate this string: 
    FOR u IN users 
       COLLECT `myvar` = u.city INTO g
       
    RETURN u
  */



```



* Configuration
* Dependencies


### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

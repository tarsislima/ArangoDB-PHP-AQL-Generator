# README #

AQL Generator For ArangoDb-PHP   [Alpha]

This is a experimental builder to generate Aql Query Strings and is in alpha version.

### What is this repository for? ###

* Quick summary
* Version 1.1a

### Important ###

* This interface only generates the string of AQL. To run this queries in this examples was used the Statement Class of Arangodb Driver available on [Github ArangoDB-PHP](https://github.com/triAGENS/ArangoDB-PHP)

### Setup and basic 
```php
//configure statement
$connection = new Connection($connectionOptions);
$statement = new Statement($connection, array(
                          "query"     => '',
                          "count"     => true,
                          "sanitize"  => true,
                      ));
                      
use tarsys\AqlGen\AqlGen;

  //mount the query
  $query1 = AqlGen::query('u', 'users'); 
    
//execute 
$statement->setQuery($mainQuery->get());
//$statement->bind($mainQuery->getParams()); //if some params has passed
```


### Examples ###
* Simple query
```php
   //SIMPLE QUERIES

   $query1 = AqlGen::query('u', 'users'); 

     echo $query1->get();
  // Generate:  FOR u IN users RETURN u

  //WITH filter
   $query1 = AqlGen::query('u', 'users')->filter('u.yearsOld == 20');
  
    echo $query1->get();
/* Generate: 
    FOR u IN users 
    FILTER u.yearsOld == 20
    RETURN u
*/

```

* More examples 

```php
//Example 1: subquery

  $mainQuery = AqlGen::query('u', 'users'); 

  $locations = AqlGen::query('l', 'locations')->filter('u.id == l.id');

  $mainQuery->subquery($locations)
              ->serReturn('{"user": u, "location": l}');

  echo $mainQuery->get();
 /* Generate this string: 
    FOR u IN users 
       FOR l IN locations 
          FILTER u.id == l.id
    RETURN {`user`:u, `location`:l}
  */


//Example 2 : filter bind variables

$mainQuery = AqlGen::query('u', 'users')->filter('u.id == @id', ['id'=> 19]); 

$mainQuery->filter('u.name == @name && u.age == @age')->bindParams(['name'=> 'jhon', 'age' => 20]);
$mainQuery->orFilter('u.group == @group')->bindParam('group', 11);
  
echo $mainQuery->get();
/* Generate: 
    FOR u IN users 
       FILTER u.id == @id  && u.name == @name && u.age == @age ||  u.group == @group
    RETURN u
*/

// USE $mainQuery->getParams(); to retrieve bind params


//Example 3 : LET

$mainQuery = AqlGen::query('u', 'users')
            ->let('myvar', 'hello')
            ->let('myfriends', AqlGen::query('f','friends') );
 
 echo $mainQuery->get();
 
 /* Generate this string: 
    FOR u IN users 
       LET  myvar = `hello`
       LET  myfriends = ( 
          FOR f IN friends 
          RETURN f
        )
    RETURN u
  */

//Example 4 : COLLECT

$mainQuery = AqlGen::query('u', 'users')
            ->collect('myvar', 'u.city', 'g');

echo $mainQuery->get();
 
 /* Generate this string: 
    FOR u IN users 
       COLLECT `myvar` = u.city INTO g
    RETURN u
  */

//Example 5 : SORT

$mainQuery = AqlGen::query('u', 'users')
            ->sort('u.activity', AqlGen::SORT_DESC)
            ->sort(array('u.name','u.created_date')); // asc by default

echo $mainQuery->get();
 
 /* Generate this string: 
    FOR u IN users 
       SORT u.activity DESC, u.name, u.created_date ASC
    RETURN u
  */
```



* Configuration
* Dependencies


### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

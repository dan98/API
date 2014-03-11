#RatingAPI

REST API that helps you building ELO based rating systems.

##Usage

### Login
To use the API, firstly you need to have a session so you can include it in every request made. If you use the PHP RatingClient, it retrieves the session automatically and stores it in memcached. Every time RatingClient instantiated, it checks if the cached session is not outdated by performing a test request to '/api/checkSession' and if the response code is 401, the class retrieves a new session and rewrites the one in memcached. 

Create an instance with your credentials and you are good to go:
```php
include('RatingAPI.php');
$api = new RatingClient('<consumer-id>', '<consumer-secret>');
```
### List objects / battles
To retrieve lists of objects or battles, use the methods `$api->listObjects()` and `$api->listBattles()`. These methods will return an ordered array with objects RatingObject or RatingBattle.
```php
// Returns 10 objects ordered by their score descendent
$top = $api->listObjects(array('order' => 'score DESC', 'limit' => 10));

// Return last 10 battles
$history = $api->listBattles(array('limit' => 10, 'order' => 'created_time DESC'));

$api->listObjects(array(
  'order'=>'ASC', // or 'DESC' or 'random'. Random retrieves two random objects
  'limit'=>10,
));

$api->listBattles(array(
  'order'=>'ASC', // or 'DESC' or 'random'. Random retrieves two random battles
  'limit'=>10,
  'object'=>$id// Returns the battles where $id is the winner or loser id (participated in the battle). 
));
```
If `$object` is an instance of RatingObject, these lines return the same thing.
```php
$object->battles(array('limit' => 5, 'order' => 'created_time DESC'));
$api->listBattles(array('limit' => 5, 'order' => 'created_time DESC', 'object'=>$object->id));

```
When using the RatingObject method `$object->battles()` it simply calls `$api->listBattles()` adding the 'object'=>$object->id to the options array.

### Get Object / Battle
You can use the RatingClient methods `$api->getObject($id)` and `$api->getBattle($id)` to retrieve an instance of an object or battle.
```php
$winner = $api->getObject($id);
echo $winner->score;

$battle = $api->getBattle($id);
echo $battle->created_time;
```

### Create Object / Battle
To create a new object, use the method `$api->createObject()`.
```php
$api->createObject()
```










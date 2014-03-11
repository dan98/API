#RatingAPI

REST API that helps you building ELO based rating systems. Elo's rating system is used in a lot of sports, from chess to football and teniss to order the participants by their rating. The rating of objects in Elo's rating change after each game, based on the expected outcome of the winner and loser. Read more on wikipedia.

##Dependecies
* Use apache with php5.4+, preferred php5.5.
* Memcached version 2.x
* Http extension version 1.7.x

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
$api->createObject(array(
  'name'=> '' // string[256]
  'image_url'=> '' // string[1000]
  'score'=> 1400 // Default: 1400
  'wins'=> 0 // Default: 0
  'loses'=> 0 // Default: 0
));
```
The object property `updated_time`, will update every time object participates in a battle. Property `created_time` is set when the object is created.

The main two options when creating a battle is `winner` and `loser`.
```php
$api->createBattle($winner_id, $loser_id);
// Also, as parameters, you can pass objects:
$api->createBattle($winner, $loser);
```
Both, `createObject` and `createBattle`, return RatingObject or RatingBattle when called.











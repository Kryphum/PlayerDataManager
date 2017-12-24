# PlayerDataManager
This is a plugin for PocketMine servers that is really just a small API for developers to store data in a central place throughout the server. PDM allows "registering" players as instances of `PDMPlayer` and adding "properties" (`PDMProperty`) to them. You can then access PDM from any plugin and fetch any property from any player.
___
### Getting started
You can access PDM this way:
```php
\Thouv\PDM\PlayerDataManager::getInstance();
```
*The rest of this README will assume `$pdm` to be the above, `$player` to be an instance of `\pocketmine\Player`, and `$pdm_player` to be an instance of `PDMPlayer`.*
___
### Registering a player
You can register a player to PDM using `PlayerDataManager::registerPlayer()`. This accepts a `string` and returns an instance of `PDMPlayer` or `false` if the player already exists. You can override this by setting `$reset_if_exists` to `true`, which will re-register the player (and therefore reset its properties) Example:
```php
$pdm->registerPlayer($player->getName());
// will reset the above instance of PDMPlayer
$pdm->registerPlayer($player->getName(), true);
```
___
### Fetching a player
You can fetch a player using `PlayerDataManager::getPlayer()` which accepts a `string` and returns a `PDMPlayer` or `false` if the player doesn't exist.
```php
var_dump($pdm->getPlayer($player->getName()); // object(PDMPlayer)#1 (1) { ["properties"]=> array(0) { } }
```
___
### Adding properties
You can add properties to a player using `PDMPlayer::setProperties()`. This accepts an `array` of instances of `PDMProperty` and returns the player's properties (an `array` of `PDMProperty`). You can create such array through `PDMPropertyFactory::makeProperties()`, which accepts an `array` consisting of the property's name as the key and the property's value as the value, or `[PDMPropertyFactory::makeProperty()]`, which accepts a `string` `$property_name` and a `$value`.
```php
$property = PDMPropertyFactory::makeProperty("last_join", time());
$pdm->registerPlayer($player->getName())->setProperties([$property]);

$properties_ar = ["friends" => ["KateeX", "Caj2003", "BartonMC"], "enemies" => ["Queen_Amanda16"], "arch_enemy" => "LoganTDM2514", "last_join" => time() + 60 * 60 * 8]; // i dont know why you would want to set the last join value to 8h ahead but it works for these purposes
$properties = PDMPropertyFactory::makeProperties($properties_ar);
$pdm->registerPlayer($player->getName())->setProperties($properties); // this will set the friends, enemies, and arch_enemy properties but skip over last_join as it has already been set and log a warning to the console

$pdm->registerPlayer($player)->setProperties($properties, true) // this will update all properties as they have all been already said (although it will keep friends, enemies, and arch_enemy seemingly unaffected as their values have not changed since they were set. it will log a notice to the console for each of them
```
___
### Fetching properties
You can fetch all of a player's propeties using `PDMPlayer::getProperties()`, which returns an array of `PDMProperty`, or a specific one with `PDMPlayer::getProperty()`, which accepts a `string`, the property's name, and returns an instance of `PDMProperty` or `false` if it doesn't exist.
```php
$properties = $pdm_player->getProperties();
vardump($properties["enemies"]); // object(PDMProperty)#1 (2) { ["property_name"]=> string(7) "enemies" ["value"]=> array(1) { [0]=> string(14) "Queen_Amanda16" } }

$property = $pdm_player->getProperty("arch_enemy");
echo $property->getPropertyName() . ":" . $property->getValue(); // arch_enemy:LoganTDM2514

echo $pdm_player->getProperty("hi_im_a_property_but_i_dont_actually_exist_so_shhhh")->getValue(); // FATAL ERROR Uncaught Error: Call to a member function getValue() on boolean
```
___
### Updating and deleting properties
You can update a property using `PDMPlayer::updateProperty()` which accepts a `PDMProperty` as the property and returns the property that was passed to the method or `false` if it doesn't exist. You can override this by setting `$set_if_nonexistent` to `true`, which will set the property if it doesn't already exist. You can unset a player's properties using `PDMPlayer::unsetProperties()`, which accepts an `array` of `string` as the properties' names.
```php
$pdm_player->updateProperty(PDMPropertyFactory::makeProperty("last_join", time() + 60 * 60 * 8));

$property = PDMPropertyFactory::makeProperty("last_block_break", time());
$pdm_player->updateProperty($property); // returns false and logs a warning to the console
$pdm_player->updateProperty($property, true); // sets last_block_break and logs a notice to the console

$pdm_player->unsetProperties(["friends"]);
```
___
### Unregistering a player
You can unregister a player using `PlayerDataManager::unregisterPlayer()` which accepts a `string` and returns `true` upon a successful unregistration (yes, I *am* aware that this isn't a word) or `false` if the player doesn't exist.
```php
$pdm->unregisterPlayer($player->getName()); // true
$pdm->unregisterPlayer($player->getName()) // false (we already unregistered the player above)
```

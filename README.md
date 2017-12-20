# PlayerDataManager
This is a plugin for PocketMine servers that is really just a small API for deveopers to store data in a central place. PDM allows "registering" players as instances of PDMPlayer and adding "properties" (PDMProperty) to them. You can them access PDM from any plugin and fetch any property from any player.
**NOTE** This is currently very simple and quite insignificant, but I have some cool features planned.
## Getting started
You can access PDM this way:
```php
\Thouv\PDM\PlayerDataManager::getInstance();
```
The rest of this README will assume `$pdm` to be the above, `$player` to be an instance of `\pocketmine\Player`, and `$pdm_player` to be an instance of `PDMPlayer`.
## Registering a player
You can register a player to PDM using `PlayerDataManager::registerNewPlayer()`. This accepts either a `string` or an instance of `\pocketmine\Player` and returns an instance of `PDMPlayer`. Example:
```php
$pdm->registerNewPlayer($player);

$pdm->registerNewPlayer($player->getName());
```
## Getting a player
You can get a player using `PlayerDataManager::getPlayer()` which accepts a `string` as the player's name and returns a `PDMPlayer`.
```php
vardump($pdm->getPlayer($player->getName()); // object(PDMPlayer)#1 (1) { ["properties"]=> array(0) { } }
```
## Adding properties
You can add properties to a player using `PDMPlayer::setProperties()`. This accepts an `array` of instances of `PDMProperty`. You can create such array through `PDMPropertyFactory::makeProperties()`, which accepts an `array` consisting of the property's name as the key and the property's value as the value, or `[PDMPropertyFactory::makeProperty()]`, which accepts a `string` `$property_name` and a `$value`.
```php
$property = PDMPropertyFactory::makeProperty("last_attack", time());
$pdm->registerNewPlayer($player)->setProperties([$property]);

$properties_ar = ["friends" => ["KateeX", "Caj2003", "BartonMC"], "enemies" => ["Queen_Amanda16"], "arch_enemy" => "LoganTDM2514"];
$properties = PDMPropertyFactory::makeProperties(properties_ar);
$pdm->registerNewPlayer($player)->setProperties($properties);
```
## Fetching properties
You can fetch all of a player's propeties using `PDMPlayer::getProperties()`, which returns an array of `PDMProperty`, or a specific one with `PDMPlayer::getProperty()`, which accepts a `string`, the property's name, and returns an instance of `PDMProperty`.
```php
$properties = $pdm_player->getProperties();
vardump($properties["enemies"]); // object(PDMProperty)#1 (2) { ["property_name"]=> string(7) "enemies" ["value"]=> array(1) { [0]=> string(14) "Queen_Amanda16" } }

$property = $pdm_player->getProperty("arch_enemy");
echo $property->getPropertyName() . ":" . $property->getValue(); // arch_enemy:LoganTDM2514
```
## Updating and deleting properties
You can update a property using `PDMPlayer::updateProperty()` which accepts a `string` as the property's name and a `PDMProperty` as the property. You can unset a player's properties using `PDMPlayer::unsetProperties()`, which accepts an `array` of `string` as the properties' names.
```php
$pdm_player->updateProperty("last_attack", PDMPropertyFactory::makeProperty("last_attack", time() - 1800));

$pdm_player->unsetProperty("friends");
```

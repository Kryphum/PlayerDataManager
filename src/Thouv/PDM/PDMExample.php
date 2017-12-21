<?php

// THIS IS NOT AN ACTUAL WORKING EXAMPLE

use \Thouv\PDM\PDMPropertyFactory;

$pdm = \Thouv\PDM\PlayerDataManager::getInstance();

public function onFactionJoin(\My\Faction\Plugin\FactionJoinEvent $ev)
{
	$properties = PDMPropertyFactory::makeProperties(["faction_name" => $ev->getFaction()->getName(), "faction_rank" => 0]); // 0 is trainee or whatever
	$pdm->registerPlayer($ev->getPlayer())->setProperties($properties);
}

public function onFactionPromote(\My\Faction\Plugin\FactionPromoteEvent $ev)
{
	$player = $pdm->getPlayer($ev->getPromoted()->getName());
	$old_rank = $player->getProperty("faction_rank");
	$player->updateProperty("faction_rank", PropertyFactory::makeProperty("faction_rank", $old_rank++));
}

public function onFactionLeave(\My\Faction\Plugin\FactionLeaveEvent $ev)
{
	$pdm->getPlayer($ev->getPlayer()->getName())->unsetProperties(["faction_name", "faction_rank"]);
}
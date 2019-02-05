<?php

namespace SmartLord\SlashServer;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;

use SmartLord\SlashServer\Commands \{
    SlashServerCommand, ServerCommand
};

class Main extends PluginBase
{

    public const VERSION = "1.0";

    public $cfg, $players = [];

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig()->getAll();
        $this->getServer()->getCommandMap()->register("slashserver", new SlashServerCommand($this));
        foreach ($this->cfg['servers'] as $name => $server) {
            $this->getServer()->getCommandMap()->register($server["command"], new ServerCommand($this, (string)$name, (string)$server["command"], (string)$server["address"], (int)$server["port"], (string)$server["description"], (array)$server["aliases"]));
        }

        $this->getLogger()->info("Enabled.");
    }

    public function transferPlayer(Player $player, string $serverName, string $address, int $port, int $second = 0)
    {
        if ($second != 0) {
            $this->getScheduler()->scheduleRepeatingTask(new TransferTask($this, $player, $serverName, $address, $port, $second), 20);
        } else {
            $player->transfer($address, $port);
            $this->getServer()->broadcastMessage(str_replace(["&", "%PLAYER%", "%SERVER%"], ["§", $player->getName(), $serverName], $this->cfg["player-transfer-message"]));

        }
    }

    public function reload()
    {
        $this->reloadConfig();
        $this->cfg = $this->getConfig()->getAll();
        foreach ($this->cfg['servers'] as $name => $server) {
            $this->getServer()->getCommandMap()->register($server["command"], new ServerCommand($this, (string)$name, (string)$server["command"], (string)$server["address"], (int)$server["port"], (string)$server["description"], (array)$server["aliases"]));
        }
    }
}
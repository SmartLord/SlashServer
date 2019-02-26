<?php

declare(strict_types = 1);

namespace SmartLord\SlashServer;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use SmartLord\SlashServer\Commands \{
    SlashServerCommand, ServersCommand, ServerCommand
};

class Main extends PluginBase
{

    public const VERSION = "1.1.0";

    public $cfg, $players = [];

    private $registeredServers = array();

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig()->getAll();

        if ($this->checkConfig()) {
            $this->getLogger()->alert("Please fix the config and reload the plugin");
        } else {
            $this->registerServers();
        }

        $this->getServer()->getCommandMap()->register("slashserver", new SlashServerCommand($this));

        if ($this->cfg["menu"]["enabled"])
            $this->getServer()->getCommandMap()->register("servers", new ServersCommand($this));

        $this->getLogger()->info("Enabled.");
    }

    public function sendServersMenu(Player $player) : void
    {
        $list = $this->getRegisteredServers();

        if (count($list) === 0) {
            $player->sendMessage(TextFormat::RED . "Servers not found");
            return;
        }
        
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        
        $form = $formapi->createSimpleForm(function (Player $player, int $data = null){
            if ($data === null) return;
            
            $name = $this->getRegisteredServers()[$data];
            if ($this->cfg["transfer-timer"]["enabled"]) {
                $player->sendMessage(str_replace(["&", "%PLAYER%", "%SERVER%", "%SECOND%"], ["§", $player->getName(), $name, $this->cfg["transfer-timer"]["second"]], $this->cfg["transfer-timer"]["message"]));
                $this->transferPlayer($player, $name, (string)$this->cfg['servers'][$name]["address"], (int)$this->cfg['servers'][$name]["port"], (int)$this->cfg["transfer-timer"]["second"]);
            } else {
                $this->transferPlayer($player, $name, (string)$this->cfg['servers'][$name]["address"], (int)$this->cfg['servers'][$name]["port"]);
            }
        });
                                          
        $form->setTitle($this->cfg["menu"]["title"]);
        foreach ($list as $name) {
            $form->addButton($name);
        }
        $form->sendToPlayer($player);
    }

    public function checkConfig() : bool
    {
        $error = false;
        foreach ($this->cfg['servers'] as $name => $server) {
            if (!isset($server["command"])) {
                $this->getLogger()->error("Command was not found in $name");
                $error = true;
            } else if (!isset($server["address"])) {
                $this->getLogger()->error("Address was not found in $name");
                $error = true;
            } else if (!isset($server["port"])) {
                $this->getLogger()->error("Port was not found in $name");
                $error = true;
            } else if (!isset($server["description"])) {
                $this->getLogger()->error("Description was not found in $name");
                $error = true;
            }
        }
        return $error;
    }

    public function registerServers() : void
    {
        $this->registeredServers = array();

        foreach ($this->cfg['servers'] as $name => $server) {
            if (isset($server["aliases"]))
                $aliases = (array)$server["aliases"];
            else
                $aliases = array();
            $this->getServer()->getCommandMap()->register($server["command"], new ServerCommand($this, (string)$name, (string)$server["command"], (string)$server["address"], (int)$server["port"], (string)$server["description"], $aliases));
            array_push($this->registeredServers, $name);
        }
    }

    public function getRegisteredServers() : array
    {
        return $this->registeredServers;
    }

    public function transferPlayer(Player $player, string $server, string $address, int $port, int $second = 0) : void
    {
        if (!$player->hasPermission("slashserver." . strtolower($this->name))) {
            $player->sendMessage(TextFormat::RED . "You do not have permission to transfer to this server");
            return;
        }

        if ($second != 0) {
            $this->getScheduler()->scheduleRepeatingTask(new TransferTask($this, $player, $server, $address, $port, $second), 20);
        } else {
            $player->transfer($address, $port);
            $this->getServer()->broadcastMessage(str_replace(["&", "%PLAYER%", "%SERVER%"], ["§", $player->getName(), $server], $this->cfg["player-transfer-message"]));
        }
    }

    public function reload() : bool
    {
        $this->reloadConfig();
        $this->cfg = $this->getConfig()->getAll();
        if ($this->checkConfig()) {
            $this->getLogger()->alert("Please fix the config and reload the plugin");
            return false;
        }
        $this->registerServers();
        return true;
    }
}

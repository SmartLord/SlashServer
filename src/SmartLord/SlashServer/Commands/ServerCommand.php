<?php

namespace SmartLord\SlashServer\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use SmartLord\SlashServer\Main;

class ServerCommand extends Command
{
    private $plugin, $name, $address, $port;

    public function __construct(Main $plugin, string $name, string $command, string $address, int $port, string $description, array $aliases = [])
    {
        parent::__construct($command, $description, "/$command", $aliases);
        $this->plugin = $plugin;
        $this->name = $name;
        $this->address = $address;
        $this->port = $port;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "You must run this command in-game");
            return true;
        }

        if (!$sender->hasPermission("slashserver." . strtolower($this->name))) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to run this command");
            return true;
        }

        if ($this->plugin->cfg["transfer-timer"]["enabled"]) {
            $sender->sendMessage(str_replace(["&", "%PLAYER%", "%SERVER%", "%SECOND%"], ["§", $sender->getName(), $this->name, $this->plugin->cfg["transfer-timer"]["second"]], $this->plugin->cfg["transfer-timer"]["message"]));
            $this->plugin->transferPlayer($sender, $this->name, $this->address, $this->port, (int)$this->plugin->cfg["transfer-timer"]["second"]);
        } else {
            $this->plugin->transferPlayer($sender, $this->name, $this->address, $this->port);
        }
        return true;
    }
}

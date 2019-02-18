<?php

namespace SmartLord\SlashServer\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use SmartLord\SlashServer\Main;

class ServersCommand extends Command
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("servers", "Open servers menu", "/servers", []);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "You must run this command in-game");
            return;
        }

        if (!$sender->hasPermission("slashserver.command.servers")) {
            $sender->sendMessage(TextFormat::RED . " You do not have permission to use this command");
            return;
        }

        $this->plugin->sendServersMenu($sender);
    }
}

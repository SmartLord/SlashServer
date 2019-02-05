<?php

namespace SmartLord\SlashServer\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use SmartLord\SlashServer\Main;

class SlashServerCommand extends Command
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("slashserver", "SlashServer main commands", "/slashserver", ["ss"]);

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            switch (strtolower($args[0])) {
                case "reload":
                    if ($sender->hasPermission("slashserver.reload")) {
                        $this->plugin->reload();
                        $sender->sendMessage(TextFormat::BLUE . "SlashServer" . TextFormat::DARK_GRAY . " >" . TextFormat::GREEN . " The plugin successfully reloaded!");

                    } else {
                        $sender->sendMessage(TextFormat::BLUE . "SlashServer" . TextFormat::DARK_GRAY . " >" . TextFormat::RED . " You do not have permission to use this command");
                    }
                    break;
                case "info":
                    $sender->sendMessage(TextFormat::DARK_GRAY . "-=-=-=" . TextFormat::BLUE . " SlashServer " . TextFormat::DARK_GRAY . "=-=-=-");
                    $sender->sendMessage(TextFormat::DARK_GRAY . ">" . TextFormat::YELLOW . " Version: " . Main::VERSION);
                    $sender->sendMessage(TextFormat::DARK_GRAY . ">" . TextFormat::YELLOW . " Author: SmartLord");
                    break;
                default:
                    $sender->sendMessage(TextFormat::DARK_GRAY . "-=-=-=" . TextFormat::BLUE . " SlashServer " . TextFormat::DARK_GRAY . "=-=-=-");
                    $sender->sendMessage(TextFormat::YELLOW . "/slashserver reload -> Reload the config");
                    $sender->sendMessage(TextFormat::YELLOW . "/slashserver info -> Plugin information");
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::DARK_GRAY . "-=-=-=" . TextFormat::BLUE . " SlashServer " . TextFormat::DARK_GRAY . "=-=-=-");
            $sender->sendMessage(TextFormat::YELLOW . "/slashserver reload -> Reload the config");
            $sender->sendMessage(TextFormat::YELLOW . "/slashserver info -> Show information about this plugin");
        }
    }
}
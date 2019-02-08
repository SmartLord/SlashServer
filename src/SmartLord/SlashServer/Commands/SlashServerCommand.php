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
                    if ($sender->hasPermission("slashserver.command.reload")) {
                        if ($this->plugin->reload())
                            $sender->sendMessage(TextFormat::BLUE . "SlashServer" . TextFormat::DARK_GRAY . " >" . TextFormat::GREEN . " The plugin successfully reloaded!");
                        else
                            $sender->sendMessage(TextFormat::BLUE . "SlashServer" . TextFormat::DARK_GRAY . " >" . TextFormat::RED . " There is a Problem! Please check the console");
                    } else {
                        $sender->sendMessage(TextFormat::BLUE . "SlashServer" . TextFormat::DARK_GRAY . " >" . TextFormat::RED . " You do not have permission to use this command");
                    }
                    break;
                case "list":
                    if ($sender->hasPermission("slashserver.command.list")) {
                        $list = $this->plugin->getRegisteredServers();
                        if (count($list) === 0) {
                            $sender->sendMessage(TextFormat::RED . "Servers not found");
                        } else {
                            $sender->sendMessage(TextFormat::YELLOW . "Servers:");
                            foreach ($list as $name) {
                                $sender->sendMessage(TextFormat::DARK_GRAY . "> " . TextFormat::AQUA . $name);
                            }
                        }
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
                    $sender->sendMessage(TextFormat::YELLOW . "/slashserver reload " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " Reload the config");
                    $sender->sendMessage(TextFormat::YELLOW . "/slashserver list " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " List of registered servers");
                    $sender->sendMessage(TextFormat::YELLOW . "/slashserver info " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " Plugin information");
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::DARK_GRAY . "-=-=-=" . TextFormat::BLUE . " SlashServer " . TextFormat::DARK_GRAY . "=-=-=-");
            $sender->sendMessage(TextFormat::YELLOW . "/slashserver reload " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " Reload the config");
            $sender->sendMessage(TextFormat::YELLOW . "/slashserver list " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " List of registered servers");
            $sender->sendMessage(TextFormat::YELLOW . "/slashserver info " . TextFormat::DARK_GRAY . "->" . TextFormat::YELLOW . " Plugin information");
        }
    }
}

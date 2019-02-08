<?php

namespace SmartLord\SlashServer;

use pocketmine\scheduler\Task;
use pocketmine\Player;

class TransferTask extends Task
{
    private $plugin, $player, $serverName, $address, $port, $second;

    public function __construct(Main $plugin, Player $player, string $serverName, string $address, int $port, int $second)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->serverName = $serverName;
        $this->address = $address;
        $this->port = $port;
        $this->second = $second;
    }

    public function onRun(int $tick)
    {
        $this->second--;
        if ($this->second === 0) {
            $this->plugin->transferPlayer($this->player, $this->serverName, $this->address, $this->port);
            $this->plugin->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}

<?php
namespace jasonwynn10\PermMgr\commands;

use jasonwynn10\PermMgr\ThePermissionManager;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class ListUserPermissions extends PluginCommand {
	/**
	 * ListUserPermissions constructor.
	 *
	 * @param ThePermissionManager $plugin
	 */
	public function __construct(ThePermissionManager $plugin) {
		parent::__construct($plugin->getLanguage()->get("listuserpermission.name"), $plugin);
		$this->setPermission("PermManager.command.listuserpermission");
		$this->setUsage($plugin->getLanguage()->get("listuserpermission.usage"));
		$this->setAliases([$plugin->getLanguage()->get("listuserpermission.usage")]);
		$this->setDescription($plugin->getLanguage()->get("listuserpermission.desc"));
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		parent::execute($sender, $commandLabel, $args);
		if(empty($args)) {
			return false;
		}
		$player = $this->getPlugin()->getServer()->getPlayer($args[0]);
		if($player instanceof Player) {
			$permissions = [];
			foreach($this->getPlugin()->perms[$player->getId()]->getPermissions() as $permission => $bool) {
				if($bool)
					$permissions[] = $permission;
			}
			sort($permissions,SORT_FLAG_CASE | SORT_NATURAL);
			foreach($permissions as $permission) {
				$sender->sendMessage(TextFormat::GREEN.$this->getPlugin()->getLanguage()->translateString("listuserpermission.list", [$permission]));
			}
		} else {
			$sender->sendMessage(TextFormat::DARK_RED.$this->getPlugin()->getLanguage()->translateString("playeroffline", [$args[0]]));
		}
		return true;
	}

	/**
	 * @return ThePermissionManager
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}
}
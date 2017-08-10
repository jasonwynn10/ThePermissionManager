<?php
namespace jasonwynn10\PermMgr\commands;

use jasonwynn10\PermMgr\ThePermissionManager;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class ListGroupPermissions extends PluginCommand {
	/**
	 * ListGroupPermissions constructor.
	 *
	 * @param ThePermissionManager $plugin
	 */
	public function __construct(ThePermissionManager $plugin) {
		parent::__construct($plugin->getLanguage()->get("listgrouppermissions.name"), $plugin);
		$this->setPermission("PermManager.command.listgrouppermissions");
		$this->setUsage($plugin->getLanguage()->get("listgrouppermissions.usage"));
		$this->setAliases([$plugin->getLanguage()->get("listgrouppermissions.alias")]);
		$this->setDescription($plugin->getLanguage()->get("listgrouppermissions.desc"));
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return true;
		}
		if(empty($args)) {
			return false;
		}
		$group = $args[0];
		if(!in_array(array_keys($this->getPlugin()->getGroups()->getAll()), $group)) {
			$sender->sendMessage(TextFormat::DARK_RED.$this->getPlugin()->getLanguage()->translateString("invalidgroup", [$group]));
			return true;
		}
		$permissions = [];
		foreach($this->getPlugin()->getGroups()->getNested($group."permissions", []) as $permission) {
			if($this->getPlugin()->sortPermissionConfigStrings($permission)) {
				$permissions[] = $permission;
			}
		}
		sort($permissions, SORT_NATURAL | SORT_FLAG_CASE);
		foreach($permissions as $permission) {
			$sender->sendMessage(TextFormat::GREEN.$this->getPlugin()->getLanguage()->translateString("listgrouppermissions.list", [$permission]));
		}
		return true;
	}

	/**
	 * @return ThePermissionManager
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function generateCustomCommandData(Player $player) : array {
		$commandData = parent::generateCustomCommandData($player);
		$groups = [];
		foreach($this->getPlugin()->getGroups()->getAll() as $group => $data) {
			$groups[] = $group;
		}
		$worlds = [];
		foreach($this->getPlugin()->getServer()->getLevels() as $level) {
			if(!$level->isClosed()) {
				$worlds[] = $level->getName();
			}
		}
		$commandData["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "group",
				"type" => "stringenum",
				"optional" => false,
				"enumtext" => $groups
			],
			[
				"name" => "world",
				"type" => "stringenum",
				"optional" => true,
				"enum_values" => $worlds
			]
		];
		$commandData["permission"] = $this->getPermission();
		return $commandData;
	}
}
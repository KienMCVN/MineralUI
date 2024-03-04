<?php
namespace KienMC\Mineral;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\command\{Command, CommandSender, CommandExecutor};
use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\{ItemBlock, Item, ItemTypeIds, StringToItemParser, LegacyStringToItemParser, LegacyStringToItemParserException};
use KienMC\Mineral\FormAPI\{Form, FormAPI, SimpleForm, CustomForm, ModalForm};
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;

class Main extends PluginBase implements Listener{

	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
    	$this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
    	$mineralFolder=$this->getDataFolder() . "mineral/";
		if (!is_dir($mineralFolder)){
			mkdir($mineralFolder);
		}
	}

	public $economyProvider;

	public function getEconomyProvider(){
		return $this->economyProvider;
	}

	public $cfg;

	public function onJoin(PlayerJoinEvent $ev){
		$player=$ev->getPlayer();
		$name=$player->getName();
		if(!file_exists($this->getDataFolder()."mineral/".$name.".yml")){
			$mineralFolder=$this->getDataFolder() . "mineral/";
			if (!is_dir($mineralFolder)){
				mkdir($mineralFolder);
			}
			$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
			$this->cfg->set("stone",0);
			$this->cfg->set("coal",0);
			$this->cfg->set("iron",0);
			$this->cfg->set("gold",0);
			$this->cfg->set("redstone",0);
			$this->cfg->set("lapis",0);
			$this->cfg->set("emerald",0);
			$this->cfg->set("diamond",0);
			$this->cfg->save();
		}
	}

	public function onBreak(BlockBreakEvent $ev){
		$player=$ev->getPlayer();
		$name=$player->getName();
		$mineralFolder=$this->getDataFolder() . "mineral/";
		if (!is_dir($mineralFolder)){
			mkdir($mineralFolder);
		}
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		if($ev->isCancelled()) return;
		$block=$ev->getBlock();
		$id=$block->getTypeId();
		if($id==BlockTypeIds::STONE || $id==BlockTypeIds::COBBLESTONE){
			$drops=$ev->getDrops();
			$level = $player->getInventory()->getItemInHand()->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(18)) + 1;
			$count=mt_rand(1, $level);
			$data=$this->cfg->get("stone");
			$newdata=(int)($data+$count);
			$this->cfg->set("stone", (int)($newdata));
			$this->cfg->save();
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::COAL_ORE || $id==BlockTypeIds::DEEPSLATE_COAL_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("coal");
				$newdata=(int)($data+$count);
				$this->cfg->set("coal", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::IRON_ORE || $id==BlockTypeIds::DEEPSLATE_IRON_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("iron");
				$newdata=(int)($data+$count);
				$this->cfg->set("iron", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::GOLD_ORE || $id==BlockTypeIds::DEEPSLATE_GOLD_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("gold");
				$newdata=(int)($data+$count);
				$this->cfg->set("gold", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::REDSTONE_ORE || $id==BlockTypeIds::DEEPSLATE_REDSTONE_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("redstone");
				$newdata=(int)($data+$count);
				$this->cfg->set("redstone", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::LAPIS_LAZULI_ORE || $id==BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("lapis");
				$newdata=(int)($data+$count);
				$this->cfg->set("lapis", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::EMERALD_ORE || $id==BlockTypeIds::DEEPSLATE_EMERALD_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("emerald");
				$newdata=(int)($data+$count);
				$this->cfg->set("emerald", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
		if($id==BlockTypeIds::DIAMOND_ORE || $id==BlockTypeIds::DEEPSLATE_DIAMOND_ORE){
			$drops=$ev->getDrops();
			foreach($drops as $item){
				$count=$item->getCount();
				$data=$this->cfg->get("diamond");
				$newdata=(int)($data+$count);
				$this->cfg->set("diamond", (int)($newdata));
				$this->cfg->save();
			}
			$ev->setDrops([]);
		}
	}

	public function onCommand(CommandSender $player, Command $cmd, string $label, array $args): bool{
		$name=$cmd->getName();
		if($name=="mineral"){
			if(!$player instanceof Player){
				$player->sendMessage("Use Command In Game");
				return true;
			}
			$this->menu($player);
			return true;
		}
		return true;
	}

	public function menu($player){
		$form=new SimpleForm(function(Player $player, $data){
			if($data==null) return;
			switch($data){
				case 0:
				break;
				case 1:
				$this->menuSell($player);
				break;
				case 2:
				$this->menuWithraw($player);
				break;
				case 3:
				$this->menuStore($player);
				break;
			}
		});
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$stone=$this->cfg->get("stone");
		$coal=$this->cfg->get("coal");
		$iron=$this->cfg->get("iron");
		$gold=$this->cfg->get("gold");
		$redstone=$this->cfg->get("redstone");
		$lapis=$this->cfg->get("lapis");
		$emerald=$this->cfg->get("emerald");
		$diamond=$this->cfg->get("diamond");
		$form->setTitle("Mineral");
		$form->setContent("- Your Stone: ".$stone."\n- Your Coal: ".$coal."\n- Your Iron: ".$iron."\n- Your Gold: ".$gold."\n- Your Redstone: ".$redstone."\n- Your Lapis: ".$lapis."\n- Your Emerald: ".$emerald."\n- Your Diamond: ".$diamond."\n");
		$form->addButton("Exit");
		$form->addButton("Sell Ore");
		$form->addButton("Withraw Ore");
		$form->addButton("Store Ore");
		$form->sendToPlayer($player);
	}

	public function menuSell($player){
		$form=new SimpleForm(function(Player $player, $data){
			if($data==0){
				$this->menu($player);
				return;
			}
			switch($data){
				case 1:
				$this->sellAll($player);
				break;
				case 2;
				$this->customSell($player);
				break;
			}
		});
		$form->setTitle("Mineral");
		$form->addButton("Back");
		$form->addButton("Sell All");
		$form->addButton("Custom Sell");
		$form->sendToPlayer($player);
	}

	public function sellAll($player){
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$stone=$this->cfg->get("stone");
		$coal=$this->cfg->get("coal");
		$iron=$this->cfg->get("iron");
		$gold=$this->cfg->get("gold");
		$redstone=$this->cfg->get("redstone");
		$lapis=$this->cfg->get("lapis");
		$emerald=$this->cfg->get("emerald");
		$diamond=$this->cfg->get("diamond");
		$stonesell=$this->getConfig()->get("stone-sell");
		$coalsell=$this->getConfig()->get("coal-sell");
		$ironsell=$this->getConfig()->get("iron-sell");
		$goldsell=$this->getConfig()->get("gold-sell");
		$redstonesell=$this->getConfig()->get("redstone-sell");
		$lapissell=$this->getConfig()->get("lapis-sell");
		$emeraldsell=$this->getConfig()->get("emerald-sell");
		$diamondsell=$this->getConfig()->get("diamond-sell");
		$total=0;
		if($stone>0){
			$this->cfg->set("stone", 0);
			$this->cfg->save();
			$money=(float)($stonesell*$stone);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($coal>0){
			$this->cfg->set("coal", 0);
			$this->cfg->save();
			$money=(float)($coalsell*$coal);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($iron>0){
			$this->cfg->set("iron", 0);
			$this->cfg->save();
			$money=(float)($ironsell*$iron);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($gold>0){
			$this->cfg->set("gold", 0);
			$this->cfg->save();
			$money=(float)($goldsell*$gold);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($redstone>0){
			$this->cfg->set("redstone", 0);
			$this->cfg->save();
			$money=(float)($redstonesell*$redstone);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($lapis>0){
			$this->cfg->set("lapis", 0);
			$this->cfg->save();
			$money=(float)($lapissell*$lapis);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($emerald>0){
			$this->cfg->set("emerald", 0);
			$this->cfg->save();
			$money=(float)($emeraldsell*$emerald);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		if($diamond>0){
			$this->cfg->set("diamond", 0);
			$this->cfg->save();
			$money=(float)($diamondsell*$diamond);
			$this->getEconomyProvider()->giveMoney($player, $money);
			$total+=$money;
		}
		$form=new SimpleForm(function(Player $player, $data){
			if($data==0) $this->menu($player);
		});
		$form->setTitle("Mineral");
		if($total>0){
			$form->setContent("Sold Successfully, Received ".$total." Money");
		}else{
			$form->setContent("You Dont Have Anything To Sell");
		}
		$form->addButton("Back");
		$form->sendToPlayer($player);
	}

	public function customSell($player){
		$form=new CustomForm(function(Player $player,$data){
			if($data==null){
				$this->menuSell($player);
				return;
			}
			if(!isset($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			if(!is_numeric($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$data[2]=floor($data[2]);
			if($data[2]<1){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$name=$player->getName();
			$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
			$stone=$this->cfg->get("stone");
			$coal=$this->cfg->get("coal");
			$iron=$this->cfg->get("iron");
			$gold=$this->cfg->get("gold");
			$redstone=$this->cfg->get("redstone");
			$lapis=$this->cfg->get("lapis");
			$emerald=$this->cfg->get("emerald");
			$diamond=$this->cfg->get("diamond");
			$stonesell=$this->getConfig()->get("stone-sell");
			$coalsell=$this->getConfig()->get("coal-sell");
			$ironsell=$this->getConfig()->get("iron-sell");
			$goldsell=$this->getConfig()->get("gold-sell");
			$redstonesell=$this->getConfig()->get("redstone-sell");
			$lapissell=$this->getConfig()->get("lapis-sell");
			$emeraldsell=$this->getConfig()->get("emerald-sell");
			$diamondsell=$this->getConfig()->get("diamond-sell");
			switch($data[1]){
				case 0: //"Stone":
					if($data[2]>$stone){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($stone-$data[2]);
					$this->cfg->set("stone", $newdata);
					$this->cfg->save();
					$money=(float)($stonesell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 1:
					if($data[2]>$coal){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($coal-$data[2]);
					$this->cfg->set("coal", $newdata);
					$this->cfg->save();
					$money=(float)($coalsell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 2:
					if($data[2]>$iron){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($iron-$data[2]);
					$this->cfg->set("iron", $newdata);
					$this->cfg->save();
					$money=(float)($ironsell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 3:
					if($data[2]>$gold){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($gold-$data[2]);
					$this->cfg->set("gold", $newdata);
					$this->cfg->save();
					$money=(float)($goldsell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 4:
					if($data[2]>$redstone){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($redstone-$data[2]);
					$this->cfg->set("redstone", $newdata);
					$this->cfg->save();
					$money=(float)($redstonesell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 5:
					if($data[2]>$lapis){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($lapis-$data[2]);
					$this->cfg->set("lapis", $newdata);
					$this->cfg->save();
					$money=(float)($lapissell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 6:
					if($data[2]>$emerald){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($emerald-$data[2]);
					$this->cfg->set("emerald", $newdata);
					$this->cfg->save();
					$money=(float)($emeraldsell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				case 7:
					if($data[2]>$diamond){
						$player->sendMessage("You Dont Have Enough To Sell");
						return;
					}
					$newdata=(int)($diamond-$data[2]);
					$this->cfg->set("diamond", $newdata);
					$this->cfg->save();
					$money=(float)($diamondsell*$data[2]);
					$this->getEconomyProvider()->giveMoney($player, $money);
					$player->sendMessage("Sold Successfully, Received ".$money." Money");
					break;
				default:
					$player->sendMessage("Error");
					break;
			}
		});
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$stone=$this->cfg->get("stone");
		$coal=$this->cfg->get("coal");
		$iron=$this->cfg->get("iron");
		$gold=$this->cfg->get("gold");
		$redstone=$this->cfg->get("redstone");
		$lapis=$this->cfg->get("lapis");
		$emerald=$this->cfg->get("emerald");
		$diamond=$this->cfg->get("diamond");
		$form->setTitle("Mineral");
		$form->addLabel("- Your Stone: ".$stone."\n- Your Coal: ".$coal."\n- Your Iron: ".$iron."\n- Your Gold: ".$gold."\n- Your Redstone: ".$redstone."\n- Your Lapis: ".$lapis."\n- Your Emerald: ".$emerald."\n- Your Diamond: ".$diamond."\n");
		$form->addDropdown("Choose",["Stone",
									 "Coal",
									 "Iron",
									 "Gold",
									 "Redstone",
									 "Lapis",
									 "Emerald",
									 "Diamond"
									]);
		$form->addInput("Enter A Number:");
		$form->sendToPlayer($player);
	}

	public function menuWithraw($player){
		$form=new CustomForm(function(Player $player, $data){
			if($data==null){
				$this->menu($player);
				return;
			}
			if(!isset($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			if(!is_numeric($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$data[2]=ceil($data[2]);
			if($data[2]<1){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$name=$player->getName();
			$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
			switch($data[1]){
				case 0:
					$olddata=$this->cfg->get("stone");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("cobblestone");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$stone=$this->cfg->get("stone");
						$newdata=(int)($stone-($item->getCount()));
						$this->cfg->set("stone", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$stone=$this->cfg->get("stone");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("cobblestone");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($stone-($item->getCount()));
								$this->cfg->set("stone", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 1:
					$olddata=$this->cfg->get("coal");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("coal");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						
						$olddata=$this->cfg->get("coal");$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("coal", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("coal");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("coal");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("coal", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 2:
					$olddata=$this->cfg->get("iron");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("iron_ingot");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("iron");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("iron", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("iron");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("iron_ingot");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("iron", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 3:
					$olddata=$this->cfg->get("gold");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("gold_ingot");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("gold");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("gold", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("gold");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("gold_ingot");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("gold", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 4:
					$olddata=$this->cfg->get("redstone");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("redstone_dust");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("redstone");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("redstone", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("redstone");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("redstone_dust");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("redstone", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 5:
					$olddata=$this->cfg->get("lapis");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("lapis_lazuli");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("lapis");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("lapis", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("lapis");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("lapis_lazuli");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("lapis", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 6:
					$olddata=$this->cfg->get("emerald");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("emerald");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("emerald");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("emerald", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("emerald");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("emerald");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("emerald", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				case 7:
					$olddata=$this->cfg->get("diamond");
					if($data[2]>$olddata){
						$player->sendMessage("You Dont Have Enough To Withraw");
						return;
					}
					if($data[2]<=64){
						$inv=$player->getInventory();
						$item=StringToItemParser::getInstance()->parse("diamond");
						$item->setCount((int)($data[2]));
						if(!$inv->canAddItem($item)){
							$player->sendMessage("Your Inventory Is Full");
							return;
						}
						$inv->addItem($item);
						$olddata=$this->cfg->get("diamond");
						$newdata=(int)($olddata-($item->getCount()));
						$this->cfg->set("diamond", $newdata);
						$this->cfg->save();
						$player->sendMessage("Withrawn Successfully");
					}else{
						$count=$data[2];
						$stacks=ceil($count/64);
						for($i=0;$i<$stacks;$i++){
							$olddata=$this->cfg->get("diamond");
							$inv=$player->getInventory();
							$item=StringToItemParser::getInstance()->parse("diamond");
							if($count>=64){
								$item->setCount(64);
								$count-=64;
							}else{
								$item->setCount($count);
								$count-=$count;
							}
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								$newdata=(int)($olddata-($item->getCount()));
								$this->cfg->set("diamond", $newdata);
								$this->cfg->save();
							}
						}
						$player->sendMessage("Withrawn Successfully");
					}
					break;
				default:
					$player->sendMessage("Error");
					break;
			}
		});
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$stone=$this->cfg->get("stone");
		$coal=$this->cfg->get("coal");
		$iron=$this->cfg->get("iron");
		$gold=$this->cfg->get("gold");
		$redstone=$this->cfg->get("redstone");
		$lapis=$this->cfg->get("lapis");
		$emerald=$this->cfg->get("emerald");
		$diamond=$this->cfg->get("diamond");
		$form->setTitle("Mineral");
		$form->addLabel("- Your Stone: ".$stone."\n- Your Coal: ".$coal."\n- Your Iron: ".$iron."\n- Your Gold: ".$gold."\n- Your Redstone: ".$redstone."\n- Your Lapis: ".$lapis."\n- Your Emerald: ".$emerald."\n- Your Diamond: ".$diamond."\n");
		$form->addDropdown("Choose",["Stone",
									 "Coal",
									 "Iron",
									 "Gold",
									 "Redstone",
									 "Lapis",
									 "Emerald",
									 "Diamond"
									]);
		$form->addInput("Enter A Number:");
		$form->sendToPlayer($player);
	}

	public function menuStore($player){
		$form=new SimpleForm(function(Player $player, $data){
			if($data==0) $this->menu($player);
			switch($data){
				case 1:
				$this->storeAll($player);
				break;
				case 2:
				$this->customStore($player);
				break;
			}
		});
		$form->setTitle("Mineral");
		$form->addButton("Back");
		$form->addButton("Store All");
/**		$form->addButton("Custom Store"); **/
		$form->sendToPlayer($player);
	}

	public function storeAll($player){
		$inv=$player->getInventory();
		$contents=$inv->getContents();
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$total=0;
		foreach($contents as $slot => $item){
			if($item instanceof ItemBlock){
				$id=$item->getBlock()->getTypeId();
				if($id==BlockTypeIds::STONE || $id==BlockTypeIds::COBBLESTONE){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("stone");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("stone",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
			}
			if($item instanceof Item){
				$id=$item->getTypeId();
				if($id==ItemTypeIds::COAL){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("coal");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("coal",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::IRON_INGOT){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("iron");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("iron",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::GOLD_INGOT){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("gold");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("gold",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::REDSTONE_DUST){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("redstone");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("redstone",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::LAPIS_LAZULI){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("lapis");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("lapis",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::EMERALD){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("emerald");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("emerald",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
				if($id==ItemTypeIds::DIAMOND){
					$count=$item->getCount();
					$total+=$count;
					$olddata=$this->cfg->get("diamond");
					$newdata=(int)($olddata+$count);
					$this->cfg->set("diamond",$newdata);
					$this->cfg->save();
					$inv->clear($slot);
				}
			}
		}
		$player->sendMessage("Stored ".$total);
	}

/** public function customStore($player){
	 * I dont know how to code this, help me please
		$form=new CustomForm(function(Player $player, $data){
			if($data==null) $this->menuStore($player);
			if(!isset($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			if(!is_numeric($data[2])){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$data[2]=ceil($data[2]);
			if($data[2]<1){
				$player->sendMessage("Please Enter A Number Bigger Than 0");
				return;
			}
			$inv=$player->getInventory();
			$contents=$inv->getContents();
			$name=$player->getName();
			$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
			$amount=$data[2];
			switch($data[1]){
				case 0:
					$total=0;
					foreach($contents as $slot => $item){
						if($item instanceof ItemBlock){
							$id=$item->getBlock()->getTypeId();
							if($id==BlockTypeIds::STONE || $id==BlockTypeIds::COBBLESTONE && $total<$amount){
								$count=min($item->getBlock()->getCount(), $amount-$total);
								$total+=$count;
								$item->setCount($item->getBlock()->getCount-$count);
								$inv->setItem($slot, $item);
							}
						}
					}
					$olddata=$this->cfg->get("stone");
					$newdata=(int)($olddata+$total);
					$this->cfg->set("stone",$newdata);
					$this->cfg->save();
					$player->sendMessage("Stored ".$total);
					break;
				case 1:
					$total=0;
					foreach($contents as $slot => $item){
						if($item instanceof Item){
							$id=$item->getTypeId();
							if($id==ItemTypeIds::COAL && $total<$amount){
								$count=min($item->getCount(), $amount-$total);
								$total+=$count;
								$item->setCount($item->getCount-$count);
								$inv->setItem($slot, $item);
							}
						}
					}
					$olddata=$this->cfg->get("coal");
					$newdata=(int)($olddata+$total);
					$this->cfg->set("coal",$newdata);
					$this->cfg->save();
					$player->sendMessage("Stored ".$total);
					break;
				default:
					$player->sendMessage("Error");
					break;
			}
		});
		$name=$player->getName();
		$this->cfg=new Config($this->getDataFolder()."mineral/".$name.".yml",Config::YAML);
		$stone=$this->cfg->get("stone");
		$coal=$this->cfg->get("coal");
		$iron=$this->cfg->get("iron");
		$gold=$this->cfg->get("gold");
		$redstone=$this->cfg->get("redstone");
		$lapis=$this->cfg->get("lapis");
		$emerald=$this->cfg->get("emerald");
		$diamond=$this->cfg->get("diamond");
		$form->setTitle("Mineral");
		$form->addLabel("- Your Stone: ".$stone."\n- Your Coal: ".$coal."\n- Your Iron: ".$iron."\n- Your Gold: ".$gold."\n- Your Redstone: ".$redstone."\n- Your Lapis: ".$lapis."\n- Your Emerald: ".$emerald."\n- Your Diamond: ".$diamond."\n");
		$form->addDropdown("Choose",["Stone",
									 "Coal",
									 "Iron",
									 "Gold",
									 "Redstone",
									 "Lapis",
									 "Emerald",
									 "Diamond"
									]);
		$form->addInput("Enter A Number:");
		$form->sendToPlayer($player);
	}  **/

}

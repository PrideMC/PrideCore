<?php

/*
 *
 *       _____      _     _      __  __  _____
 *      |  __ \    (_)   | |    |  \/  |/ ____|
 *      | |__) | __ _  __| | ___| \  / | |
 *      |  ___/ '__| |/ _` |/ _ \ |\/| | |
 *      | |   | |  | | (_| |  __/ |  | | |____
 *      |_|   |_|  |_|\__,_|\___|_|  |_|\_____|
 *            A minecraft bedrock server.
 *
 *      This project and it’s contents within
 *     are copyrighted and trademarked property
 *   of PrideMC Network. No part of this project or
 *    artwork may be reproduced by any means or in
 *   any form whatsoever without written permission.
 *
 *  Copyright © PrideMC Network - All Rights Reserved
 *
 *  www.mcpride.tk                 github.com/PrideMC
 *  twitter.com/PrideMC         youtube.com/c/PrideMC
 *  discord.gg/PrideMC           facebook.com/PrideMC
 *               bit.ly/JoinInPrideMC
 *  #StandWithUkraine                     #PrideMonth
 *
 */

declare(strict_types=1);

namespace PrideCore\Utils;

use GdImage;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\world\particle\BlockBreakParticle;
use PrideCore\Player\Player;
use TypeError;

use function array_rand;
use function chr;
use function imagecolorat;
use function imagedestroy;
use function imagesx;
use function imagesy;
use function mt_rand;
use function preg_match;
use function preg_replace;
use function sizeof;
use function str_replace;
use function strlen;
use function ucwords;

class Utils
{

	public static array $profanity = [ // you can add more things here.
		"Bastard",
		"Beaver",
		"Beef curtains",
		"Bellend",
		"Bloodclaat",
		"Clunge",
		"Cock",
		"Dick",
		"Dickhead",
		"Fanny",
		"Flaps",
		"Gash",
		"Knob",
		"Minge",
		"Prick",
		"Punani",
		"Pussy",
		"Snatch",
		"Twat",
		"Arse",
		"Bloody",
		"Bugger",
		"Crap",
		"Damn",
		"God",
		"Goddam",
		"Jesus Christ",
		"Minger",
		"Sod-off",
		"Motherfucker",
		"Motherfuck",
		"Childfucker",
		"Kidsfucker",
		"Fuck",
		"Cunt",
		"Nigger",
		"Nigga",
		"Fucker",
		"Frick",
		"Dickweed",
		"Shit",
		"WTF",
		"gay",
		"tf",
		"fatass",
		"bitchass",
		"stupid",
		"stupido",
		"trans",
		"retard",
	];

	public static function lightningBolt(Player $player) : void
	{
		$pos = $player->getPosition();
		$light2 = AddActorPacket::create(Entity::getId(), Entity::nextRuntimeId(), 1, $player->getLocation()->asVector3(), "minecraft:lightning_bolt", $player->getPosition()->asVector3(), null, $player->getLocation()->getYaw(), $player->getLocation()->getPitch(), 0.0, 0.0, [], [], []);
		$block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
		$particle = new BlockBreakParticle($block);
		$player->getWorld()->addParticle($pos, $particle, $player->getWorld()->getPlayers());
		$sound2 = PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1);
		Server::getInstance()->broadcastPackets($player->getWorld()->getPlayers(), [$light2, $sound2]);
	}

	/**
	 * @return [type]
	 */
	public static function explotion(){}

	private const PREFIX_SUFFIX = [
		["The", "Gamer"],
		["xX", "Xx"],
		["MC", "YT"],
		["YT", "HD"],
		["The", "Playz"]
	];
	private const ADJECTIVES = [
		'abandoned', 'able', 'absolute', 'adorable', 'adventurous', 'academic', 'acceptable', 'acclaimed', 'accomplished',
		'accurate', 'acidic', 'acrobatic', 'active', 'actual', 'adept', 'admirable', 'admired', 'adorable', 'adored', 'advanced',
		'affectionate', 'aged', 'aggravating', 'aggressive', 'agile', 'agitated', 'agonizing', 'agreeable', 'alarmed',
		'alarming', 'alert', 'alienated', 'alive', 'all', 'altruistic', 'amazing', 'ambitious', 'ample', 'amused', 'amusing',
		'anchored', 'ancient', 'angelic', 'angry', 'anguished', 'animated', 'annual', 'another', 'antique', 'anxious', 'any',
		'apprehensive', 'appropriate', 'apt', 'arctic', 'arid', 'aromatic', 'artistic', 'ashamed', 'assured', 'astonishing',
		'athletic', 'attached', 'attentive', 'attractive', 'austere', 'authentic', 'authorized', 'automatic', 'avaricious',
		'average', 'aware', 'awesome', 'awful', 'awkward', 'babyish', 'bad', 'back', 'baggy', 'bare', 'barren', 'basic',
		'beautiful', 'belated', 'beloved', 'beneficial', 'better', 'best', 'bewitched', 'big', 'big-hearted', 'biodegradable',
		'bite-sized', 'bitter', 'black', 'black-and-white', 'bland', 'blank', 'blaring', 'bleak', 'blind', 'blissful', 'blond',
		'blue', 'blushing', 'bogus', 'boiling', 'bold', 'bony', 'boring', 'bossy', 'both', 'bouncy', 'bountiful', 'bowed', 'brave',
		'breakable', 'brief', 'bright', 'brilliant', 'brisk', 'bronze', 'brown', 'bruised', 'bubbly', 'bulky', 'bumpy', 'buoyant',
		'burdensome', 'burly', 'bustling', 'busy', 'buttery', 'buzzing', 'calculating', 'calm', 'candid', 'canine', 'capital',
		'carefree', 'careful', 'careless', 'caring', 'cautious', 'cavernous', 'celebrated', 'charming', 'cheap', 'cheerful',
		'cheery', 'chief', 'chilly', 'chubby', 'circular', 'classic', 'clean', 'clear', 'clear-cut', 'clever', 'close', 'closed',
		'cloudy', 'clueless', 'clumsy', 'cluttered', 'coarse', 'cold', 'colorful', 'colorless', 'colossal', 'comfortable',
		'common', 'compassionate', 'competent', 'complete', 'complex', 'complicated', 'composed', 'concerned', 'concrete',
		'confused', 'conscious', 'considerate', 'constant', 'content', 'conventional', 'cooked', 'cool', 'cooperative',
		'coordinated', 'corny', 'corrupt', 'costly', 'courageous', 'courteous', 'crafty', 'crazy', 'creamy', 'creative',
		'creepy', 'criminal', 'crisp', 'critical', 'crooked', 'crowded', 'cruel', 'crushing', 'cuddly', 'cultivated',
		'cultured', 'cumbersome', 'curly', 'curvy', 'cute', 'cylindrical', 'damaged', 'damp', 'dangerous', 'dapper', 'daring',
		'darling', 'dark', 'dazzling', 'dead', 'deadly', 'deafening', 'dear', 'dearest', 'decent', 'decimal', 'decisive', 'deep',
		'defenseless', 'defensive', 'defiant', 'deficient', 'definite', 'definitive', 'delayed', 'delectable', 'delicious',
		'delightful', 'delirious', 'demanding', 'dense', 'dental', 'dependable', 'dependent', 'descriptive', 'deserted',
		'detailed', 'determined', 'devoted', 'different', 'difficult', 'digital', 'diligent', 'dim', 'dimpled', 'dimwitted',
		'direct', 'disastrous', 'discrete', 'disfigured', 'disgusting', 'disloyal', 'dismal', 'distant', 'downright', 'dreary',
		'dirty', 'disguised', 'dishonest', 'dismal', 'distant', 'distinct', 'distorted', 'dizzy', 'dopey', 'doting', 'double',
		'downright', 'drab', 'drafty', 'dramatic', 'dreary', 'droopy', 'dry', 'dual', 'dull', 'dutiful', 'each', 'eager',
		'earnest', 'early', 'easy', 'easy-going', 'ecstatic', 'edible', 'educated', 'elaborate', 'elastic', 'elated', 'elderly',
		'electric', 'elegant', 'elementary', 'elliptical', 'embarrassed', 'embellished', 'eminent', 'emotional', 'empty',
		'enchanted', 'enchanting', 'energetic', 'enlightened', 'enormous', 'enraged', 'entire', 'envious', 'equal', 'equatorial',
		'essential', 'esteemed', 'ethical', 'euphoric', 'even', 'evergreen', 'everlasting', 'every', 'evil', 'exalted',
		'excellent', 'exemplary', 'exhausted', 'excitable', 'excited', 'exciting', 'exotic', 'expensive', 'experienced',
		'expert', 'extraneous', 'extroverted', 'extra-large', 'extra-small', 'fabulous', 'failing', 'faint', 'fair',
		'faithful', 'fake', 'false', 'familiar', 'famous', 'fancy', 'fantastic', 'far', 'faraway', 'far-flung', 'far-off',
		'fast', 'fat', 'fatal', 'fatherly', 'favorable', 'favorite', 'fearful', 'fearless', 'feisty', 'feline', 'female',
		'feminine', 'few', 'fickle', 'filthy', 'fine', 'finished', 'firm', 'first', 'firsthand', 'fitting', 'fixed', 'flaky',
		'flamboyant', 'flashy', 'flat', 'flawed', 'flawless', 'flickering', 'flimsy', 'flippant', 'flowery', 'fluffy', 'fluid',
		'flustered', 'focused', 'fond', 'foolhardy', 'foolish', 'forceful', 'forked', 'formal', 'forsaken', 'forthright',
		'fortunate', 'fragrant', 'frail', 'frank', 'frayed', 'free', 'French', 'fresh', 'frequent', 'friendly', 'frightened',
		'frightening', 'frigid', 'frilly', 'frizzy', 'frivolous', 'front', 'frosty', 'frozen', 'frugal', 'fruitful', 'full',
		'fumbling', 'functional', 'funny', 'fussy', 'fuzzy', 'gargantuan', 'gaseous', 'general', 'generous', 'gentle',
		'genuine', 'giant', 'giddy', 'gigantic', 'gifted', 'giving', 'glamorous', 'glaring', 'glass', 'gleaming', 'gleeful',
		'glistening', 'glittering', 'gloomy', 'glorious', 'glossy', 'glum', 'golden', 'good', 'good-natured', 'gorgeous',
		'graceful', 'gracious', 'grand', 'grandiose', 'granular', 'grateful', 'grave', 'gray', 'great', 'greedy', 'green',
		'gregarious', 'grim', 'grimy', 'gripping', 'grizzled', 'gross', 'grotesque', 'grouchy', 'grounded', 'growing',
		'growling', 'grown', 'grubby', 'gruesome', 'grumpy', 'guilty', 'gullible', 'gummy', 'hairy', 'half', 'handmade',
		'handsome', 'handy', 'happy', 'happy-go-lucky', 'hard', 'hard-to-find', 'harmful', 'harmless', 'harmonious', 'harsh',
		'hasty', 'hateful', 'haunting', 'healthy', 'heartfelt', 'hearty', 'heavenly', 'heavy', 'hefty', 'helpful', 'helpless',
		'hidden', 'hideous', 'high', 'high-level', 'hilarious', 'hoarse', 'hollow', 'homely', 'honest', 'honorable', 'honored',
		'hopeful', 'horrible', 'hospitable', 'hot', 'huge', 'humble', 'humiliating', 'humming', 'humongous', 'hungry', 'hurtful',
		'husky', 'icky', 'icy', 'ideal', 'idealistic', 'identical', 'idle', 'idiotic', 'idolized', 'ignorant', 'ill', 'illegal',
		'ill-fated', 'ill-informed', 'illiterate', 'illustrious', 'imaginary', 'imaginative', 'immaculate', 'immaterial',
		'immediate', 'immense', 'impassioned', 'impeccable', 'impartial', 'imperfect', 'imperturbable', 'impish', 'impolite',
		'important', 'impossible', 'impractical', 'impressionable', 'impressive', 'improbable', 'impure', 'inborn',
		'incomparable', 'incompatible', 'incomplete', 'inconsequential', 'incredible', 'indelible', 'inexperienced',
		'indolent', 'infamous', 'infantile', 'infatuated', 'inferior', 'infinite', 'informal', 'innocent', 'insecure',
		'insidious', 'insignificant', 'insistent', 'instructive', 'insubstantial', 'intelligent', 'intent', 'intentional',
		'interesting', 'internal', 'international', 'intrepid', 'ironclad', 'irresponsible', 'irritating', 'itchy', 'jaded',
		'jagged', 'jam-packed', 'jaunty', 'jealous', 'jittery', 'joint', 'jolly', 'jovial', 'joyful', 'joyous', 'jubilant',
		'judicious', 'juicy', 'jumbo', 'junior', 'jumpy', 'juvenile', 'kaleidoscopic', 'keen', 'key', 'kind', 'kindhearted',
		'kindly', 'klutzy', 'knobby', 'knotty', 'knowledgeable', 'knowing', 'known', 'kooky', 'kosher', 'lame', 'lanky', 'large',
		'last', 'lasting', 'late', 'lavish', 'lawful', 'lazy', 'leading', 'lean', 'leafy', 'left', 'legal', 'legitimate', 'light',
		'lighthearted', 'likable', 'likely', 'limited', 'limp', 'limping', 'linear', 'lined', 'liquid', 'little', 'live', 'lively',
		'livid', 'loathsome', 'lone', 'lonely', 'long', 'long-term', 'loose', 'lopsided', 'lost', 'loud', 'lovable', 'lovely',
		'loving', 'low', 'loyal', 'lucky', 'lumbering', 'luminous', 'lumpy', 'lustrous', 'luxurious', 'mad', 'made-up',
		'magnificent', 'majestic', 'major', 'male', 'mammoth', 'married', 'marvelous', 'masculine', 'massive', 'mature',
		'meager', 'mealy', 'mean', 'measly', 'meaty', 'medical', 'mediocre', 'medium', 'meek', 'mellow', 'melodic', 'memorable',
		'menacing', 'merry', 'messy', 'metallic', 'mild', 'milky', 'mindless', 'miniature', 'minor', 'minty', 'miserable',
		'miserly', 'misguided', 'misty', 'mixed', 'modern', 'modest', 'moist', 'monstrous', 'monthly', 'monumental', 'moral',
		'mortified', 'motherly', 'motionless', 'mountainous', 'muddy', 'muffled', 'multicolored', 'mundane', 'murky', 'mushy',
		'musty', 'muted', 'mysterious', 'naive', 'narrow', 'nasty', 'natural', 'naughty', 'nautical', 'near', 'neat', 'necessary',
		'needy', 'negative', 'neglected', 'negligible', 'neighboring', 'nervous', 'new', 'next', 'nice', 'nifty', 'nimble',
		'nippy', 'nocturnal', 'noisy', 'nonstop', 'normal', 'notable', 'noted', 'noteworthy', 'novel', 'noxious', 'numb',
		'nutritious', 'nutty', 'obedient', 'obese', 'oblong', 'oily', 'oblong', 'obvious', 'occasional', 'odd', 'oddball',
		'offbeat', 'offensive', 'official', 'old', 'old-fashioned', 'only', 'open', 'optimal', 'optimistic', 'opulent', 'orange',
		'orderly', 'organic', 'ornate', 'ornery', 'ordinary', 'original', 'other', 'our', 'outlying', 'outgoing', 'outlandish',
		'outrageous', 'outstanding', 'oval', 'overcooked', 'overdue', 'overjoyed', 'overlooked', 'palatable', 'pale', 'paltry',
		'parallel', 'parched', 'partial', 'passionate', 'past', 'pastel', 'peaceful', 'peppery', 'perfect', 'perfumed',
		'periodic', 'perky', 'personal', 'pertinent', 'pesky', 'pessimistic', 'petty', 'phony', 'physical', 'piercing', 'pink',
		'pitiful', 'plain', 'plaintive', 'plastic', 'playful', 'pleasant', 'pleased', 'pleasing', 'plump', 'plush', 'polished',
		'polite', 'political', 'pointed', 'pointless', 'poised', 'poor', 'popular', 'portly', 'posh', 'positive', 'possible',
		'potable', 'powerful', 'powerless', 'practical', 'precious', 'present', 'prestigious', 'pretty', 'precious', 'previous',
		'pricey', 'prickly', 'primary', 'prime', 'pristine', 'private', 'prize', 'probable', 'productive', 'profitable',
		'profuse', 'proper', 'proud', 'prudent', 'punctual', 'pungent', 'puny', 'pure', 'purple', 'pushy', 'putrid', 'puzzled',
		'puzzling', 'quaint', 'qualified', 'quarrelsome', 'quarterly', 'queasy', 'querulous', 'questionable', 'quick',
		'quick-witted', 'quiet', 'quintessential', 'quirky', 'quixotic', 'quizzical', 'radiant', 'ragged', 'rapid', 'rare',
		'rash', 'raw', 'recent', 'reckless', 'rectangular', 'ready', 'real', 'realistic', 'reasonable', 'red', 'reflecting',
		'regal', 'regular', 'reliable', 'relieved', 'remarkable', 'remorseful', 'remote', 'repentant', 'required', 'respectful',
		'responsible', 'repulsive', 'revolving', 'rewarding', 'rich', 'rigid', 'right', 'ringed', 'ripe', 'roasted', 'robust',
		'rosy', 'rotating', 'rotten', 'rough', 'round', 'rowdy', 'royal', 'rubbery', 'rundown', 'ruddy', 'rude', 'runny', 'rural',
		'rusty', 'sad', 'safe', 'salty', 'same', 'sandy', 'sane', 'sarcastic', 'sardonic', 'satisfied', 'scaly', 'scarce', 'scared',
		'scary', 'scented', 'scholarly', 'scientific', 'scornful', 'scratchy', 'scrawny', 'second', 'secondary', 'second-hand',
		'secret', 'self-assured', 'self-reliant', 'selfish', 'sentimental', 'separate', 'serene', 'serious', 'serpentine',
		'several', 'severe', 'shabby', 'shadowy', 'shady', 'shallow', 'shameful', 'shameless', 'sharp', 'shimmering', 'shiny',
		'shocked', 'shocking', 'shoddy', 'short', 'short-term', 'showy', 'shrill', 'shy', 'sick', 'silent', 'silky', 'silly',
		'silver', 'similar', 'simple', 'simplistic', 'sinful', 'single', 'sizzling', 'skeletal', 'skinny', 'sleepy', 'slight',
		'slim', 'slimy', 'slippery', 'slow', 'slushy', 'small', 'smart', 'smoggy', 'smooth', 'smug', 'snappy', 'snarling', 'sneaky',
		'sniveling', 'snoopy', 'sociable', 'soft', 'soggy', 'solid', 'somber', 'some', 'spherical', 'sophisticated', 'sore',
		'sorrowful', 'soulful', 'soupy', 'sour', 'Spanish', 'sparkling', 'sparse', 'specific', 'spectacular', 'speedy', 'spicy',
		'spiffy', 'spirited', 'spiteful', 'splendid', 'spotless', 'spotted', 'spry', 'square', 'squeaky', 'squiggly', 'stable',
		'staid', 'stained', 'stale', 'standard', 'starchy', 'stark', 'starry', 'steep', 'sticky', 'stiff', 'stimulating', 'stingy',
		'stormy', 'straight', 'strange', 'steel', 'strict', 'strident', 'striking', 'striped', 'strong', 'studious', 'stunning',
		'stupendous', 'sturdy', 'stylish', 'substantial', 'subtle', 'suburban', 'sudden', 'sugary', 'sunny', 'super', 'superb',
		'superficial', 'superior', 'supportive', 'sure-footed', 'surprised', 'suspicious', 'svelte', 'sweaty', 'sweet',
		'sweltering', 'swift', 'sympathetic', 'tall', 'talkative', 'tame', 'tan', 'tangible', 'tart', 'tasty', 'tattered', 'taut',
		'tedious', 'teeming', 'tempting', 'tender', 'tense', 'tepid', 'terrible', 'terrific', 'testy', 'thankful', 'that', 'these',
		'thick', 'thin', 'third', 'thirsty', 'this', 'thorough', 'thorny', 'those', 'thoughtful', 'threadbare', 'thrifty',
		'thunderous', 'tidy', 'tight', 'timely', 'tinted', 'tiny', 'tired', 'torn', 'total', 'tough', 'traumatic', 'treasured',
		'tremendous', 'tragic', 'trained', 'tremendous', 'triangular', 'tricky', 'trifling', 'trim', 'trivial', 'troubled',
		'true', 'trusting', 'trustworthy', 'trusty', 'truthful', 'tubby', 'turbulent', 'twin', 'ugly', 'ultimate', 'unacceptable',
		'unaware', 'uncomfortable', 'uncommon', 'unconscious', 'understated', 'unequaled', 'uneven', 'unfinished', 'unfit',
		'unfolded', 'unfortunate', 'unhappy', 'unhealthy', 'uniform', 'unimportant', 'unique', 'united', 'unkempt', 'unknown',
		'unlawful', 'unlined', 'unlucky', 'unnatural', 'unpleasant', 'unrealistic', 'unripe', 'unruly', 'unselfish',
		'unsightly', 'unsteady', 'unsung', 'untidy', 'untimely', 'untried', 'untrue', 'unused', 'unusual', 'unwelcome',
		'unwieldy', 'unwilling', 'unwitting', 'unwritten', 'upbeat', 'upright', 'upset', 'urban', 'usable', 'used', 'useful',
		'useless', 'utilized', 'utter', 'vacant', 'vague', 'vain', 'valid', 'valuable', 'vapid', 'variable', 'vast', 'velvety',
		'venerated', 'vengeful', 'verifiable', 'vibrant', 'vicious', 'victorious', 'vigilant', 'vigorous', 'villainous',
		'violet', 'violent', 'virtual', 'virtuous', 'visible', 'vital', 'vivacious', 'vivid', 'voluminous', 'wan', 'warlike',
		'warm', 'warmhearted', 'warped', 'wary', 'wasteful', 'watchful', 'waterlogged', 'watery', 'wavy', 'wealthy', 'weary',
		'webbed', 'wee', 'weekly', 'weepy', 'weighty', 'weird', 'welcome', 'well-documented', 'well-groomed', 'well-informed',
		'well-lit', 'well-made', 'well-off', 'well-to-do', 'well-worn', 'wet', 'which', 'whimsical', 'whirlwind', 'whispered',
		'white', 'whole', 'whopping', 'wicked', 'wide', 'wide-eyed', 'wiggly', 'wild', 'willing', 'wilted', 'winding', 'windy',
		'winged', 'wiry', 'wise', 'witty', 'wobbly', 'woeful', 'wonderful', 'wooden', 'woozy', 'wordy', 'worldly', 'worn',
		'worried', 'worrisome', 'worse', 'worst', 'worthless', 'worthwhile', 'worthy', 'wrathful', 'wretched', 'writhing',
		'wrong', 'wry', 'yawning', 'yearly', 'yellow', 'yellowish', 'young', 'youthful', 'yummy', 'zany', 'zealous', 'zesty',
		'zigzag'
	];

	public static function generateName() : string
	{
		do {
			$didSomething = false;
			$prefix = "";
			$suffix = "";
			if (mt_rand(0, 5) === 0) {
				$exp = self::PREFIX_SUFFIX[array_rand(self::PREFIX_SUFFIX)];
				$prefix = $exp[0];
				$suffix = $exp[1];
				$didSomething = true;
			}
			$name = self::ADJECTIVES[array_rand(self::ADJECTIVES)];
			$name = str_replace("-", " ", $name);
			$name = ucwords($name);
			$name = str_replace(" ", "", $name);
			$number = -1;
			if (mt_rand(0, 2) === 0) {
				$number = mt_rand(0, 9999);
				$didSomething = true;
			} elseif (mt_rand(0, 1) === 0) {
				$repLen = mt_rand(1, 3);
				for ($i = 0; $i < $repLen; $i++) {
					$name .= $name[strlen($name) - 1];
				}
				$didSomething = true;
			}
			if (mt_rand(0, 2) === 0) {
				$name = preg_replace("/[au]/", "v", $name, 1);
				$didSomething = true;
			} elseif (mt_rand(0, 3) === 0) {
				$name = preg_replace("/[aeiouck]/", "x", $name, 1);
				$didSomething = true;
			}

			if (mt_rand(0, 8) === 0) {
				$name = preg_replace("/[aeiou]/", "", $name);
				$didSomething = true;
			}

			if (mt_rand(0, 8) === 0) {
				$name .= "_";
				$didSomething = true;
			}

			if (mt_rand(0, 8) === 0) {
				$name = "o" . $name;
				$didSomething = true;
			} elseif (mt_rand(0, 8) === 0) {
				$name = "x" . $name;
				$didSomething = true;
			}
			$display = $prefix . $name . ($number === -1 ? "" : (string) $number) . $suffix;
		} while ((($len = strlen($display)) > 10) || $len < 5 || !$didSomething);
		return $display;
	}

	public static function setGamemode(int|string $gamemode, Player $player) : void
	{
		switch ($gamemode) {
			case 0:
			case "survival":
			case "s":
				$player->setGamemode(GameMode::SURVIVAL());
				break;
			case 1:
			case "creative":
			case "c":
				$player->setGamemode(GameMode::CREATIVE());
				break;
			case 2:
			case "adventure":
			case "a":
				$player->setGamemode(GameMode::ADVENTURE());
				break;
			case 3:
			case "spectator":
			case "v":
				$player->setGamemode(GameMode::SPECTATOR());
				break;
			default:
				throw new TypeError("Invalid GameMode is provided, got {$gamemode}.");
				break;
		}
	}

	public static function containsProfanity(string $msg) : bool // credit to nhanaz
	{
		$profanities = (array) Utils::$profanity;
		$filterCount = sizeof($profanities);
		for ($i = 0; $i < $filterCount; $i++) {
			$condition = preg_match('/' . $profanities[$i] . '/iu', $msg) > 0;
			if ($condition) {
				return true;
			}
		}
		return false;
	}

	public static function contains(string $a, string $b) : bool
	{
		if (preg_match('/' . $b . '/iu', $b) > 0) { // idk
			return true;
		} else {
			return false;
		}
	}

	public static function removeSpaces(string $str) : string
	{
		$str = str_replace(" ", "", $str);
		return $str;
	}

	public static function removeDashes(string $str) : string
	{
		$str = str_replace("-", "", $str);
		return $str;
	}

	public static function fromImage(GdImage $image) : string
	{
		$bytes = "";
		for ($y = 0; $y < imagesy($image); $y++) {
			for ($x = 0; $x < imagesx($image); $x++) {
				$rgba = @imagecolorat($image, $x, $y);

				$a = ((~($rgba >> 24)) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;

				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($image);

		return $bytes;
	}

	public function killPlayer(Player $player, bool $nodrops = false) : void{
		if($nodrops){
			$player->getInventory()->clearAll();
		}

		$player->kill();
	}
    
    public static function generateCode(int $length, string $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789"): string{
        mt_srand(intval(microtime(true)*1000000));
        $pass = '' ;
        for($i = 0; $i < $length; $i += 1){
            $num = mt_rand() % strlen($chars);
            $pass .= $chars[$num];
        }
        return $pass;
    }
}

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

use DateInterval;
use DateTime;

use function abs;
use function array_slice;
use function array_splice;
use function count;
use function date;
use function implode;
use function in_array;
use function is_int;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function round;
use function sprintf;
use function str_replace;
use function str_split;
use function strlen;
use function strtolower;
use function strtotime;
use function strtoupper;
use function substr;
use function time;
use function trim;

/**
 * Time utilization related...
 */
final class TimeUtils {

	public static function secsToHHMMSS(int $seconds, bool $strict = false) : string
	{
		if (!$strict && $seconds < 60 * 60) {
			return self::secsToMMSS($seconds);
		}
		return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
	}

	public static function secsToMMSS(float $seconds, bool $strict = true) : string
	{
		if (!$strict && $seconds < 60) {
			return ((string) round($seconds, 2)) . "s";
		}
		$seconds = (int) $seconds;
		return sprintf('%02d:%02d', ($seconds / 60 % 60), $seconds % 60);
	}

	public static function parseDuration(string $argument) : int
	{
		if (self::isInfinite($argument)) {
			return -1; // -1 = infinite
		}
		$parts = str_split($argument);
		static $time_units = [
			"y" => "year",
			"M" => "month",
			"w" => "week",
			"d" => "day",
			"h" => "hour",
			"m" => "minute",
			"s" => "second"
		];
		$time = "";
		$i = -1;
		foreach ($parts as $part) {
			$i++;
			if (isset($time_units[$part])) {
				$unit = $time_units[$part];
				$n = implode("", array_slice($parts, 0, $i));
				$time .= "$n $unit ";
				array_splice($parts, 0, $i + 1);
				$i = -1;
			}
		}
		$time = trim($time);

		return empty($time) ? 0 : strtotime($time) - time();
	}

	private static function isInfinite(string $str) : bool
	{
		$str = strtolower($str);
		return in_array($str, ["-1", "inf", "infinite", "infinity", "forever", "permanent"], true);
	}

	public static function secondsToTicks(int $secs) : int{
		return $secs * 20;
	}

	public static function minutesToTicks(int $mins) : int{
		return $mins * 1200;
	}

	public static function hoursToTicks(int $hours) : int{
		return $hours * 72000;
	}

	public static function ticksToSeconds(int $ticks) : int{
		return (int) ($ticks / 20);
	}

	public static function ticksToMinutes(int $ticks) : int{
		return (int) ($ticks / 1200);
	}

	public static function ticksToHours(int $ticks) : int{
		return (int) ($ticks / 72000);
	}

	public static function isDurationValid(string $duration) : bool
	{
		return preg_match("/^(?:\d+[yMwdhms])+$/", $duration) || self::isInfinite($duration);
	}

	public static function timestamp2Readable(int $time) : string
	{
		return date("F j, Y, g:i a T", $time);
	}

	public static function timestamp2Compact(int $time) : string
	{
		return date("j/n/Y g:i:sA T", $time);
	}

	public static function timestamp2HHMMSS(int $time) : string
	{
		return date("H:i:s", $time);
	}

	public static function humanizeDuration(int $seconds) : string
	{
		$diff = (new DateTime("@0"))->diff(new DateTime("@$seconds"));
		$nega = $seconds < 0;
		$seconds = abs($seconds);
		$fmt = "%s seconds";
		if ($seconds >= 60) {
			$fmt = "%i minutes and " . $fmt;
		}
		if ($seconds >= 60 * 60) {
			$fmt = "%h hours, " . $fmt;
		}
		if ($seconds >= 24 * 60 * 60) {
			$fmt = "%a days, " . $fmt;
		}
		return $diff->format($fmt) . ($nega ? " ago" : "");
	}

	public static function stringTimeToInt(string $string) : int
	{
		/**
		 * Rules:
		 * Integers without suffix are considered as seconds
		 * "s" is for seconds
		 * "m" is for minutes
		 * "h" is for hours
		 * "d" is for days
		 * "w" is for weeks
		 * "mo" is for months
		 * "y" is for years
		 */
		if (trim($string) === "") {
			return null;
		}
		preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
		if (count($found[0]) < 1) {
			return null;
		}
		$found[2] = preg_replace("/[^0-9]/", "", $found[0]);
		$t = 0;
		foreach ($found[2] as $k => $i) {
			switch ($c = $found[1][$k]) {
				case "y":
					$t = +20 * 60 * 60 * 12 * 365;
					break;
				case "w":
					$t = +20 * 60 * 60 * 12 * 7;
					break;
				case "d":
					$t = +20 * 60 * 60 * 12;
					break;
				case "mo":
					$t = +20 * 60 * 60 * 12 * 31;
					break;
				case "h":
					$t = +20 * 60 * 60;
					break;
				case "m":
					$t = +20 * 60;
					break;
				case "s":
					$t = +20;
					break;
				default:
					$t = +20;
					break;
			}
		}
		return $t;
	}

	public static function stringToTimestampReduce(string $string, DateTime $time) : ?array
	{
		/**
		 * Rules:
		 * Integers without suffix are considered as seconds
		 * "s" is for seconds
		 * "m" is for minutes
		 * "h" is for hours
		 * "d" is for days
		 * "w" is for weeks
		 * "mo" is for months
		 * "y" is for years
		 */
		if (trim($string) === "") {
			return null;
		}
		$t = $time;
		preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
		if (count($found[0]) < 1) {
			return null;
		}
		$found[2] = preg_replace("/[^0-9]/", "", $found[0]);
		foreach ($found[2] as $k => $i) {
			switch ($c = $found[1][$k]) {
				case "y":
				case "w":
				case "d":
					$t->sub(new DateInterval("P" . $i . strtoupper($c)));
					break;
				case "mo":
					$t->sub(new DateInterval("P" . $i . strtoupper(substr($c, 0, strlen($c) - 1))));
					break;
				case "h":
				case "m":
				case "s":
					$t->sub(new DateInterval("PT" . $i . strtoupper($c)));
					break;
				default:
					$t->sub(new DateInterval("PT" . $i . "S"));
					break;
			}
			$string = str_replace($found[0][$k], "", $string);
		}
		return [$t, ltrim(str_replace($found[0], "", $string))];
	}

	public static function stringToTimestampAdd(string $string, DateTime $time) : ?array
	{
		/**
		 * Rules:
		 * Integers without suffix are considered as seconds
		 * "s" is for seconds
		 * "m" is for minutes
		 * "h" is for hours
		 * "d" is for days
		 * "w" is for weeks
		 * "mo" is for months
		 * "y" is for years
		 */
		if (trim($string) === "") {
			return null;
		}
		$t = $time;
		preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
		if (count($found[0]) < 1) {
			return null;
		}
		$found[2] = preg_replace("/[^0-9]/", "", $found[0]);
		foreach ($found[2] as $k => $i) {
			switch ($c = $found[1][$k]) {
				case "y":
				case "w":
				case "d":
					$t->add(new DateInterval("P" . $i . strtoupper($c)));
					break;
				case "mo":
					$t->add(new DateInterval("P" . $i . strtoupper(substr($c, 0, strlen($c) - 1))));
					break;
				case "h":
				case "m":
				case "s":
					$t->add(new DateInterval("PT" . $i . strtoupper($c)));
					break;
				default:
					$t->add(new DateInterval("PT" . $i . "S"));
					break;
			}
			$string = str_replace($found[0][$k], "", $string);
		}
		return [$t, ltrim(str_replace($found[0], "", $string))];
	}

	public static function toPrettyFormat(DateTime $duration) : string{
		$now = new DateTime('NOW');
		$interval = $duration->diff($now);
		$output = $interval->format('%m month(s), %d day(s), %h hour(s), %i minute(s)');
		return $output;
	}

	/**
	 * converts shorn int-strings to time stamp formats
	 *
	 * @param int|string $str The input string or integer value to convert.
	 * @return int The converted time value in seconds.
	*/
	public function stringToTimeFormat(int|string $str) : int {
		if (is_int($str)) {
			return $str;
		}

		preg_match('/^\d+/', $str, $matches);
		$value = (int) $matches[0];

		preg_match('/[a-z]+$/', $str, $matches);
		$format = $matches[0];

		switch($format) {
			case 's':
				$seconds = $value;
				break;
			case 'm':
				$seconds = $value * 60;
				break;
			case 'h':
				$seconds = $value * 60 * 60;
				break;
			case 'd':
				$seconds = $value * 60 * 60 * 24;
				break;
			case 'w':
				$seconds = $value * 60 * 60 * 24 * 7;
				break;
			case 'y':
				$seconds = $value * 60 * 60 * 24 * 365;
				break;
			default:
				$seconds = $value;
		}

		return $seconds;
	}

	/**
	 * converts ints to readable shorten string-ints (K, M, B, etc.).
	 *
	 * @param int $number The integer to convert.
	 * @param int $precision The number of decimal places to include in the output (default is 1).
	 * @return string The human-readable number with unit suffix.
	*/
	public function intToReadable(int $number, int $precision = 1) : string {
		$suffixes = ['', 'K', 'M', 'B', 'T', 'P', 'E', 'Z', 'Y'];
		$suffixIndex = 0;
		while (abs($number) >= 1000 && $suffixIndex < count($suffixes) - 1) {
			$number /= 1000;
			$suffixIndex++;
		}
		return round($number, $precision) . $suffixes[$suffixIndex];
	}
}

<?php
/**
* Move Message [Italian]
*
* @package phpBB.de Move Message
* @copyright (c) 2015 phpBB.de
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
<<<<<<< HEAD
	'MOVED_MESSAGE'				=> 'Spostato dal forum <strong>%1$s</strong> al forum <strong>%2$s</strong> il <strong>%4$s</strong> da %3$s', // variables order: source forum, destination forum, time, moderator
));

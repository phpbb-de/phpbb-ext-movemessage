<?php
/**
* Move Message [Estonian]
*
* @package phpBB.de Move Message
* @copyright (c) 2015 phpBB.de; Estonian translation by phpBBeesti.com 05/2015
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
	'MOVED_MESSAGE'				=> 'See postitus on liigutatud <strong>%1$s</strong> foorumist <strong>%2$s</strong> foorumisse liikme <strong>%4$s</strong> poolt %3$s.',
));

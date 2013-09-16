<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Main code for block vlebooks
 *
 * @package   block_modules_dashboard
 * @copyright 2013 Andreas Wagner, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// ...sharedsecret and saltkey for all moodle installations. Do not modify!
define('SHAREDSECRET', 'KfjeceREQk2rCle23wT3qvqFpelgf4tgqhzDI3eg2/0=');
define('SALTKEY', 'trh30Kl3@#r392kPQ2');

/** gets the login url for vlebooks
 * @global object $USER this user.
 * @return string
 * @throws moodle_exception
 */
function block_vlebooks_get_url() {
    global $USER, $CFG;

    require_once($CFG->dirroot.'/blocks/vlebooks/includes/class.rijndael_dotnet_encrypter.php');

    $config = get_config('block_vlebooks');

    // ...uid for login and encrypting.
    $uid = $USER->username;

    // ...generate text.
    $text = $config->id . "-" . $uid;

    // ...decode the sharedsecret.
    $sharedsecret = base64_decode(SHAREDSECRET);

    // Encrypt token.
    if (!$token = rijndael_dotnet_encrypter::encrypt($text, SALTKEY, $sharedsecret,
            MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC, PADDING_TYPE_PKCS7)) {
        throw new moodle_exception('encryptionfailed', 'block_vlebooks');
    }

    // To make the token web safe we need to url encode it.
    $tokenwebsafe = urlencode(base64_encode($token));

    // Build url.
    $params = array('id=' . $config->id, 'accId=' . $config->accId, 'uid=' . $uid, 'token=' . $tokenwebsafe);
    return trim($config->loginurl, '/').'/?'.implode("&", $params);
}
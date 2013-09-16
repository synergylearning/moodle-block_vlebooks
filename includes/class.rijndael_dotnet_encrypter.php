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
 * @package   block_vlebooks
 * @copyright 2013 Andreas Wagner, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/** padding modes for padding text to correct blocksize
 * see: http://msdn.microsoft.com/en-us/library/system.security.cryptography.paddingmode.aspx
 */
define('PADDING_TYPE_NONE', 0);
define('PADDING_TYPE_ZEROS', 1);
define('PADDING_TYPE_ANSIX923', 3);
define('PADDING_TYPE_ISO10126', 5);
define('PADDING_TYPE_PKCS7', 7);

/** this class encrypts text which is compatible to encryption achieved by 
 * RijndaelManaged-Class of Microsofts .NET Framework.
 * 
 * Encryption of .NET uses:
 * 1- Rfc2898DeriveBytes-Class: pdkdf2 Method to obtain key from given password and salt.
 * http://msdn.microsoft.com/en-us//library/system.security.cryptography.rfc2898derivebytes.aspx
 * 
 * 2- various padding modes to fill the text up to the correct blocksize.
 * http://msdn.microsoft.com/en-us/library/system.security.cryptography.paddingmode.aspx
 */
class rijndael_dotnet_encrypter {

    /** derive key from password and salt following the pdkf2-method
     * 
     * @param string $p the password
     * @param type $s the salt
     * @param type $c the iteration count
     * @param type $kl the keylength to obtain
     * @param type $a the encrypting method
     * @return string the key in appropriate length
     */
    private static function pbkdf2($p, $s, $c, $kl, $a = 'sha1') {

        // ...calculating Hashlength.
        $hl = strlen(hash($a, null, true));

        // Key blocks to compute.
        $kb = ceil($kl / $hl);

        // ...create key.
        $dk = '';

        for ($block = 1; $block <= $kb; $block++) {

            // Initial hash for this block.
            $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

            // Perform block iterations.
            for ($i = 1; $i < $c; $i++) {

                // XOR each iterate.
                $ib ^= ($b = hash_hmac($a, $b, $p, true));
            }
            $dk .= $ib;
        }
        return substr($dk, 0, $kl);
    }

    /** pads text to achieve correct blocksize
     * see http://msdn.microsoft.com/en-us/library/system.security.cryptography.paddingmode.aspx
     * 
     * @param string $text 
     * @param int $blocksize
     * @param int $mode one of the padding modes
     * @return string padded text with correct blocksize length
     */
    private static function padding_text($text, $blocksize, $mode = PADDING_TYPE_NONE) {

        switch ($mode) {

            case PADDING_TYPE_PKCS7 :

                $pad = $blocksize - (strlen($text) % $blocksize);
                $result = $text . str_repeat(chr($pad), $pad);

                break;

            case PADDING_TYPE_ZEROS :

                $pad = $blocksize - (strlen($text) % $blocksize);
                $result = $text . str_repeat(chr(0), $pad);

                break;

            case PADDING_TYPE_ANSIX923 :

                $pad = $blocksize - (strlen($text) % $blocksize);
                if ($pad == 0) {
                    $result = $text;
                } else {
                    $result = $text . str_repeat(chr(0), $pad - 1);
                    $result .= chr($pad);
                }
                break;

            case PADDING_TYPE_ISO10126 :

                $pad = $blocksize - (strlen($text) % $blocksize);
                if ($pad == 0) {
                    $result = $text;
                } else {
                    $result = $text . str_repeat(chr(rand(0, 255)), $pad - 1);
                    $result .= chr($pad);
                }
                break;

            default:
                $result = $text;
        }
        return $result;
    }

    /** encrypts text using mcrypt
     * 
     * @param string $text text to encrypt
     * @param string $saltkey the salt
     * @param string $sharedsecret the shared secret also often named 'password'
     * @param string $cipher the encryption method (q. v. documentation of mcrypt)
     * @param string $mode the encryption mode (q. v. documentation of mcrypt)
     * @param string $paddingtype one of the padding type .NET Framework uses: 
     *               http://msdn.microsoft.com/en-us/library/system.security.cryptography.paddingmode.aspx
     * @param string $iteration iterationcount while generating key.
     * @return mixed the encrypted text
     */
    public static function encrypt($text, $saltkey, $sharedsecret, $cipher = MCRYPT_RIJNDAEL_128,
            $mode = MCRYPT_MODE_CBC, $paddingtype = PADDING_TYPE_PKCS7, $iteration = 1) {

        // ...generating key and iv.
        $keysize = mcrypt_get_key_size($cipher, $mode);
        $ivsize = mcrypt_get_iv_size($cipher, $mode);

        $pbkdf = self::pbkdf2($sharedsecret, $saltkey, $iteration, ($keysize + $ivsize));

        $key = substr($pbkdf, 0, $keysize);
        $iv = substr($pbkdf, $keysize, $ivsize);

        // ...padding the text with bytes.
        $blocksize = mcrypt_get_block_size($cipher, $mode);
        $text = self::padding_text($text, $blocksize, $paddingtype);

        // ...encrypt text with mcrypt.
        return mcrypt_encrypt($cipher, $key, $text, $mode, $iv);
    }

}
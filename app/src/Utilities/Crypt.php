<?php namespace Astra\Utilities;


final class Crypt
{

	const MCRYPT_CIPHER = MCRYPT_RIJNDAEL_128;
	const MCRYPT_MODE = MCRYPT_MODE_CBC;

	/**
	 * [encrypt description]
	 * @param  [type] $plaintext [description]
	 * @param  [type] $key       [description]
	 * @return [type]            [description]
	 */
	public static function encrypt($plaintext, $key)
	{
		$iv_length = mcrypt_get_iv_size(self::MCRYPT_CIPHER, self::MCRYPT_MODE);
		$iv = mcrypt_create_iv($iv_length, MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(self::MCRYPT_CIPHER, $key, $plaintext, self::MCRYPT_MODE, $iv);
        return [base64_encode($ciphertext), base64_encode($iv)];
	}

	/**
	 * [decrypt description]
	 * @param  [type] $ciphertext [description]
	 * @param  [type] $key        [description]
	 * @param  [type] $iv         [description]
	 * @return [type]             [description]
	 */
	public static function decrypt($ciphertext, $key, $iv)
	{
		$ciphertext = base64_decode($ciphertext);
		$iv = base64_decode($iv);
		$plaintext = mcrypt_decrypt(self::MCRYPT_CIPHER, $key, $ciphertext, self::MCRYPT_MODE, $iv);
		return rtrim($plaintext, "\x00..\x1F");
	}

}
<?php

namespace App\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Security
{
    protected $container;
    protected $secret_key;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->secret_key = $this->generate_secret_key($this->container->getParameter('secret'));
    }

    private function generate_secret_key($secret_key = null)
    {
        if (is_null($secret_key)) $secret_key = $this->secret_key;
        if (is_null($secret_key) || strlen($secret_key) < 1) throw new \Exception("Invalid secret key");

        $secret_key_size = strlen($secret_key);

        $final_secret_key = null;

        if ($secret_key_size == SODIUM_CRYPTO_SECRETBOX_KEYBYTES) $final_secret_key = $secret_key;
        else if ($secret_key_size > SODIUM_CRYPTO_SECRETBOX_KEYBYTES) $final_secret_key = substr($secret_key, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        else
        {
            while (strlen($secret_key) < SODIUM_CRYPTO_SECRETBOX_KEYBYTES)
            {
                $secret_key .= $secret_key;
            }
            return $this->gen_secret_key($secret_key);
        }

        $byte_array = unpack('C*', $final_secret_key);
        $hidden_secret_key = "";
        foreach ($byte_array as $byte)
        {
            $masked_byte = $byte << 0x00000001 ^ 0x00000042;
            $hidden_secret_key .= chr($masked_byte);
        }
        return $hidden_secret_key;
    }

    public function encrypt(string $raw_message, $utf8 = true) : string
    {
        try {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

            $encrypted_message = sodium_crypto_secretbox($raw_message, $nonce, $this->secret_key);

            return ($utf8) ? utf8_encode($encrypted_message . $nonce) : $encrypted_message . $nonce;
        }
        catch(\Throwable $exception) {
            return null;
        }
    }

    public function decrypt(string $encrypted_message, $utf8 = true) : string
    {
        try {
            if ($utf8) $encrypted_message = utf8_decode($encrypted_message);

            $nonce_size = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
            $nonce = substr($encrypted_message, -$nonce_size);

            $true_encrypted_message_length = strlen($encrypted_message);
            if ($true_encrypted_message_length > $nonce_size) $true_encrypted_message_length -= $nonce_size;
            $true_encrypted_message = substr($encrypted_message, 0, $true_encrypted_message_length);

            $decrypted_message = sodium_crypto_secretbox_open($true_encrypted_message, $nonce, $this->secret_key);

            return $decrypted_message;
        }
        catch(\Throwable $exception) {
            return 'Error';
        }
    }

    public function decryptFileContent(string $filepath)
    {
        $fd = fopen($filepath, 'r');

        $crypted_content = "";
        while ($line = fgets($fd)) $crypted_content .= $line;
        $decrypted_content = $this->decrypt($crypted_content);

        fclose($fd);

        return $decrypted_content;
    }
}

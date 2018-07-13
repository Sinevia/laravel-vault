<?php

namespace Sinevia\Vault\Models;

class Vault extends \AdvancedModel {

    protected $table = 'snv_vault_vault';
    protected $primaryKey = 'Id';
    public $incrementing = false;
    public $useMicroId = true;

    public static function storeValue($value, $password) {
        $vault = new Vault;
        $vault->setValue($value, $password);
        $isSaved = $vault->save();
        if ($isSaved != false) {
            return $vault->Id;
        }
        return null;
    }

    public static function retrieveValue($id, $password) {
        $vault = Vault::find($id);
        if ($vault != null) {
            return $vault->getValue($password);
        }
        return null;
    }

    /**
     * Returns the value
     * @param type $key
     * @return string
     */
    public function getValue($password) {
        try {
            return VaultHelper::decode($this->Value, $password);
        } catch (\RuntimeException $e) {
            return '';
        }
    }

    /**
     * Saves the value
     * @param object $value
     * @return boolean
     */
    public function setValue($value, $password) {
        $this->Value = VaultHelper::encode($value, $password);
        return $this->save() != false ? true : false;
    }

    public static function tableCreate() {
        $o = new self;

        if (\Schema::connection($o->connection)->hasTable($o->table) == false) {
            return \Schema::connection($o->connection)->create($o->table, function (\Illuminate\Database\Schema\Blueprint $table) use ($o) {
                        $table->engine = 'InnoDB';
                        $table->string($o->primaryKey, 40)->primary();
                        $table->longtext('Value');
                        $table->datetime('CreatedAt')->nullable();
                        $table->datetime('UpdatedAt')->nullable();
                        $table->datetime('DeletedAt')->nullable();
                    });
        }

        return true;
    }

    public static function tableDelete() {
        $o = new self;
        if (\Schema::connection($o->connection)->hasTable($o->table) == false) {
            return true;
        }
        return \Schema::connection($o->connection)->drop($o->table);
    }

}

class VaultHelper {

    /**
     * @param string $value
     * @param tring $password
     * @return string
     * @throws \RuntimeException
     */
    public static function decode($value, $password) {
        $first = self::xorDecode($value, self::strongifyPassword($password));
        $v4 = base64_decode($first);
        $a = explode('_', $v4);
        if (isset($a[1]) == false) {
            throw new \RuntimeException('Vault password incorrect');
        }
        $v1 = substr($a[1], 0, $a[0]);
        $v2 = base64_decode($v1);
        return $v2;
    }

    public static function encode($value, $password) {
        $v1 = base64_encode($value);
        $v2 = strlen($v1) . '_' . $v1;
        $rand = self::createRandomBlock(self::calculateRequiredBlockLength(strlen($v2)));
        $v3 = $v2 . '' . substr($rand, strlen($v2));
        $v4 = base64_encode($v3);
        $last = self::xorEncode($v4, self::strongifyPassword($password));
        return $last;
    }

    /**
     * Performs multiple calculations on top of the password
     * and changes it to a derivative long hash.
     * This is done so that even simple and not-long passwords
     * can become longer and stronger (144 characters).
     * @return string
     */
    protected static function strongifyPassword($password) {
        $p1 = md5($password) . md5($password) . md5($password);
        $p1 = str_rot13($p1);
        $p2 = sha1($p1) . sha1($p1) . sha1($p1);
        $p3 = sha1($p2) . md5($p2) . sha1($p2);
        $p4 = str_rot13($p3);
        $p5 = sha1($p4) . str_rot13(md5($p4)) . str_rot13(sha1($p4)) . md5($p4);
        return $p5;
    }

    /**
     * Returns a random string of specified length
     * @param integer $length
     * @return string
     */
    protected static function createRandomBlock($length = 128) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Calculates block length (128) required to contain a length
     * @param integer $v
     * @return integer
     */
    protected static function calculateRequiredBlockLength($v) {
        $a = 128;
        while ($v > $a) {
            $a = $a * 2;
        }
        return $a;
    }

    /**
     * XOR Encodes a String
     * Encodes a String with another key String using the
     * XOR encryption.
     * @param String the String to encode
     * @param String the key String
     * @return String the XOR encoded String
     */
    protected static function xorEncode($string, $key) {
        for ($i = 0, $j = 0; $i < strlen($string); $i++, $j++) {
            if ($j == strlen($key)) {
                $j = 0;
            }
            $string[$i] = $string[$i] ^ $key[$j];
        }
        return base64_encode($string);
    }

    /**
     * XOR Decodes a String
     *
     * Decodes a XOR encrypted String using the same key String.
     * @param String the String to decode
     * @param String the key String
     * @return String the decoded String
     */
    protected static function xorDecode($encstring, $key) {
        $string = base64_decode($encstring);
        for ($i = 0, $j = 0; $i < strlen($string); $i++, $j++) {
            if ($j == strlen($key)) {
                $j = 0;
            }
            $string[$i] = $key[$j] ^ $string[$i];
        }
        return $string;
    }

}

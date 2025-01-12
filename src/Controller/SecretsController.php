<?php

namespace UserFrosting\Sprinkle\Secrets\Controller;

/**
 * Rules controller
 *
 * @package UserFrosting-RegSevak
 * @author Srinivas Nukala
 * @link http://srinivasnukala.com
 */
class SecretsController//extends SimpleController

{

    protected $secrets = [];

    public function __construct($key = [])
    {
        $this->loadSecrets($key);
    }

    public function loadSecrets($key = 'NOKEY')
    {
    }

    public function getSecret($secretName, $defaultValue = '_NOT_SET_')
    {
        $secret = env($secretName) ?: $defaultValue;
        return $secret;
    }

    public function createSecret($key, $value) // $setting like "FOO=BAR"

    {
        putenv("$key=$value");
    }
}
<?php

namespace UserFrosting\Sprinkle\Secrets\Controller\Aws;

//use Carbon\Carbon;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;
use UserFrosting\Sprinkle\Secrets\Controller\SecretsController;
//use Aws\Sts\StsClient;
//use Aws\Credentials\CredentialProvider;
//use Aws\Credentials\InstanceProfileProvider;
//use Aws\Credentials\AssumeRoleCredentialProvider;

/**
 * Rules controller
 *
 * @package UserFrosting-Secrets
 * @author Srinivas Nukala
 * @link http://srinivasnukala.com
 */
class AwsSecretsController extends SecretsController
{
    protected $region = "us-east-1";

    public function loadSecrets($key = 'NOKEY')
    {
        $secrets = $this->getAwsSecret($key);
        $envarr = json_decode($secrets, true);
        $this->secrets = $envarr;
    }

    public function getSecret($secretName, $defaultValue = '_NOT_SET_')
    {
        $secret = isset($this->secrets[$secretName]) ? $this->secrets[$secretName] : $defaultValue;
        return $secret;
    }

    public function createSecret($key, $value) // $setting like "FOO=BAR"
    {
        $this->secrets[$key] = $value;
    }

    public function getAwsSecret($secretName)
    {
        $client = new SecretsManagerClient([
            //'profile' => 'default', -- don't use this as it will ignore Credentials 
            //'credentials' => $provider, -- don't need this running from EC2 instance
            'version' => '2017-10-17',
            'region' => $this->region
            //,'endpoint' => 'secretsmanager.us-east-1.amazonaws.com'
        ]);

        try {
            $result = $client->getSecretValue([
                'SecretId' => $secretName,
            ]);
        } catch (AwsException $e) {
            $error = $e->getAwsErrorCode();
            error_log("Line 60 aWS Secret Controller : $error is the Error for secret : $secretName");
            if ($error == 'DecryptionFailureException') {
                // Secrets Manager can't decrypt the protected secret text using the provided AWS KMS key.
                // Handle the exception here, and/or rethrow as needed.
                throw $e;
            }
            if ($error == 'InternalServiceErrorException') {
                // An error occurred on the server side.
                // Handle the exception here, and/or rethrow as needed.
                throw $e;
            }
            if ($error == 'InvalidParameterException') {
                // You provided an invalid value for a parameter.
                // Handle the exception here, and/or rethrow as needed.
                throw $e;
            }
            if ($error == 'InvalidRequestException') {
                // You provided a parameter value that is not valid for the current state of the resource.
                // Handle the exception here, and/or rethrow as needed.
                throw $e;
            }
            if ($error == 'ResourceNotFoundException') {
                // We can't find the resource that you asked for.
                // Handle the exception here, and/or rethrow as needed.
                throw $e;
            }
        }
        // Decrypts secret using the associated KMS CMK.
        // Depending on whether the secret is a string or binary, one of these fields will be populated.
        if (isset($result['SecretString'])) {
            $secret = $result['SecretString'];
        } else {
            $secret = base64_decode($result['SecretBinary']);
        }
        //print_r($secret);
        return $secret;
    }

    public function createAwsSecret()
    {
        $client = new SecretsManagerClient([
            //'profile' => 'default', -- don't use this as it will ignore Credentials 
            'version' => '2017-10-17',
            'region' => $this->region
        ]);

        $secretName = '<<{{MySecretName}}>>';
        $secret = '{"username":"<<USERNAME>>","password":"<<PASSWORD>>"}';
        $description = '<<Description>>';

        try {
            $result = $client->createSecret([
                'Description' => $description,
                'Name' => $secretName,
                'SecretString' => $secret,
            ]);
            var_dump($result);
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }
    }
}

<?php
// AWS SDK v2 &&  PHP 5.6
require_once 'aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$config = [];

$config["key"] = ""; // YOUR_API_KEY
$config["secret"] = ""; // YOUR_SECRET_KEY
$config["bucket"] = "bucketwcosta"; // YOUR_BUCKET_NAME

$file['file_name'] = ""; // name of file
$file['file_path'] = ""; // path of file

$resp = uploadS3($config, $file);

echo $resp->code . " - " . $resp->msg;

/**
 * uploadS3
 * Function that upload of the <sourceFile> to an S3 bucket
 * Reference: https://docs.aws.amazon.com/aws-sdk-php/v2/guide/service-s3.html
 * 
 * @author Wellington Costa - https://github.com/wcostale
 * @param Array $config = ['key', 'secret', 'bucket'] required 
 * @param Array $file = ['file_name', 'file_path'] required
 * @return Object [code] [msg]
 */
function uploadS3($config = null, $file = null) 
{    
    if(!$config) return (object)["code" => 0, "msg" => "Param @config is required"];
    if(!$file) return (object)["code" => 0, "msg" => "Param @file is required"];

    if(!isset($config["key"]) || $config["key"] == "") return (object)["code" => 0, "msg" => "@key is required"];
    if(!isset($config["secret"]) || $config["secret"] == "" ) return (object)["code" => 0, "msg" => "@secret is required"];
    if(!isset($config["bucket"]) || $config["bucket"] == "" ) return (object)["code" => 0, "msg" => "@bucket is required"];
    
    if(!isset($file['file_name']) || $file['file_name'] == "") return (object)["code" => 0, "msg" => "@file_name is required"];
    if(!isset($file['file_path']) || $file['file_path'] == "" ) return (object)["code" => 0, "msg" => "@file_path is required"];

    try {
        $client = S3Client::factory(
            array(
            'key'    => $config["key"],
            'secret' => $config["secret"]
            )
        );

        // send file to bucket
        if(file_exists($file['file_path'])) {
            $result = $client->putObject(array(
                'Bucket'     => $config["bucket"],
                'Key'        => $file["file_name"],
                'SourceFile' => $file["file_path"],
                'ACL'        => 'public-read'
            ));
            // echo $result['ObjectURL'];
            
            // delete file after
            unlink($file['file_path']);            
            return (object)["code" => 1, "msg" => "file successfully sent to s3"];
        }
        else
            return (object)["code" => 0, "msg" => "file not found"];
    }
    catch (S3Exception $e) {
        $e = $e->getMessage();
    }
    return (object)["code" => 0, "msg" => "Error: $e"];
}
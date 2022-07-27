<?php
class HelloAssoAPI
{
    private $api_url = "https://api.helloasso.com/";
    private $api_version = "v3";

    private $api_key = "";
    private $api_password = "";


    public function __construct($config = array())
    {
        $this->api_key = $config['api_key'];
        $this->api_password = $config['api_password'];
    }


    public function GetAccessToken()
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . "/" . $this->api_version . "/actions.json",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->api_key . ":" . $this->api_password,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_RETURNTRANSFER => true
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response)
        {
            $response = json_decode($response);
            var_dump($response);

            return true;
        }
        
        return false;
    }
}
?>
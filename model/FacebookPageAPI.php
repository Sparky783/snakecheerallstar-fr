<?php
class FacebookPageAPI
{
    private $api_url = "https://graph.facebook.com/";
    private $api_version = "v4.0";

    private $app_id = "";
    private $app_secret = "";
    private $page_id = "";
    private $access_token = null;


    public function __construct($config = array())
    {
        $this->app_id = $config['app_id'];
        $this->app_secret = $config['app_secret'];
        $this->page_id = $config['page_id'];
    }

    public function GetPagePhotos()
    {
        $this->GetAccessToken();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
			CURLOPT_URL => $this->api_url . $this->api_version ."/" . $this->page_id . "/photos?access_token=" . $this->access_token,
			//CURLOPT_URL => $this->api_url . $this->api_version ."/" . $this->page_id . "/manage_pages?access_token=" . $this->access_token,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_TIMEOUT => 30,
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

    public function GetAccessToken()
    {
        if($this->access_token == null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "oauth/access_token?client_id=" . $this->app_id . "&client_secret=" . $this->app_secret . "&grant_type=client_credentials",
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            if ($response)
            {
                $response = json_decode($response);
                $this->access_token = $response->access_token;

                return $this->access_token;
                //return true;
            }
        }
        
        return false;
    }
}
?>
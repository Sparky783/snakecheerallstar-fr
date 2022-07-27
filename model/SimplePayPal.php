<?php
/*
 * Simple version of PayPal for checkout.
 */

class SimplePayPal
{
    private $api_url = "";
    private $api_client_id = "";
    private $api_secret = "";

    private $access_token = null;


    public function __construct($config = array())
    {
        $this->api_url = $config['url'];
        $this->api_client_id = $config['client_id'];
        $this->api_secret = $config['secret'];

        $this->GetAccessToken();
    }

    public function CreateOrder($data)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/checkout/orders",
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'accept: application/json',
                    'accept-language: en_US',
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                //return json_decode($response);
                return $response;
        }

        return false;
    }

    public function UpdateOrder($orderId)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/checkout/orders/" . $orderId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PATCH",
                CURLOPT_POSTFIELDS => '[
                    {
                        "op": "replace",
                        "path": "/purchase_units/@reference_id==\'PUHF\'/amount",
                        "value": {
                            "currency_code": "USD",
                            "value": "200.00",
                            "breakdown": {
                                "item_total": {
                                "currency_code": "USD",
                                "value": "180.00"
                                },
                                "shipping": {
                                    "currency_code": "USD",
                                    "value": "20.00"
                                }
                            }
                        }
                    }
                ]',
                CURLOPT_HTTPHEADER => array(
                    'accept: application/json',
                    'accept-language: en_US',
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                return json_decode($response);
        }

        return false;
    }

    public function CapturePaymentForOrder($orderId)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/checkout/orders/" . $orderId . "/capture",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json',
                    'paypal-request-id: 7b92603e-77ed-4896-8e78-5dea2050476a'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                return json_decode($response);
        }
        
        return false;
    }

    // Retourne les détails de la commande.
    public function ShowOrderDetails($orderId)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/checkout/orders/" . $orderId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                return json_decode($response);
        }

        return false;
    }

    public function ShowCapturedPaymentDetails($captureId)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/payments/captures/" . $captureId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                return json_decode($response);
        }

        return false;
    }

    // TODO :
    public function RefundCapturedPayment($captureId)
    {
        if($this->access_token != null)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api_url . "/v2/payments/captures/" . $captureId . "/refund",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => 6,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . $this->access_token,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err)
                return json_decode($response);
        }

        return false;
    }

    // Récupère le jeton d'accés pour l'utilisation de l'API PayPal
    private function GetAccessToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url . "/v1/oauth2/token",
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_USERPWD => $this->api_client_id . ":" . $this->api_secret
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
        {
            $response = json_decode($response);

            $this->access_token = $response->access_token;

            return true;
        }

        return false;
    }
}
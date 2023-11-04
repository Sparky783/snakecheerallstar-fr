<?php
namespace Snake;
use InvalidArgumentException;

/*
 * Simple version of PayPal for checkout.
 */

class SimplePayPal
{
    /**
     * @var string $_apiUrl PayPal API's URL.
     */
    private $_apiUrl = '';

    /**
     * @var string $_apiVersion PayPal API's version.
     */
    private $_apiVersion = 'v2';

    /**
     * @var string $_apiClientId PayPal public key.
     */
    private $_apiClientId = '';

    /**
     * @var string $_apiSecret PayPal private key.
     */
    private $_apiSecret = '';

    /**
     * @var string $_accessToken Token used to communicate with PayPal API.
     */
    private $_accessToken = null;


    // ==== CONSTRUCTOR ====
    public function __construct($config = [])
    {
        if (count($config) > 0) {
            $this->_apiUrl = $config['url'];
            $this->_apiClientId = $config['client_id'];
            $this->_apiSecret = $config['secret'];
        } else {
            throw new InvalidArgumentException('The PayPal API configuration is missing.');
        }

        $this->getAccessToken();
    }

    /**
     * Create a new order for the customer.
     */
    public function createOrder(array $data): mixed
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/checkout/orders',
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'accept-language: en_US',
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            //return json_decode($response);
            return $response;
        }

        return false;
    }

    /**
     * Update an existing order.
     */
    public function updateOrder(string $orderId)
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/checkout/orders/' . $orderId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
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
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'accept-language: en_US',
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response);
        }

        return false;
    }

    /**
     * Apply the payment and transfert customer money to your PayPal account.
     */
    public function capturePaymentForOrder($orderId)
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/checkout/orders/' . $orderId . '/capture',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json',
                'paypal-request-id: 7b92603e-77ed-4896-8e78-5dea2050476a'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response);
        }
        
        return false;
    }

    /**
     * Get the order details
     */
    public function showOrderDetails($orderId)
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/checkout/orders/' . $orderId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response);
        }

        return false;
    }

    /**
     * Get information about the payment.
     */
    public function showCapturedPaymentDetails($captureId)
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/payments/captures/' . $captureId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response);
        }

        return false;
    }

    // TODO :
    /**
     * Refund the customer.
     */
    public function refundCapturedPayment($captureId)
    {
        if ($this->_accessToken === null) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/' . $this->_apiVersion . '/payments/captures/' . $captureId . '/refund',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->_accessToken,
                'content-type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response);
        }

        return false;
    }

    /**
     * Ask to the API a new token to communicate with it.
     */
    private function getAccessToken(): bool
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_apiUrl . '/v1/oauth2/token',
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_USERPWD => $this->_apiClientId . ':' . $this->_apiSecret
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            $response = json_decode($response);

            $this->_accessToken = $response->access_token;

            return true;
        }

        return false;
    }
}
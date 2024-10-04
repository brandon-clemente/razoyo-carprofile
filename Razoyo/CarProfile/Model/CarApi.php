<?php

declare(strict_types=1);

namespace Razoyo\CarProfile\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class CarApi
{

    const BASE_URI = 'https://exam.razoyo.com/';

    const CAR_LIST_ENDPOINT_URI = 'api/cars';

    const CAR_BY_ID_ENDPOINT_URI = 'api/cars/%s';

    private string $token;

    public function __construct(
        protected readonly Client $client,
        protected readonly ClientFactory $clientFactory,
        protected readonly Response $response,
        protected readonly Json $serializer,
        protected readonly LoggerInterface $logger
    ) {

    }

    /**
     * @return array
     */
    public function getCarList(): array
    {
        $carList = [];

        try {

            $getCarListResponse = $this->getCarListResponse();

            if ($getCarListResponse->getStatusCode() !== 200) {
                throw new \Exception($getCarListResponse->getReasonPhrase());
            }

            $responseBody = $getCarListResponse->getBody();
            $this->token = $getCarListResponse->getHeader('your-token')[0];

            $responseContent = $this->serializer->unserialize($responseBody->getContents());
            if ($cars = $responseContent['cars']) {
                $carList = $cars;
            }


        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $carList;
    }


    /**
     * @return Response
     */
    public function getCarListResponse(): Response
    {

        $requestData = [
            'config' => [
                'base_uri' => self::BASE_URI,
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]
        ];

        return $this->doRequest(
            $requestData,
            self::CAR_LIST_ENDPOINT_URI,
            Request::HTTP_METHOD_GET
        );

    }

    /**
     * @param $carId
     * @return array
     */
    public function getCarListById($carId): array
    {
        $carInfo = [];

        try {
            $getCarListByIdResponse = $this->getCarListByIdResponse($carId);

            if ($getCarListByIdResponse->getStatusCode() !== 200) {
                throw new \Exception($getCarListByIdResponse->getReasonPhrase());
            }

            $responseBody = $getCarListByIdResponse->getBody();
            $responseContent = $this->serializer->unserialize($responseBody->getContents());
            if ($car = $responseContent['car']) {
                $carInfo = $car;
            }


        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $carInfo;
    }


    /**
     * @param $carId
     * @return Response
     */
    public function getCarListByIdResponse($carId): Response
    {

        $requestData = [
            'config' => [
                'base_uri' => self::BASE_URI,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ]
        ];

        return $this->doRequest(
            $requestData,
            sprintf(self::CAR_BY_ID_ENDPOINT_URI, $carId),
            Request::HTTP_METHOD_GET
        );

    }

    /**
     * @param array $requestData
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     * @return Response
     */
    public function doRequest(
        array $requestData,
        string $uriEndpoint,
        string $requestMethod = Request::HTTP_METHOD_GET,
        array $params = []
    ): Response {

        $client = $this->clientFactory->create($requestData);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            $statusCode = $exception->getCode() !== 0 ? $exception->getCode() : 500;
            $response = new Response(
                $statusCode,
                [],
                null,
                '1.1',
                $exception->getMessage()
            );
        }

        return $response;
    }
}

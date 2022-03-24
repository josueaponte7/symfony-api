<?php

namespace App\Service\Isbn;

use App\Model\Dto\Isbn\GetBookByIsbnResponse;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetBookByIsbn
{
    private HttpClientInterface $httpClient;


    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(string $isbn): GetBookByIsbnResponse
    {
        // OL7353617M
        $response = $this->httpClient->request('GET', sprintf('https://openlibrary.org/isbn/%s.json', $isbn), []);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new Exception('Error recuperando el libro');
        }
        $content = $response->getContent();
        $json = json_decode($content, true);
        return new GetBookByIsbnResponse(
            $json['number_of_pages'],
            $json['title'],
            $json['publish_date'],
        );
    }
}

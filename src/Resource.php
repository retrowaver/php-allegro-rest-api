<?php
namespace Allegro\REST;

use Psr\Http\Message\ResponseInterface;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Client\Exception\TransferException;
use Allegro\REST\Token\Token;
use Allegro\REST\Traits\HttpBuildQuery;
use Allegro\REST\Traits\Uuid;

class Resource
{
    use HttpBuildQuery;
    use Uuid;

    /**
     * @var string
     */
    private $id;

    /**
     * @var Resource
     */
    private $parent;

    /**
     * Resource constructor.
     * @param string $id
     * @param Resource $parent
     */
    public function __construct(string $id, Resource $parent)
    {
        $this->id = $id;
        $this->parent = $parent;
    }

    /**
     * @param string $name
     * @return Resource
     */
    public function __get(string $name): Resource
    {
        return new Resource($name, $this);
    }

    /**
     * @param string $name
     * @param array $args
     * @return Resource
     */
    public function __call(string $name, array $args): Resource
    {
        $previous = new Resource($name, $this);
        $id = array_shift($args);

        if ($previous->isCommand()) {
            return new Resource(
                $id ?? $this->getUuid(),
                $previous
            );
        }

        return new Resource($id, $previous);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param null|array $queryParams
     * @return string
     */
    public function getUri(?array $queryParams = null): string
    {
        $uri = $this->parent->getUri() . '/' . $this->id;

        if (!empty($queryParams)) {
            $uri .= '?' . $this->httpBuildQuery($queryParams);
        }

        return $uri;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->parent->getHeaders();
    }

    /**
     * @return bool
     */
    public function isCommand(): bool
    {
        return (substr($this->getId(), -9) === '-commands');
    }

    /**
     * @param null|array $queryParams
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function get(?array $queryParams = null, array $headers = []): ResponseInterface
    {
        return $this->sendApiRequest(
            $this->getUri($queryParams),
            'GET',
            $headers
        );
    }

    /**
     * @param array $data
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function put(?array $data, array $headers = []): ResponseInterface
    {
        return $this->sendApiRequest($this->getUri(), 'PUT', $headers, $data);
    }

    /**
     * @param array|string|null $data
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function post($data, array $headers = [])
    {
        return $this->sendApiRequest($this->getUri(), 'POST', $headers, $data);
    }

    /**
     * @param null|array $queryParams
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function delete(?array $queryParams = null, array $headers = [])
    {
        return $this->sendApiRequest(
            $this->getUri($queryParams),
            'DELETE',
            $headers
        );
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array|string|null $data
     * @return ResponseInterface
     */
    protected function sendApiRequest(
        string $url,
        string $method,
        array $headers = [],
        $data = null
    ): ResponseInterface {
        return $this->getClient()->sendRequest(
            $this->getMessageFactory()->createRequest(
                $method,
                $url,
                $headers + $this->getHeaders(),
                is_array($data) ? json_encode($data) : $data
            )
        );
    }

    /**
     * @return HttpClient
     */
    protected function getClient(): HttpClient
    {
        return $this->parent->getClient();
    }

    /**
     * @return MessageFactory
     */
    protected function getMessageFactory(): MessageFactory
    {
        return $this->parent->getMessageFactory();
    }
}

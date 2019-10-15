<?php
namespace Allegro\REST;

use Psr\Http\Message\ResponseInterface;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Client\Exception\TransferException;
use Allegro\REST\Token\Token;

class Resource
{
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

    public function __get($name)
    {
        return new Resource($name, $this);
    }

    public function __call($name, $args)
    {
        $previous = new Resource($name, $this);
        $id = array_shift($args);

        if ($this->isResourceACommand($previous)) {
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
     * @return string
     */
    public function getUri(): string
    {
        return $this->parent->getUri() . '/' . $this->id;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->parent->getHeaders();
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

    /**
     * @return string
     */
    private function getUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param null|array $data
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function get(?array $data = null, array $headers = []): ResponseInterface
    {
        $uri = $this->getUri();

        if ($data !== null) {
            $uri .= '?' . $this->httpBuildQuery($data);
        }

        return $this->sendApiRequest($uri, 'GET', $headers);
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
     * @param null|array $data
     * @param array $headers
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function delete($data = null, array $headers = [])
    {
        $uri = $this->getUri();

        if ($data !== null) {
            $uri .= '?' . $this->httpBuildQuery($data);
        }

        return $this->sendApiRequest($uri, 'DELETE', $headers);
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
     * @param array $data
     * @return string
     */
    protected function httpBuildQuery($data)
    {
        // Change booleans to strings ("true" / "false")
        foreach ($data as $key => $value) {
            if (gettype($value) === 'boolean') {
                $data[$key] = var_export($value, true);
            }
        }

        return preg_replace('/%5B\d+%5D/', '', http_build_query($data));
    }

    protected function isResourceACommand(Resource $resource): bool
    {
        return (substr($resource->getId(), -9) === '-commands');
    }
}

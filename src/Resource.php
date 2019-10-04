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
    public function __construct($id, Resource $parent)
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
        $id = array_shift($args);
        $collection = new Resource($name, $this);
        return new Resource($id, $collection);
    }

    /**
     * @return string
     */
    public function getUri()
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
     * @return Commands
     */
    public function commands()
    {
        return new Commands($this);
    }

    /**
     * @param null|array $data
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function get($data = null)
    {
        $uri = $this->getUri();

        if ($data !== null) {
            $uri .= '?' . $this->httpBuildQuery($data);
        }

        return $this->sendApiRequest($uri, 'GET');
    }

    /**
     * @param array $data
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function put($data)
    {
        return $this->sendApiRequest($this->getUri(), 'PUT', $data);
    }

    /**
     * @param array $data
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function post($data)
    {
        return $this->sendApiRequest($this->getUri(), 'POST', $data);
    }

    /**
     * @param null|array $data
     * @throws TransferException on error
     * @return ResponseInterface
     */
    public function delete($data = null)
    {
        $uri = $this->getUri();

        if ($data !== null) {
            $uri .= '?' . $this->httpBuildQuery($data);
        }

        return $this->sendApiRequest($uri, 'DELETE');
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $data
     * @return ResponseInterface
     */
    protected function sendApiRequest(
        string $url,
        string $method,
        array $headers = [],
        array $data = []
    ): ResponseInterface {
        return $this->getClient()->sendRequest(
            $this->getMessageFactory()->createRequest(
                $method,
                $url,
                $headers + $this->getHeaders(),
                json_encode($data)
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
}

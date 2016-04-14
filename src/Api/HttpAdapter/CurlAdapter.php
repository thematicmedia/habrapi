<?php

namespace Habrahabr\Api\HttpAdapter;

use Habrahabr\Api\Exception\ExtenstionNotLoadedException;
use Habrahabr\Api\Exception\NetworkException;

/**
 * Class CurlAdapter
 *
 * Habrahabr Api HTTP adapter using cURL as transport
 *
 * @package Habrahabr\Api\HttpAdapter
 * @version 0.0.8
 * @author thematicmedia <info@tmtm.ru>
 * @link https://tmtm.ru/
 * @link https://habrahabr.ru/
 * @link https://github.com/thematicmedia/habrahabr_api
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CurlAdapter extends BaseAdapter implements HttpAdapterInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Экземпляр cURL
     *
     * Переменнная объявлена как protected, чтобы можно было унаследовать
     * этот класс для для проведения различных Unit-тестов.
     *
     * @var resource
     */
    protected $curl;

    /**
     * Проверяет наличие функций для работы с cURL и
     * инициализирует библиотеку
     */
    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new ExtenstionNotLoadedException('The cURL PHP extension was not loaded');
        }

        $this->curl = curl_init();
    }

    /**
     * Завершает работу с cURL
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Выполняет GET-запрос
     *
     * @param string $url Запрашиваемый ресурс без endpoint'а
     *
     * @return array|false Результат запроса
     */
    public function get($url)
    {
        return $this->request($this->createUrl($url), self::METHOD_GET);
    }

    /**
     * Выполняет POST-запрос
     *
     * @param string $url Запрашиваемый ресурс без endpoint'а
     * @param array $params Параметры, передаваемые в теле запроса
     *
     * @return array|false Результат запроса
     */
    public function post($url, array $params = [])
    {
        return $this->request($this->createUrl($url), self::METHOD_POST, $params);
    }

    /**
     * Выполняет DELETE-запрос
     *
     * @param string $url Запрашиваемый ресурс без endpoint'а
     * @param array $params Параметры, передаваемые в теле запроса
     *
     * @return array|false Результат запроса
     */
    public function delete($url, array $params = [])
    {
        return $this->request($this->createUrl($url), self::METHOD_DELETE, $params);
    }

    /**
     * Выполняет PUT-запрос
     *
     * @param string $url Запрашиваемый ресурс без endpoint'а
     * @param array $params Параметры, передаваемые в теле запроса
     *
     * @return array|false Результат запроса
     */
    public function put($url, array $params = [])
    {
        return $this->request($this->createUrl($url), self::METHOD_PUT, $params);
    }

    /**
     * Выполняет HTTP-запрос
     *
     * @param string $url URL, запрашиваемого ресурса
     * @param string $method HTTP-метод, например, GET
     * @param array $params Параметры, передаваемые в теле запроса
     *
     * @throws NetworkException
     *
     * @return array|boolean Результат запроса
     */
    protected function request($url, $method, array $params = [])
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'client: ' . $this->client,
            'token: ' . $this->token
        ]);

        if ($method == self::METHOD_PUT || $method == self::METHOD_POST) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        if (!$result = curl_exec($this->curl)) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            throw new NetworkException($error, $errno);
        }

        return $result ? json_decode($result, true) : false;
    }
}
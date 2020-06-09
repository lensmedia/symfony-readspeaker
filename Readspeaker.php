<?php

namespace Lens\Bundle\ReadspeakerBundle;

use DateTimeInterface;
use Lens\Bundle\ReadspeakerBundle\DependencyInjection\Configuration;
use Lens\Bundle\ReadspeakerBundle\Exception\InvalidProfileName;
use Lens\Bundle\ReadspeakerBundle\Exception\ReadspeakerRequestException;
use Lens\Bundle\ReadspeakerBundle\Response\Credits;
use Lens\Bundle\ReadspeakerBundle\Response\Event;
use Lens\Bundle\ReadspeakerBundle\Response\Speaker;
use Lens\Bundle\ReadspeakerBundle\Response\Statistic;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Readspeaker implements ReadspeakerInterface
{
    private $http;
    private $config;
    private $profile;

    public function __construct(HttpClientInterface $http, array $config, string $profile = null)
    {
        $this->http = $http;
        $this->config = $config;

        if (null !== $profile) {
            $this->profile($profile);
        }
    }

    public function produce(string $text, bool $ssml = false)
    {
        // Build query params.
        $contentIndex = $ssml ? 'ssml' : 'text';
        $parameters = $this->profileConfig([
            'key' => $this->config['key'],
            $contentIndex => $text,
        ]);

        $parameters['dictionary'] = $parameters['dictionary'] ? 'on' : 'off';

        if ($this->config['preview']) {
            $parameters['command'] = 'preview';
        }

        $streaming = $parameters['streaming'];
        $format = $parameters['audioformat'];

        // Strips defaults from the parameters.
        $parameters = array_diff($parameters, Configuration::PROFILE_DEFAULTS);

        // Build HTTP Client config.
        $requestConfig = ['body' => $parameters];
        if ($streaming) {
            $requestConfig['max_redirects'] = 0;
        }

        // Do the request.
        $response = $this->http->request('POST', $this->config['endpoint'], $requestConfig);

        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw new ReadspeakerRequestException($response->getContent(false), $status);
        }

        if ($streaming) {
            $headers = $response->getHeaders(false);

            return new RedirectResponse(reset($headers['location']), $status);
        } else {
            $tmpfname = str_replace('.tmp', '.'.$format, @tempnam(sys_get_temp_dir(), 'readspeaker_'));

            $handle = fopen($tmpfname, 'w');
            foreach ($this->http->stream($response) as $chunk) {
                fwrite($handle, $chunk->getContent());
            }

            fclose($handle);

            return new File($tmpfname);
        }

        return $content;
    }

    public function statistics(DateTimeInterface $from = null, DateTimeInterface $to = null, string $lang = null, string $voice = null)
    {
        $parameters = [];
        $parameters['from_date'] = $from ? $from->format('YYYYMMDD') : null;
        $parameters['to_date'] = $to ? $to->format('YYYYMMDD') : null;
        $parameters['lang'] = $lang;
        $parameters['voice'] = $voice;

        $content = $this->request('statistics', $parameters)->toArray();

        return array_map(function ($data) {
            return Statistic::fromApiResponse($data);
        }, reset($content));
    }

    public function voiceinfo()
    {
        $content = $this->request('voiceinfo')->toArray();

        return array_map(function ($data) {
            return Speaker::fromApiResponse($data);
        }, reset($content));
    }

    public function credits()
    {
        $content = $this->request('credits')->toArray();

        return array_map(function ($data) {
            return Credits::fromApiResponse($data);
        }, reset($content));
    }

    public function events(string $text, bool $ssml = false)
    {
        $index = $ssml ? 'ssml' : 'text';
        $parameters = $this->profileConfig([$index => $text]);
        $parameters['dictionary'] = $parameters['dictionary'] ? 'on' : 'off';

        $content = $this->request('events', $parameters)->toArray();

        return array_map(function ($data) {
            return Event::fromApiResponse($data);
        }, reset($content));
    }

    private function request(string $command, array $parameters = []): ResponseInterface
    {
        $parameters['command'] = $command;
        $parameters['key'] = $this->config['key'];

        $response = $this->http->request('GET', $this->config['endpoint'], [
            'query' => $parameters,
            'headers' => ['accept' => 'application/json'],
        ]);

        // 200 Ok                   The request was successfully handled. Audio data is returned according to parameters.
        // 302 Document found       The request was successfully handled. An URL location is returned where data can be streamed from.
        // 400 Bad Request          The request sent is incorrect. Please make sure the parameters and their values are correct.
        // 403 Forbidden            The key used does not have permission to perform the requested operation.
        // 404 Not Found            The URL to the API is not correct, or cannot be found.
        // 500 Unexpected Error     An unexpected error has occured.
        // 503 Temporary Busy       The service is currently busy, try again in a few seconds.
        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw new ReadspeakerRequestException($response->getContent(false), $status);
        }

        return $response;
    }

    public function profile(string $profile)
    {
        if (!isset($this->config['profiles'][$profile])) {
            throw new InvalidProfileName($profile);
        }

        $this->profile = $profile;
    }

    public function get(string $index, string $profile = null)
    {
        $profile = $profile ?? $this->profile;

        if (!isset($this->config['profiles'][$profile])) {
            throw new InvalidProfileName($profile);
        }

        return $this->config['profiles'][$profile][$index];
    }

    private function profileConfig(array $config = [], string $profile = null)
    {
        $profile = $profile ?? $this->profile;

        if (null !== $profile) {
            if (!isset($this->config['profiles'][$profile])) {
                throw new InvalidProfileName($profile);
            }

            $config = array_merge($this->config['profiles'][$profile], $config);
        }

        return $config;
    }
}

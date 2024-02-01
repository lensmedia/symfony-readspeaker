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
    private string $profile;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly array $config,
        string $profile = null,
    ) {
        if (null !== $profile) {
            $this->useProfile($profile);
        }
    }

    public function produce(string $text, bool $ssml = false): RedirectResponse|File
    {
        // Build query params.
        $contentIndex = $ssml ? 'ssml' : 'text';
        $parameters = $this->config([
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
        $response = $this->httpClient->request('POST', $this->config['endpoint'], $requestConfig);

        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw new ReadspeakerRequestException($response->getContent(false), $status);
        }

        if ($streaming) {
            $headers = $response->getHeaders(false);

            return new RedirectResponse(reset($headers['location']), $status);
        }

        $tmpfname = str_replace('.tmp', '.'.$format, @tempnam(sys_get_temp_dir(), 'readspeaker_'));

        $handle = fopen($tmpfname, 'wb');
        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($handle, $chunk->getContent());
        }

        fclose($handle);

        return new File($tmpfname);
    }

    public function statistics(
        DateTimeInterface $from = null,
        DateTimeInterface $to = null,
        string $lang = null,
        string $voice = null
    ): array {
        $parameters = [];
        $parameters['from_date'] = $from?->format('YYYYMMDD');
        $parameters['to_date'] = $to?->format('YYYYMMDD');
        $parameters['lang'] = $lang;
        $parameters['voice'] = $voice;

        $content = $this->request('statistics', $parameters)->toArray();

        return array_map(
            static fn ($data) => Statistic::fromApiResponse($data),
            reset($content),
        );
    }

    public function voiceInfo(): array
    {
        $content = $this->request('voiceinfo')->toArray();

        return array_map(
            static fn ($data) => Speaker::fromApiResponse($data),
            reset($content),
        );
    }

    public function credits(): array
    {
        $content = $this->request('credits')->toArray();

        return array_map(
            static fn ($data) => Credits::fromApiResponse($data),
            reset($content),
        );
    }

    public function events(string $text, bool $ssml = false): array
    {
        $index = $ssml ? 'ssml' : 'text';
        $parameters = $this->config([$index => $text]);
        $parameters['dictionary'] = $parameters['dictionary'] ? 'on' : 'off';

        $content = $this->request('events', $parameters)->toArray();

        return array_map(
            static fn ($data) => Event::fromApiResponse($data),
            reset($content),
        );
    }

    private function request(string $command, array $parameters = []): ResponseInterface
    {
        $parameters['command'] = $command;
        $parameters['key'] = $this->config['key'];

        $response = $this->httpClient->request('GET', $this->config['endpoint'], [
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

    public function useProfile(string $profile): void
    {
        if (!isset($this->config['profiles'][$profile])) {
            throw new InvalidProfileName($profile);
        }

        $this->profile = $profile;
    }

    public function get(string $index, string $profile = null): array|string
    {
        $profile = $profile ?? $this->profile;

        if (!isset($this->config['profiles'][$profile])) {
            throw new InvalidProfileName($profile);
        }

        return $this->config['profiles'][$profile][$index];
    }

    private function config(array $config = []): array
    {
        if (!isset($this->config['profiles'][$this->profile])) {
            throw new InvalidProfileName($this->profile);
        }

        return array_merge($this->config['profiles'][$this->profile], $config);
    }
}

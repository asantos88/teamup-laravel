<?php

Namespace Asantos88\TeamUpLaravel;

use Asantos88\TeamUpLaravel\Exceptions\TeamUpException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TeamUp
{
    private Client $client;

    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client(['headers' => [
            'Teamup-Token' => config('team-up.api_key'),
        ]]);
        $this->baseUrl = config('team-up.base_url');
    }

    /**
     * @throws TeamUpException
     */
    public function getEventsFromCalendar($calendarId, $startDate, $endDate, $tz = 'America/London')
    {
        $rawParams = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'tz' => $tz
        ];

        $params = $this->setParams($rawParams);

        return $this->sendRequest('GET', $calendarId.'/events', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function getEvent($calendarId, $eventId, $tz = 'America/London')
    {
        $params = $this->setParams(['tz' => $tz]);

        return $this->sendRequest('GET', $calendarId.'/events/'.$eventId, $params);
    }

    /**
     * @throws TeamUpException
     */
    public function createEvent($calendarId, array $body, $tz = 'America/London')
    {

        $params = $this->setParams(['tz' => $tz], $body);

        return $this->sendRequest('POST', $calendarId.'/events', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function deleteEvent($calendarId, $eventId)
    {
        return $this->sendRequest('DELETE', $calendarId.'/events/'.$eventId);
    }

    /**
     * @throws TeamUpException
     */
    public function updateEvent($calendarId, $eventId, array $body, $tz = 'America/London')
    {

        $params = $this->setParams(['tz' => $tz], $body);

        return $this->sendRequest('PUT', $calendarId.'/events/'.$eventId, $params);
    }

    /**
     * @throws TeamUpException
     */
    public function eventHistory($calendarId, $eventId, $tz = 'America/London')
    {
        $params = $this->setParams(['tz' => $tz]);

        return $this->sendRequest('GET', $calendarId.'/events/'.$eventId.'/history', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function eventAuxInfo($calendarId, $eventId, $tz = 'America/London')
    {
        $params = $this->setParams(['tz' => $tz]);

        return $this->sendRequest('GET', $calendarId.'/events/'.$eventId.'/aux', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function eventUrl($calendarId, $eventId, $tz = 'America/London')
    {
        $params = $this->setParams(['tz' => $tz]);

        return $this->sendRequest('POST', $calendarId.'/events/'.$eventId.'/pointer', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function latestEventsChanges($calendarId, $modifiedSince, $tz = 'America/London')
    {
        $params = $this->setParams([
            'tz' => $tz,
            'modifiedSince' => $modifiedSince
        ]);

        return $this->sendRequest('GET', $calendarId.'/events/', $params);
    }

    /**
     * @throws TeamUpException
     */
    public function undoAction($calendarId, $undoCode)
    {
        return $this->sendRequest('PUT', $calendarId.'/events/undo/'.$undoCode);
    }

    private function setParams($query, $body = [], $headers = []): array
    {
        $params['query'] = $query;

        if (empty($headers)) {
            $params['headers'] = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
        }

        if (!empty($body)) {
            $params['body'] = json_encode($body);
        }

        return $params;
    }

    /**
     * @throws TeamUpException
     */
    private function sendRequest($method, $request, $params = [])
    {
        try {
            $result = $this->client->request($method, $this->baseUrl.'/'.$request, $params);
        } catch (GuzzleException $e) {
            try {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                throw new TeamUpException($response->error->message);
            } catch (\Exception $e) {
                throw new TeamUpException($e->getMessage());
            }
        }

        if (in_array($result->getStatusCode(), [200, 201])) {
            return json_decode($result->getBody()->getContents());
        }

        if ($result->getStatusCode() == 204) {
            return (object)['result' => true];
        }

        throw new TeamUpException('Uncategorized Exception');
    }
}

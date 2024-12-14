<?php

Namespace Asantos88\TeamUpLaravel;

class TeamUp
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('team-up.api_key');
    }
}

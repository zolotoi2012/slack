<?php

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

const SLACK_BASE_URL = "https://slack.com/api/";

// Register .env
$dotenv = Dotenv::create(__DIR__ . "/../");
$dotenv->load();

return function (App $app) {
    $container = $app->getContainer();

    $client = new Client([
        "base_uri" => SLACK_BASE_URL,
        'headers' => [
            "Authorization" => "Bearer " . getenv('SLACK_BOT_USER_TOKEN'),
        ],
    ]);

    $app->post('/inbox/slack', function (Request $request, Response $response, array $args) use ($container, $client) {

        $payload = $request->getParsedBody();
        $message = $payload['event']['text'];
        $channel = $payload['event']['channel'];

        if ($message === "Hi!") {

            $res = $client->post('chat.postMessage', [
                'json'    => [
                    'text'        => "How was your day today?",
                    'channel'     => $channel,
                    'attachments' => [
                        [
                            'text'        => "Choose your answer:",
                            'fallback'    => "You are unable to choose a game",
                            'callback_id' => "day_question",
                            'actions'     => [
                                [
                                    "name"  => "day",
                                    "text"  => "It would great",
                                    "type"  => "button",
                                    "value" => "great",
                                ],
                                [
                                    "name"  => "day",
                                    "text"  => "It was ok",
                                    "type"  => "button",
                                    "value" => "ok",
                                ],
                                [
                                    "name"  => "day",
                                    "text"  => "It wasn’t that good",
                                    "type"  => "button",
                                    "value" => "good",
                                ],
                                [
                                    "name"  => "day",
                                    "text"  => "It was bad",
                                    "type"  => "button",
                                    "value" => "bad",
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $container->get('logger')->info($res->getBody());
        }


        return $response->withJson($request->getParsedBody());
    });
};

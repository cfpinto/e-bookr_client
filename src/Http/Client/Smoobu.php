<?php


namespace Ebookr\Client\Http\Client;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Smoobu
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => config('e-bookr.booking_aggregator.smoobu_api_url'),
                'headers'  => [
                    'Api-Key'       => config('e-bookr.booking_aggregator.smoobu_api_key'),
                    'cache-control' => 'no-cache',
                    'Content-Type'  => 'application/json',
                ],
            ]
        );
    }

    public function rates(Carbon $start, $end, array $apartments = [])
    {

        $response = $this->client->get(
            '/api/rates',
            [
                RequestOptions::QUERY => [
                    'start_date' => $start->firstOfMonth()->format('Y-m-d'),
                    'end_date'   => $end->lastOfMonth()->format('Y-m-d'),
                    'apartments' => $apartments,
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true)['data'];
    }

    public function reserve(Carbon $arrival, Carbon $departure, int $adults, int $children, string $name, string $email, string $mobile, int $roomId)
    {
        try {
            $parts = explode(' ', $name);
            $last = array_pop($parts);
            $first = implode(' ', $parts);
            $response = $this->client->post(
                sprintf('/api/apartment/%s/booking', $roomId),
                [
                    RequestOptions::JSON => [
                        'arrivalDate'   => $arrival->format('Y-m-d'),
                        'departureDate' => $departure->format('Y-m-d'),
                        'adults'        => $adults,
                        'children'      => $children,
                        'priceStatus'   => 0,
                        'firstName'     => $first,
                        'lastName'      => $last,
                        'email'         => $email,
                        'phone'         => $mobile,
                        'channelId'     => config('e-bookr.booking_aggregator.smoobu_settings_channel_id'),
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents())->id;
        } catch (\Throwable $t) {
            return false;
        }
    }

    public function retrieve(int $id, int $roomId, Carbon $from, Carbon $to)
    {
        try {
            $response = $this->client->get(
                sprintf('/api/apartment/%s/booking', $roomId),
                [
                    RequestOptions::QUERY => [
                        'showCancellation' => 'true',
                        'from'             => $from->format('Y-m-d'),
                        'to'               => $to->format('Y-m-d'),
                    ],
                ]
            );

            $data = collect(json_decode($response->getBody()->getContents())->bookings);

            return $data->filter(
                function ($item) use ($id) {
                    return $item->id === $id;
                }
            )->first();
        } catch (\Throwable $t) {

            return false;
        }
    }
}
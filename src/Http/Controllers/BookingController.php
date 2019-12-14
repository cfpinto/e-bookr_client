<?php

namespace Ebookr\Client\Http\Controllers;

use Carbon\Carbon;
use Ebookr\Client\Http\Client\Smoobu;
use Ebookr\Client\Http\Requests\StoreBooking;
use Ebookr\Client\Models\Booking;
use Ebookr\Client\Models\Room;
use Ebookr\Client\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $request->flash();

        if ($request->query('date_check_in')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->query('date_check_in'));
        } else {
            $start = Carbon::create()->addDays(3);
        }

        if ($request->query('date_check_out')) {
            $end = Carbon::createFromFormat('d/m/Y', $request->query('date_check_out'));
        } else {
            $end = $start->copy()->addDay();
        }

        $adults = $request->query('adults', 1);
        $children = $request->query('children', 0);
        $location = location();
        
        if (count($request->query('rooms', []))) {
            $rooms = $location->rooms()->whereIn('id', $request->query('rooms'));
        } else {
            $rooms = $location->rooms()->where('api_source', env('EBOOKR_AGGREGATOR'))->whereNotNull('api_id');
        }
        
        if ($request->isXmlHttpRequest() || $request->query('ajax')) {
            try {
                $smoobu = new Smoobu();
                $data = [];
                $response = $smoobu->rates($start, $end, $rooms->pluck('api_id')->toArray());
                $location->rooms->map(
                    function (Room $room) use ($response, &$data) {
                        if (isset($response[$room->api_id])) {
                            $data[$room->id] = $response[$room->api_id];
                        }
                    }
                );
            } catch (\Throwable $e) {
                $data = [];
            }

            return response()->json($data);
        }

        return view('e-bookr::bookings.index', compact('start', 'end', 'adults', 'children', 'duration', 'rooms'));
    }

    public function create(Request $request)
    {

        $checkIn = Carbon::createFromFormat('Y-m-d', $request->query('start'));
        $duration = $request->query('duration');
        $adults = $request->query('adults');
        $children = $request->query('children');
        $room = \location()->rooms()->where('id', $request->query('room'))->firstOrFail();

        return view('e-bookr::bookings.create', compact('duration', 'checkIn', 'room', 'adults', 'children'));
    }

    public function store(StoreBooking $request)
    {
        $start = Carbon::createFromFormat('Y-m-d', $request->post('start'));
        $end = $start->copy()->addDays($request->post('duration'));
        $adults = $request->post('adults');
        $children = $request->post('children');
        $email = $request->post('email');
        $mobile = $request->post('mobile');
        $name = $request->post('name');
        /** @var Room $room */
        $room = \location()->rooms()->where('id', $request->post('room'))->firstOrFail();

        $smoobu = new Smoobu();
        if ($id = $smoobu->reserve($start, $end, $adults, $children, $name, $email, $mobile, $room->api_id)) {
            /** @var User $user */
            $user = User::firstOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'role_id'  => 2,
                    'name'     => $name,
                    'email'    => $email,
                    'avatar'   => 'users/default.png',
                    'password' => \Hash::make(str_random()),
                    'settings' => [
                        "locale" => "en",
                    ],
                ]
            );

            $booking = new Booking();
            $booking->user_id = $user->id;
            $booking->start = $start;
            $booking->end = $end;
            $booking->api_source = config('e-bookr.booking_aggregator.driver');
            $booking->api_id = $id;
            $room->bookings()->save($booking);
            flash()->success(__('Reservation completed.'));

            return redirect()->to(\URL::signedRoute('bookings.show', ['booking' => $booking->id]));
        }

        flash()->error(__('There was an error finishing your reservation, please review your dates and try again.'));

        return redirect()->route(
            'bookings.index',
            [
                'date_check_in'  => $start->format('d/m/Y'),
                'date_check_out' => $end->format('d/m/Y'),
                'adults'         => $adults,
                'children'       => $children,
            ]
        );
    }

    public function show(Request $request, Booking $booking)
    {
        if (!$request->hasValidSignature()) {
            return abort(404);
        }
        
        $client = new Smoobu();
        $smoobuBooking = $client->retrieve($booking->api_id, $booking->bookable->api_id, $booking->start, $booking->end);
        
        return view('e-bookr::bookings.show', compact('booking', 'smoobuBooking'));
    }
}

<?php

namespace Ebookr\Client\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
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
        $days = $start->diffInDays($end);
        $adults = $request->query('adults') ?? 2;
        $children = $request->query('children') ?? 0;
        if (!$days) {
            flash()->error(__('Select at least one day'));
            return redirect()->back();
        }

        return view('e-bookr::bookings.index', compact('start', 'days', 'adults', 'children'));
    }
}

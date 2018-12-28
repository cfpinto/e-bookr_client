<?php
if (!function_exists('rooms')) {
    function rooms()
    {
        return location()->rooms;
    }
}

if (!function_exists('location')) {
    function location()
    {
        static $location;

        if (empty($location)) {
            $location = \App\Location::find(config('e-bookr.location_id'));
        }
        return $location;
    }
}
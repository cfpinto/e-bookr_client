<?php
if (!function_exists('rooms')) {
    /**
     * @return \Ebookr\Client\Models\Room[]
     */
    function rooms()
    {
        return location()->rooms;
    }
}

if (!function_exists('location')) {
    /**
     * @return \Ebookr\Client\Models\Location
     */
    function location()
    {
        static $location;

        if (empty($location)) {
            $location = \Ebookr\Client\Models\Location::find(config('e-bookr.location_id'));
        }

        return $location;
    }
}

if (!function_exists('other_locations')) {
    /**
     * @return \Ebookr\Client\Models\Location[]
     */
    function other_locations()
    {
        static $locations;

        if (empty($locations)) {
            $locations = \Ebookr\Client\Models\Location::where('id', '<>', config('e-bookr.location_id'))->get();
        }

        return $locations;
    }
}

if (!function_exists('contact')) {
    /**
     * @param integer $id
     *
     * @return \Ebookr\Client\Models\Contact
     */
    function contact($id)
    {
        static $contact = [];

        if (empty($contact[$id])) {
            $contact[$id] = \Ebookr\Client\Models\Contact::find($id);
        }

        return $contact[$id];
    }
}
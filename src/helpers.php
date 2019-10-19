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

if (!function_exists('cloud_thumbnail_settings')) {
    /**
     * @param string  $client_id
     * @param integer $width
     * @param integer $height
     *
     * @return array
     */
    function cloud_thumbnail_settings($client_id, $width = null, $height = null, $crop = 'fill', $secure = true)
    {
        $settings = [
            'public_id' => $client_id,
            'options'   => [
                'transformation' => [],
                'secure'         => $secure,
            ],
        ];

        if ($width) {
            $settings['options']['transformation']['width'] = $width;
        }

        if ($height) {
            $settings['options']['transformation']['height'] = $height;
        }

        if ($crop) {
            $settings['options']['transformation']['crop'] = $crop;
        }

        return $settings;
    }
}
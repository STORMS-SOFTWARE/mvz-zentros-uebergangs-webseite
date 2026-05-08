@component(_wfTemplate(\STORMS\webframe\Core\WebFrame::TEMPLATE__GMAP), [
    'lat' => 51.227741,
    'lng' => 6.773456,
    'zoom' => 15,
    'style' => 'accent',
    'accent_color' => '#ef7d00',
    'height' => '400px',
])
    @slot('popup')
        {{Config::CUSTOMER_NAME}}
    @endslot
@endcomponent
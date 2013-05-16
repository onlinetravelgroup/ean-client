<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)

/**
 * APIs offered by EAN
 *
 * @see Guzzle\Service\Builder\ServiceBuilder
 */
return array(
    'services' => array(
        'hotel' => array(
            'alias' => 'Hotel',
            'class' => 'Otg\Ean\Hotel\HotelClient'
        )
    )
);

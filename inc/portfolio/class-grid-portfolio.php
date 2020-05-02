<?php

class GridPortfolio extends FluxusPage {

    const DEFAULT_GRID_SIZE = '4 3';
    const DEFAULT_ORIENTATION = 'horizontal';
    const DEFAULT_ASPECT_RATIO = 'auto';

    protected $META_PREFIX = 'fluxus_portfolio_';

    protected $meta_data_defaults = array(
        'grid_size'                => self::DEFAULT_GRID_SIZE,
        'grid_orientation'         => self::DEFAULT_ORIENTATION,
        'grid_aspect_ratio'        => self::DEFAULT_ASPECT_RATIO,
        'grid_image_sizes'         => ''
    );


    function get_options() {

        $options = array(
            'orientation'            => $this->meta_grid_orientation,
            'aspect_ratio'           => $this->meta_grid_aspect_ratio,
            'grid_size'              => $this->meta_grid_size,
            'image_sizes_serialized' => $this->meta_grid_image_sizes
        );

        $options['image_sizes'] = GridPortfolio::parse_image_sizes( $options['image_sizes_serialized'] );

        $grid_size = explode( ' ', $options['grid_size'] );

        if ( is_array( $grid_size ) && count( $grid_size ) == 2 ) {
            $options['columns'] = $grid_size[0];
            $options['rows'] = $grid_size[1];
        } else {
            $options['columns'] = 4;
            $options['rows'] = 3;
        }

        return $options;

    }

    static function parse_image_sizes( $json ) {
        $result = array();

        $decoded = json_decode( $json );

        if ( is_array( $decoded ) ) {
            foreach ( $decoded as $image_size ) {
                $result[$image_size->id] = $image_size->size;
            }
        }

        return $result;
    }

}
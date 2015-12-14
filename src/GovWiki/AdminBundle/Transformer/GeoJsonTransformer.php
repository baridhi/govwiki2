<?php

namespace GovWiki\AdminBundle\Transformer;

use GovWiki\AdminBundle\Exception\InvalidGeoJsonException;

/**
 * Convert GeoJson file to plain array of data.
 * {@link http://geojson.org/geojson-spec.html}
 *
 * @package GovWiki\AdminBundle\Transformer
 */
class GeoJsonTransformer implements FileTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function supportedExtensions()
    {
        return [ 'json' ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormatName()
    {
        return 'GeoJson';
    }


    /**
     * {@inheritdoc}
     */
    public function transform($filePath)
    {
        $data = json_decode(file_get_contents($filePath), true);

        if (! array_key_exists('type', $data)) {
            throw new InvalidGeoJsonException($data);
        }

        $result = [];
        if ('FeatureCollection' === $data['type']) {
            if (! array_key_exists('features', $data)) {
                throw new InvalidGeoJsonException($data);
            }

            foreach ($data['features'] as $row) {
                $result[] = $this->convert($row);
            }

        } elseif ('Feature' === $data['type']) {
            $result[] = $this->convert($data);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($filePath, array $data)
    {
        $result = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];
        foreach ($data as $row) {
            if (! array_key_exists('latitude', $row) &&
                ! array_key_exists('longitude', $row)) {
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];

                unset($row['latitude'], $row['longitude']);

                $result['features'][] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [ $longitude, $latitude ],
                    ],
                    'properties' => $row,
                ];
            }
        }

        file_put_contents($filePath, json_encode($result));
    }

    /**
     * @param array $row GeoJson Feature row.
     *
     * @return array
     */
    private function convert(array $row)
    {
        $isTypeDefined = array_key_exists('type', $row) &&
            ('Feature' === $row['type']);
        $isPointGeometry = array_key_exists('geometry', $row) &&
            array_key_exists('type', $row['geometry']) &&
            ('Point' === $row['geometry']['type']);
        if ($isTypeDefined && $isPointGeometry &&
            array_key_exists('coordinates', $row['geometry'])) {
            return array_merge($row['properties'], [
                'longitude' => $row['geometry']['coordinates'][0],
                'latitude' => $row['geometry']['coordinates'][1],
            ]);
        }

        throw new InvalidGeoJsonException($row);
    }
}

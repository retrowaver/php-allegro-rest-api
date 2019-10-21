<?php
namespace Allegro\REST\Traits;

trait HttpBuildQuery
{
    /**
     * @param array $data
     * @return string
     */
    function httpBuildQuery($data)
    {
        // Change booleans to strings ("true" / "false")
        foreach ($data as $key => $value) {
            if (gettype($value) === 'boolean') {
                $data[$key] = var_export($value, true);
            }
        }

        return preg_replace('/%5B\d+%5D/', '', http_build_query($data));
    }
}

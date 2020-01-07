<?php
namespace Retrowaver\Allegro\REST\Traits;

trait HttpBuildQueryTrait
{
    /**
     * Generate URL-encoded query string
     * 
     * Native PHP's `http_build_query` method that's been altered to be friendly
     * with Allegro API. See #6 and #13 in the original repo.
     * 
     * @param array $data
     * @return string
     */
    protected function httpBuildQuery(array $data): string
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

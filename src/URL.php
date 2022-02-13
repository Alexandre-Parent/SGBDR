<?php
namespace App;

class URL{
    public static function withParam(array $data, string $param,   $value): string {
        return http_build_query (array_merge($_GET, [$param => $value]));
    }
    public static function withParams( array $data, array $params): string {
        return http_build_query (array_merge($data, $params));

    }
}

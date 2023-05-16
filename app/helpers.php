<?php
if (! function_exists('handleSetCookie')) {
    function handleSetCookie($cookie_name, $cookie_value = null,$cookie_date = null) {
        // 86400 = 1 day
        $cookie_time = $cookie_date ?? time() + (86400 * 1);
        setcookie($cookie_name, $cookie_value, $cookie_time, "/");
    }
}
if (! function_exists('handleGetCookie')) {
    function handleGetCookie($cookie_name) {
        return @$_COOKIE[$cookie_name];
    }
}
if (! function_exists('handleDeleteCookie')) {
    function handleDeleteCookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
    }
}

if (! function_exists('convertData')) {
    function convertData(array $data): array
    {
        $defile = [];
        foreach ($data as $key => $value) {
            $defile[$value[1]][] = ['column' => $value[2], 'type' => $value[3]];
        }

        return $defile;
    }
}

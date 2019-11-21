<?php


namespace Kodilab\Deployer\Helpers;


class Str extends \Illuminate\Support\Str
{
    /**
     * Removes the last \n in the string
     *
     * @param string $line
     * @return string|string[]|null
     */
    public static function removeLastCarriageReturn(string $line)
    {
        return preg_replace("/\n$/", "", $line);
    }
}

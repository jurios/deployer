<?php


namespace Kodilab\Deployer\Helpers;


class Path
{
    /**
     * Returns whether a path matches in at least one rule
     *
     * @param array $rules
     * @param string $path
     * @return bool
     */
    public static function match(array $rules, string $path)
    {
        return count(self::getMatchedRules($rules, $path)) > 0;
    }

    /**
     * Returns the rules which match with the path
     *
     * @param array $rules
     * @param string $path
     * @return array
     */
    public static function getMatchedRules(array $rules, string $path)
    {
        $matched_rules = [];

        foreach ($rules as $rule){
            if (fnmatch($rule, $path)) {
                $matched_rules[] = $rule;
            }
        }

        return $matched_rules;
    }
}
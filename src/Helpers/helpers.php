<?php

if(!function_exists('isCommitValid')) {
    function isCommitValid(string $commit)
    {
        return preg_match("/[a-z0-9]{40}/", $commit);
    }
}

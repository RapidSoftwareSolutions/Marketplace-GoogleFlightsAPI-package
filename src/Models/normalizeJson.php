<?php
namespace Models;

Class normilizeJson {
    
    public function normalizeJson($jsonObject)
    {
        return preg_replace_callback('~"([\[{].*?[}\]])"~s', function ($match) {
            return preg_replace('~\s*"\s*~', "\"", $match[1]);
        }, $jsonObject);
    }

}

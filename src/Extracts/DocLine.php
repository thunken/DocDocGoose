<?php

namespace Thunken\DocDocGoose\Extracts;

class DocLine
{

    public function __construct(array $item)
    {
        foreach ($item as $name => $content) {
            $this->$name = $content;
        }
    }

}
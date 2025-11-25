<?php

namespace digitalpulsebe\editorially\services;

use craft\base\Component;

class SessionService extends Component
{
    public function isActive(): bool
    {
        return false;
    }
}
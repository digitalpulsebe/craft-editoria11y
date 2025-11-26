<?php

namespace digitalpulsebe\editorially\services;

use Craft;
use craft\base\Component;

class SessionService extends Component
{
    const COOKIE_NAME = 'editoria11y-session';

    public function start(): void
    {
        Craft::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => self::COOKIE_NAME,
            'value' => '1',
            'expire' => time() + 60 * 60 * 24
        ]));
    }

    public function stop(): void
    {
        Craft::$app->response->cookies->remove(self::COOKIE_NAME);
    }

    public function isActive(): bool
    {
        return Craft::$app->request->cookies->has(self::COOKIE_NAME);
    }
}
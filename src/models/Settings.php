<?php

namespace digitalpulsebe\editorially\models;

use Craft;
use craft\base\Model;

/**
 * Editoria11y settings
 */
class Settings extends Model
{
    public bool $enableOnPreviewRequests = false;
    public bool $enableOnSiteRequests = false;
    public string $scriptConfig = <<<JS
const ed11y = new Ed11y({
    "checkRoots": ".main"
});
JS;

}

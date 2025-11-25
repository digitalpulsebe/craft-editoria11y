<?php

namespace digitalpulsebe\editorially\assets;

use craft\web\AssetBundle as BaseAssetBundle;

class EditoriallyVendorBundle extends BaseAssetBundle
{
    public function init()
    {
        $this->sourcePath = '@digitalpulsebe/editorially/assets';

        $this->js = [
            'vendor/editoria11y-2.4.5/editoria11y.min.js'
        ];

        $this->css = [
            'vendor/editoria11y-2.4.5/editoria11y.min.css'
        ];

        parent::init();
    }
}

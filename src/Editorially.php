<?php

namespace digitalpulsebe\editorially;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\events\TemplateEvent;
use craft\services\UserPermissions;
use craft\web\View;
use digitalpulsebe\editorially\assets\EditoriallyVendorBundle;
use digitalpulsebe\editorially\models\Settings;
use digitalpulsebe\editorially\services\SessionService;use yii\base\Event;

/**
 * Editoria11y plugin
 *
 * @method static Editorially getInstance()
 * @method Settings getSettings()
 * @property SessionService $sessions
 * @author Digital Pulse nv <support@digitalpulse.be>
 * @copyright Digital Pulse nv
 * @license MIT
 */
class Editorially extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public static function config(): array
    {
        return [
            'components' => [
                'sessions' => SessionService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // after Craft is fully initialized,
        Craft::$app->onInit(function() {
            $this->registerPermissions();
            $this->registerScripts();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('editoria11y/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Register custom permission
     *
     * @return void
     */
    private function registerPermissions(): void
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                $event->permissions[] = [
                    'heading' => 'Editoria11y',
                    'permissions' => [
                        'enableEditoria11y' => [
                            'label' => 'Use Editoria11y by global configuration',
                        ],
                        'startEditoria11ySession' => [
                            'label' => 'Start Editoria11y sessions manually',
                        ],
                    ],
                ];
            }
        );
    }

    private function registerScripts(): void
    {
        if ($this->shouldRegisterScripts()) {
            Craft::$app->view->registerAssetBundle(EditoriallyVendorBundle::class);

            Event::on(
                View::class,
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
                function(RegisterTemplateRootsEvent $event) {
                    $event->roots['editoria11y'] = __DIR__ . '/templates';
                }
            );

            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
                function (TemplateEvent $event) {
                    $variables = [
                        'scriptConfig' => Editorially::getInstance()->getSettings()->scriptConfig ?? '{}'
                    ];

                    $renderedScript = Craft::$app
                        ->view
                        ->renderTemplate(
                            'editoria11y/scripts/editoria11y-craft.twig',
                            $variables
                        );
                    Craft::$app->view->registerHtml($renderedScript);
                }
            );
        }

    }

    protected function shouldRegisterScripts(): bool
    {
        if (!Craft::$app->request->getIsSiteRequest()) {
            // no need to check anything else
            return false;
        }

        if (Craft::$app->user->getIsGuest()) {
            // no need to check anything else
            return false;
        }

        $enabledAutomatically = false;

        // check if enabled by settings
        if ($this->getSettings()->enableOnSiteRequests && !Craft::$app->request->getIsPreview()) {
            $enabledAutomatically = true;
        }
        if ($this->getSettings()->enableOnPreviewRequests && Craft::$app->request->getIsPreview()) {
            $enabledAutomatically = true;
        }

        // check user permissions
        if ($enabledAutomatically) {
            $hasPermission = Craft::$app->user->checkPermission('enableEditoria11y');
        } else {
            $hasPermission = Craft::$app->user->checkPermission('startEditoria11ySession');
        }

        if ($hasPermission) {
            if ($enabledAutomatically) {
                return true;
            } else {
                return $this->sessions->isActive();
            }
        } else {
            return false;
        }
    }
}

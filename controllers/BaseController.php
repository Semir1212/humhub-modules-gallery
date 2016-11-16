<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
namespace humhub\modules\gallery\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\models\User;
use humhub\modules\gallery\permissions\WriteAccess;
use yii\web\HttpException;
use humhub\modules\gallery\models\Gallery;
use yii\base\Model;
use humhub\modules\content\widgets\WallEntry;

/**
 * Description of a Base Controller for the gallery module.
 *
 * @package humhub.modules.gallery.controllers
 * @since 1.0
 * @author Sebastian Stumpf
 */
abstract class BaseController extends ContentContainerController
{

    /**
     * The supported extensions
     */
    public static $validExtensions = [
        'jpg',
        'gif',
        'bmp',
        'svg',
        'tiff',
        'png'
    ];

    /**
     * Checks if user can write
     *
     * @param $throw boolean
     *            default true throws exception if permission failure.
     * @return boolean current user has write acces.
     */
    protected function canWrite($throw = true)
    {
        $permission = false;
        // check if user is on his own profile
        if ($this->contentContainer instanceof User) {
            if ($this->contentContainer->id === Yii::$app->user->getIdentity()->id) {
                $permission = true;
            }
        } else {
            $permission = $this->contentContainer->permissionManager->can(new WriteAccess());
        }
        
        if (! $permission) {
            if ($throw) {
                throw new HttpException(401, Yii::t('GalleryModule.base', 'Insufficient rights to execute this action.'));
            }
            return false;
        }
        return true;
    }

    /**
     * Get a user by id.
     *
     * @param integer $id            
     * @return User the user or null.
     */
    protected function getUserById($id)
    {
        return User::findOne([
            'id' => $id
        ]);
    }

    /**
     * Get the currently open gallery.
     * @url-param 'open-gallery-id' id of the open gallery.
     *
     * @param int $openGalleryId
     *            If specified the id from the url-param is ignored.
     *            
     * @return null | models\Gallery
     */
    abstract protected function getOpenGallery($openGalleryId = null);

    /**
     * Delete an item identified by its type and id: &lt;type&gt;_&lt;id&gt;.
     * Also deletes all subcontent.
     *
     * @param string $id
     *            &lt;type&gt;_&lt;id&gt;.
     */
    protected function deleteItem($itemId)
    {
        $item = $this->module->getItemById($itemId);
        if ($item instanceof Model) {
            return $item->delete();
        }
        
        return false;
    }

    /**
     * Combines and merges given errormessages with the errors from a model.
     *
     * @param array $baseErrors
     *            the errors that should be merged with the model errors.
     * @param array&lt;Model&gt; $models
     *            the models the errors will be extracted from.
     * @param string $prefix
     *            appended at the start of the model error (e.g. model-name).
     * @param boolean $useKey
     *            also append the key from the model errors specifying the attribute name. Default true.
     * @return array&lt;string&gt; the merged errors
     */
    protected function extractAndCombineErrors($baseErrors, $models, $prefix = '', $useKey = true)
    {
        $errors = $baseErrors;
        foreach ($models as $model) {
            $modelErrors = array_map(function ($value, $key) use($prefix, $useKey)
            {
                return $prefix . ($useKey ? $key : '') . ': ' . $value[0];
            }, array_values($model->getErrors()), array_keys($model->getErrors()));
            $errors = array_merge($errors, $modelErrors);
        }
        return $errors;
    }

    /**
     * Check if the given extension is supported.
     *
     * @param string $ext
     *            the extension.
     * @return boolean true if supported.
     */
    protected function isValidExtension($ext)
    {
        return in_array($ext, self::$validExtensions);
    }
}

<?php

namespace app\models;

use app\controllers\UsersController;
use Yii;
use yii\web\UploadedFile;
use yii\httpclient\Client;


/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property double $lat
 * @property double $lng
 * @property string $image
 */
class Users extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address'], 'required'],
            [['lat', 'lng'], 'number'],
            [['name'], 'string', 'max' => 20],
            [['file'], 'image', 'message' => 'Увы! Это не картинка!'],
            [['address', 'image'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'address' => 'Адрес',
            'file' => 'Картинка',
            'image' => 'Картинка',
            'lat' => 'Lat',
            'lng' => 'Lng',
        ];
    }


    public function beforeSave($insert)
    {
        $http_client = new Client([
            'transport' =>  'yii\httpclient\CurlTransport',
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $http_client->createRequest()
            ->setMethod('GET')
            ->setUrl('http://maps.googleapis.com/maps/api/geocode/json?address='.implode('+', explode(' ', $this->address)));


        $response = $request->send();
        $response_headers = $response->headers->toArray();
        Yii::info($response->content);
        $arr = json_decode($response->content, true);
        $lat = $arr['results'][0]['geometry']['location']['lat'];
        $lng = $arr['results'][0]['geometry']['location']['lng'];
        $this->lat = $lat;
        $this->lng = $lng;

        if ($file = UploadedFile::getInstance($this, 'image')) {
            $dir = Yii::getAlias('@webroot') . '/images/';
            $this->image = strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '.' . $file->extension;
            $file->saveAs($dir . $this->image);
            $img = Yii::$app->image->load($dir . $this->image);
            $img->background('#fff', 0);
            $img->resize('50', '50', Yii\image\drivers\Image::INVERSE);
            $img->crop('50', '50');
            $img->save($dir . '50/' . $this->image, 90);
        }
        return parent::beforeSave($insert);
    }

}



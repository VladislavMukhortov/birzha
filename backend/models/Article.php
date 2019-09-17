<?php
declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\data\ActiveDataProvider;

/**
 * @property integer    id
 * @property string     url             url
 * @property string     title           заголовок
 * @property string     description     описание
 * @property string     keywords        ключевые слова
 * @property string     text            текст
 * @property integer    views           кол-во просмотров
 * @property boolean    status          статус
 * @property timestamp  created_at      дата создания
 * @property timestamp  updated_at      дата изменения
 */
class Article extends ActiveRecord
{

    const STATUS_INACTIVE = false;  // неактивная статья
    const STATUS_ACTIVE  = true;    // активная

    const LIMIT_ON_PAGE  = 12;  // кол-во записей на странице



    /**
     * @return string
     */
    public static function tableName() : string
    {
        return '{{%news}}';
    }



    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UTC_TIMESTAMP()')
            ]
        ];
    }



    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['url', 'title'], 'required'],
            [['url', 'title', 'description', 'keywords', 'text'], 'trim'],
            [['url', 'title', 'description', 'keywords'], 'string', 'max' => 255],

            ['url', 'unique'],
            ['url', 'url'],

            ['status', 'boolean'],
        ];
    }



    /**
     * @param  int|string $id
     * @return null|static
     */
    public static function findById($id) : ?self
    {
        return static::findOne(['id' => $id]);
    }



    /**
     * @param  string $url
     * @return null|static
     */
    public static function findIdentityByUrl(string $url) : ?self
    {
        return static::findOne(['url' => $url]);
    }



    /**
     * Устанавливаем url статьи
     * @param string $url
    */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }



    /**
     * Устанавливаем заголовок статьи
     * @param string $title
    */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }



    /**
     * Устанавливаем описание статьи
     * @param string $description
    */
    public function setDescription(string $description = '') : void
    {
        $this->description = $description;
    }



    /**
     * Устанавливаем ключевые слова статьи
     * @param string $keywords
    */
    public function setKeywords(string $keywords = '') : void
    {
        $this->keywords = $keywords;
    }



    /**
     * Устанавливаем текст статьи
     * @param string $text
    */
    public function setText(string $text = '') : void
    {
        $this->text = $text;
    }



    /**
     * Устанавливаем статус
    */
    public function setStatus() : void
    {
        $this->status = self::STATUS_ACTIVE;
    }



    /**
     * @return ActiveDataProvider
     */
    public static function all() : ActiveDataProvider
    {
        $query = static::find()
            ->where([
                'status' => self::STATUS_ACTIVE
            ])
            ->orderBy([
                'id' => SORT_DESC
            ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::LIMIT_ON_PAGE
            ]
        ]);
    }

}

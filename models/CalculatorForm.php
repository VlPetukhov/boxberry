<?php
/**
 * @namespace app\models
 * @class CalculatorForm
 *
 * @property integer $pvzCode
 * @property integer $weight
 * @property integer $orderSum
 * @property integer $deliverySum
 * @property integer $paySum
 */

namespace app\models;

use Yii;
use yii\base\Model;

class CalculatorForm extends Model
{

    const ERROR_MSG = 'Извините, но на сервере произошла ошибка обработки Вашего запроса.';

    /** @var  string $pvzCode */
    public $pvzCode;

    /** @var  integer $weight */
    public $weight;

    /** @var  integer $orderSum */
    public $orderSum = 0;

    /** @var  integer $deliverySum */
    public $deliverySum = 0;

    /** @var  integer $paySum */
    public $paySum = 0;

    /**
     * Rules
     * @return array
     */
    public function rules(){
        return [
            [['pvzCode', 'weight'], 'required'],
            [['pvzCode'], 'string', 'max' => 7],
            [['weight'], 'integer'],
            [['weight'], 'compare', 'operator' => '>', 'compareValue' => 0],
            [['orderSum', 'deliverySum', 'paySum'], 'integer', 'min' => 0],
        ];
    }

    /**
     * Attributes labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'pvzCode'     => 'Пункт выдачи заказов',
            'weight'      => 'Вес посылки в граммах',
            'orderSum'    => 'Объявленная cтоимость товаров',
            'deliverySum' => 'Стоимость доставки, объявленная получателю',
            'paySum'      => 'Сумма, которую необходимо взять с получателя',
        ];
    }

    /**
     * CalculatorForm::getCityPvzArray()
     *
     * Returns array of "PvzCode => AddressReduce" elements for given cityCode or false if error happens
     *
     * @param integer $cityCode
     * @return array|boolean
     */
    public static function getCityPvzArray($cityCode = null){

        if (!is_int($cityCode))
        {
            return false;
        }

        $apiKey = Yii::$app->params['boxberryApiKey'];
        $url = "http://api.boxberry.de/json.php?token=$apiKey&method=ListPoints&CityCode=$cityCode";

        try
        {
            $handle   = fopen($url, "rb");
            $contents = stream_get_contents($handle);
            fclose($handle);
        }
        catch(\Exception $e)
        {
            return false;
        }

        $data = json_decode($contents, true);
        $result = [];

        if (count($data) <= 0 || isset($data[0]['err']))
        {
            return false;
        }
        else
        {
            foreach($data as $item)
            {
                $result[$item['Code']] = $item['AddressReduce'];
            }
        }

        natcasesort($result);

        return $result;
    }

    /**
     * CalculatorForm::getCitiesArray
     *
     * Returns array of "cityCode => CityName" elements or false if error happens
     *
     * @return array|boolean
     */
    public static function getCitiesArray()
    {
        $apiKey = Yii::$app->params['boxberryApiKey'];
        $url = "http://api.boxberry.de/json.php?token=$apiKey&method=ListCities";

        try
        {
            $handle   = fopen($url, "rb");
            $contents = stream_get_contents($handle);
            fclose($handle);
        }
        catch(\Exception $e)
        {
            return false;
        }

        $data = json_decode($contents, true);
        $result = [];

        if (count($data) <= 0 || isset($data[0]['err']))
        {
            return false;
        }
        else
        {
            foreach($data as $item)
            {
                $result[$item['Code']] = $item['Name'];
            }
        }

        natcasesort($result);

        return $result;
    }

    /**
     * Price calculation
     *
     * @return array
     */
    public function getPrice()
    {
        if(!$this->validate())
        {
            return ['err' => self::ERROR_MSG];
        }

        $apiKey = Yii::$app->params['boxberryApiKey'];
        $url = "http://api.boxberry.de/json.php?token=$apiKey&method=DeliveryCosts";

        $url .="&weight={$this->weight}";
        $url .="&target={$this->pvzCode}";
        $url .="&ordersum={$this->orderSum}";
        $url .="&deliverysum={$this->deliverySum}";
        $url .="&paysum={$this->paySum}";

        try
        {
            $handle   = fopen($url, "rb");
            $contents = stream_get_contents($handle);
            fclose($handle);
        }
        catch(\Exception $e)
        {
            return ['err' => self::ERROR_MSG];
        }
        $data = json_decode($contents, true);

        if (count($data) <= 0 || isset($data[0]['err']))
        {
            return   ['err' => self::ERROR_MSG];
        }

        return $data;
    }
}
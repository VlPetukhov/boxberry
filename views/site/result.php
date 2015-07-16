<?php
    /**
     * @var app\models\CalculatorForm $formModel
     */
    $result = $formModel->getPrice();
?>

<h2>Результат расчета</h2>

<?php if(!isset($result['err'])): ?>

    <ul>
        <li>Полная цена: <?=$result['price']?></li>
        <li>Базовая цена: <?=$result['price_base']?></li>
        <li>Цена доставки: <?=$result['price_service']?></li>
    </ul>

    <?php else:?>

    <p><?=$result['err']?></p>

<?php endif; ?>

    <br>
    <hr>
<?= \yii\helpers\Html::a('Вернутся на главную', ['site/index'], ['class' => 'btn btn-primary'])?>
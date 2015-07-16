<?php
    /**
     * @var app\models\CalculatorForm $formModel
     */

    use yii\helpers\Html;
    use yii\web\View;
    use yii\widgets\ActiveForm;

    $script = <<<JS
$( function(){
    $("#pvzDropDown").attr('disabled','disabled');
    $('#cityDropDown').change(function(){
        $.post(
            "/index.php?r=site/getpvz",
            {
                cityId: $('#cityDropDown option:selected').val(),
                _csrf : $('input[name="_csrf"]')[0].value
            },
            onAjaxSuccess
        );
    });
    function onAjaxSuccess(data){
        $('#pvzDropDown option').each(function() {
            $(this).remove();
        });
        for(var key in data){
            console.log( data[key] + '-->' );
            $("<option value=" + key + ">" + data[key] + "</option>").appendTo("#pvzDropDown");
        }
        $("#pvzDropDown").removeAttr('disabled');
    }
});
JS;

    $this->registerJS($script, View::POS_END);

    $form = ActiveForm::begin([
        'id' => 'CalcForm',
        'options' => ['class' => 'form-horizontal'],
    ]);
?>
    <div class="form-group field-calculatorform-pvzcode required">
    <?php echo Html::label('Выберите город для доставки', 'cityName')?>
    <?php echo Html::DropdownList('cityName', null, $formModel::getCitiesArray(), ['id' => 'cityDropDown', 'class' => 'form-control']);?>
    <div class="help-block"></div>
    </div>

<?php echo $form->field($formModel, 'pvzCode')->dropDownList([],['id' => 'pvzDropDown', 'disabled' => 'disabled'])?>

<?php echo $form->field($formModel, 'weight')?>

<?php echo $form->field($formModel, 'orderSum')?>

<?php echo $form->field($formModel, 'deliverySum')?>

<?php echo $form->field($formModel, 'paySum')?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Рассчитать!', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end()?>
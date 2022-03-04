
<?php $this->beginContent('//layouts/main'); ?>
<!-- VK Widget -->
<!-- <div class="sect-left-side"> -->

    <div class="container">

        <div class="index-top-slider clearfix">
            <div class="pull-left slider-block__wrap">
                <?php if($this->id=='site' && $this->action->id == 'index'): ?>
                    <?php $this->widget('widget.SiteSlider.SiteSlider', array('type'=>2)); ?>
                <?php endif; ?>
            </div>
            <div class="pull-right i-t-s-right-wrap">
                <div class="i-t-s-right">
                    <?php
                    $criteria = new CDbCriteria();
                    $criteria->addColumnCondition(['active' => 1]);
                    $criteria->order = 'sort';
                    ?>

                    <?php foreach (BigButton::model()->findAll($criteria) as $item): ?>
                        <div class="i-t-s-right-item">
                            <div class="lent"><?= $item->title ?></div>
                            <a href="<?= $item->link ?>"><img src="/images/button/<?= $item->preview ?>" alt="<?= $item->alt ?>"/></a>
                        </div>
                    <?php endforeach ?>

                    <?php if (false): ?>
                        <div class="i-t-s-right-item">
                            <div class="lent">Цветы к празднику</div>
                            <a href="<?= Yii::app()->createUrl('/shop/category', ['id' => 57]) ?>"><img src="/themes/template_11/img/cvet.jpg" alt="цветы на день рождения"/></a>
                        </div>
                        <div class="i-t-s-right-item">
                            <div class="lent">Букет невесты</div>
                            <a href="<?= Yii::app()->createUrl('/shop/category', ['id' => 48]) ?>"><img src="/themes/template_11/img/232_love.jpg" alt="цветы на любой праздник"/></a>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>



        <?php echo $content; ?>
    </div>





<!--  </div> -->
 <!--
<div class="sect-right-side pull-right">
    <iframe src='/inwidget/index.php?width=250&view=20' scrolling='no' frameborder='no' style='border:none;width:auto;height:425px;overflow:hidden;'>
    </iframe>

        <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

        <div id="vk_groups"></div>
        <script type="text/javascript">
        VK.Widgets.Group("vk_groups", {mode: 0, width: "auto", height: "500", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 119714659);
        </script>

</div> -->
<?php $this->endContent(); ?>

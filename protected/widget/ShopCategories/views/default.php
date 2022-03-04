<div class="shop-menu-wrap">
	<div class="shop-menu-header">
		<div class="shop-mehu-title">Каталог</div>
		<div class="shop-menu-tuggle-batton">
			<div class="shop-menu-tuggle-icon"></div>
			<div class="shop-menu-tuggle-icon"></div>
			<div class="shop-menu-tuggle-icon"></div>
		</div>
	</div>

	<div class="shop-menu-header shop-menu-header-mobile">
      <div class="hidden-block" style="width:28px; height:28px;"></div>
		<div class="mobile-buttons">
	<!--		<div class="mobile-call"> 
				<a href="tel:+74958593194" onclick="ga('send', 'event', 'zvonok', 'click');return true;">Позвонить</a>
			</div>	-->
			<div class="mobile-write">
				<a href="https://api.whatsapp.com/send?phone=79993200047" onclick="ga('send', 'event', 'wazup', 'click');return true;">Написать</a>
			</div>
		</div>
		<div class="shop-menu-tuggle-batton"></div>
	</div>

	<div class="shop-menu-collapsed">
<?php $this->owner->widget('zii.widgets.CMenu', array(
    'items'=>$items,
    'htmlOptions'=>array('class'=>$this->listClass),
    'submenuHtmlOptions'=>array('class'=>'ullvl2')
)); ?>
	</div>
</div>

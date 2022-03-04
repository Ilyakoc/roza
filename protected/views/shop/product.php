<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget', compact('product'))?>

<?php
$offers = $product->offersByPrice;

$cartAttributes = $cartSerialize = [];

if ($offers) {
    $cartAttributes['offer_id'] = '[name="product-offer"]:checked';
}
?>
<div class="tovar-content clearfix">

	<div class="card" itemscope itemtype="http://schema.org/Product">

		<h1  itemprop="name" class="card-title"><?php echo $this->contentTitle?:$product->title; ?></h1>
		<div class="card_features">
			<div class="card_features_slider">
				<div class="slick_slider-nav_block clearfix">
					<div class="slick_slider-for">
						<div class="sl__slide">
							<a href="<?php echo $product->fullImg; ?>" data-fancybox="product"><img src="<?php echo $product->fullImg; ?>" class="sl__img" itemprop="image" alt="<?= (trim(str_replace('"', '', $product->title)) . ' фото') ?>"></a>
						</div>

						<?php foreach($product->moreImages as $id=>$img): ?>
							<div class="sl__slide">
								<a href="<?php echo $img->url; ?>" data-fancybox="product"><img src="<?php echo $img->url; ?>" class="sl__img"></a>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="slick_slider-nav">
						<div class="sl__slide"><img src="<?php echo ResizeHelper::resize($product->fullImg, 68, 68); ?>" class="sl__img"></div>
						<?php foreach($product->moreImages as $id=>$img): ?>
							<div class="sl__slide"><img src="<?php echo ResizeHelper::resize($img->url, 68, 68); ?>" class="sl__img"></div>
						<?php endforeach; ?>
					</div>
					<div class="slick_slider-cactom"></div>
				</div>
			</div>

			<?php if ($offers): ?>
				<?php
				$activeOffer = 0;

				foreach ($offers as $key => $offer) {
					if ($offer->title == 'Стандарт') {
						$activeOffer = $key;
					}
				}
				?>
				<div class="card_features_description">
					<div class="product-offer-labels">
					    <?php $i = 0; foreach ($offers as $key => $offer): ?>
					        <div class="product-offer-label">
					            <label for="offer-<?= $offer->id ?>">
					            <span class="fake-checkbox">
					                <input
					                    type="radio"
					                    id="offer-<?= $offer->id ?>"
					                    name="product-offer"
					                    data-id="<?= $offer->id ?>"
					                    data-price="<?= HtmlHelper::priceFormat($offer->price) ?>"
					                    value="<?= $offer->id ?>"
					                    <?php if($key == $activeOffer) echo 'checked'; ?>
					                    placeholder=""
					                    class="js-change-offer"
					                    data-tab=".js-product-offer_<?= $offer->id ?>"
					                    >
					                <i></i>
					            </span>
					                <span><?= $offer->title ?></span>
					            </label>
					        </div>

					        <?php $i++; ?>
					    <?php endforeach; ?>
					</div>

					<?php
					$cartSerialize['attribute_count'] = ('[data-offer]');
					?>

		    	    <div class="js-product-offers">
					    <?php foreach ($offers as $key => $offer): ?>
			    		    <div class="js-product-offer js-product-offer_<?= $offer->id ?>" style="display: <?= $key == $activeOffer ? 'block' : 'none'; ?>;">
				    		    	<?php if ($offer->height > 0 || $offer->diameter > 0 || $offer->sale_value): ?>
				    		    	<div class="action_block">
				    		    	    <?php if ($offer->height > 0): ?>
				    		    	        <div class="action_block__elem">
				    		    	            <p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $offer->height ?></p>
				    		    	        </div>
				    		    	    <?php endif ?>
				    		    	    <?php if ($offer->diameter > 0): ?>
				    		    	        <div class="action_block__elem">
				    		    	            <p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $offer->diameter ?></p>
				    		    	        </div>
				    		    	    <?php endif ?>
				    		    	    <?php if ($offer->sale_value > 0): ?>
				    		    	        <div class="action_block__elem action_block__elem__active">
				    		    	            <p>-<span><?= $offer->sale_value ?></span>%</p>
				    		    	        </div>
				    		    	    <?php endif ?>
				    		    	</div>
				    		    <?php endif; ?>

			    		        <?php
			    		        /**
			    		         * @var $offer Offer
			    		         */

			    		        $eavAttributes = $offer->eavAttributes;
			    		        ?>

			    		        <?php if ($eavAttributes): ?>
			    		            <div class="desc1-line">
			    		                <b class="desc1-line__title">Состав:</b>
			    		                <ul class="product-composition">
			    		                  <?php foreach ($eavAttributes as $eavAttribute):?>
			    		                    <?php
			    		                    $attribute = $eavAttribute->eavAttribute;

			    		                    if (!$attribute->name || !$eavAttribute->value) {
			    		                    	continue;
			    		                    }
			    		                    ?>
			    		                    <li><span><?= $attribute->name ?>:</span> <span><?= $eavAttribute->value ?> шт</span></li>
			    		                  <?php endforeach;?>
			    		                </ul>
			    		            </div>
			    		        <?php elseif (!empty($product->composition)): ?>
			    		            <div class="desc1-line">
			    		                <b class="desc1-line__title">Состав:</b> <?php echo $product->composition; ?>
			    		            </div>
			    		        <?php endif; ?>
			    		    </div>
					    <?php endforeach ?>
		    	    </div>

		    	    <?php if (!empty($product->description)): ?>
		    	        <div class="desc1-line">
		    	            <b class="desc1-line__title">Описание:</b> <span itemprop="description"><?php echo $product->description; ?></span>
		    	        </div>
		    	    <? else: ?>
		    	        <meta itemprop="description" content="&nbsp;" />
		    	    <?php endif; ?>
			    </div>

			    <script>
			    	$(function() {
			    		$('.js-change-offer').change(function() {
			    		    var $el = $(this);
			    		    var offerID = $el.data('id');

			    		    $('.js-product-offer').hide();
			    		    $($el.data('tab')).show();

			    		    $('.js-product-price').text($el.data('price'));
			    		});
			    	});
			    </script>
			<?php else: ?>
				<div class="card_features_description">
					<div class="action_block">
						<?php if ($product->height > 0): ?>
							<div class="action_block__elem">
								<p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $product->height ?></p>
							</div>
						<?php endif ?>
						<?php if ($product->diameter > 0): ?>
							<div class="action_block__elem">
								<p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $product->diameter ?></p>
							</div>
						<?php endif ?>
						<?php if ($product->sale_value > 0): ?>
							<div class="action_block__elem action_block__elem__active">
								<p>-<span><?= $product->sale_value ?></span>%</p>
							</div>
						<?php endif ?>
					</div>
					<?php if ($product->productAttributes): ?>
						<div class="desc1-line">
							<b class="desc1-line__title">Состав:</b>
							<ul>
							  <?php foreach ($product->productAttributes as $productAttribute):?>
								<li><span><?=$productAttribute->eavAttribute->name;?>:</span> <span><?=$productAttribute->value;?> шт</span></li>
							  <?php endforeach;?>
							</ul>
						</div>
					<?php elseif (!empty($product->composition)): ?>
						<div class="desc1-line">
							<b class="desc1-line__title">Состав:</b> <?php echo $product->composition; ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($product->description)): ?>
						<div class="desc1-line">
							<b class="desc1-line__title">Описание:</b> <span itemprop="description"><?php echo $product->description; ?></span>
						</div>
	                <? else: ?>
	                    <meta itemprop="description" content="&nbsp;" />
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="card_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <meta itemprop="priceCurrency" content="RUB" />
			<meta itemprop="price" content="<?=$product->price?>" />
			<div class="desc1-line desc1_price"><span class="js-product-price"><?php echo HtmlHelper::priceFormat($offers ? ($activeOffer ? $offers[$activeOffer]->price : $offers[0]->price) : $product->price); ?></span><span>.-</span></div>

			<div class="tovar-last-bl clearfix">
				<div class="pull-left inpwrapp">
					<div class="inp-minus">-</div>
					<input type="text" name="COUNT" id="COUNT" value="1">
					<div class="inp-plus">+</div>
				</div>

				<?php if ($product->notexist): ?>
		            нет в наличии
		            <?php else: ?>
		            <noindex><a rel="nofollow" href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$product->id)) ?>" data-cart-attributes='<?= json_encode($cartAttributes) ?>' class="goobasket-btn">Купить</a></noindex>
	            <?php endif; ?>

			</div>

			<div class="card_price_link">
				<p><img src="/themes/template_11/img/card_price_link1.png" alt=""/><a href="#quality_text" class="fancybox">Гарантия качества</a></p>
				<!-- <p><img src="/themes/template_11/img/card_price_link2.png" alt=""/><a href="#return_text" class="fancybox">Условия возврата</a></p> -->
				<p><img src="/themes/template_11/img/card_price_link3.png" alt=""/><a href="#delivery_text" class="fancybox">Подробнее о доставке</a></p>
				<p class="card_price_link_back">
					<span>
						Вернуться в категорию:
						<br>
						<a href="<?= Yii::app()->createUrl('/shop/category', ['id' => $product->category->id]) ?>"><?= $product->category->title ?></a>
					</span>
				</p>
			</div>

		</div>
		<div class="clear"></div>
	</div>

		<div class="block-promise">
	<div class="block-promise-content">
		<div class="block-promise-content-item">
			<img src="/dist/img/free-shipping.png" alt="prom">
			<p>Бесплатная доставка
	за 2 часа</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/spring.png" alt="prom">
			<p>Стойкость до
	двух недель</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/landline.png" alt="prom">
			<p>Доставка только
	по номеру телефона</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/postcard.png" alt="prom">
			<p>Открытка в подарок</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/gift.png" alt="prom">
			<p>Подарки к каждому
	празднику</p>
		</div>
	</div>
	</div>

</div>

<?php if ($product->category_id != 34 && $product->id != 801): ?>
	<h2 class="h2">
		<span>Ваши бонусы</span>
	</h2>

 	<div class="slider-block">
		<ul class="slider-block-list">
			<?php
			$criteria = new CDbCriteria();

			$category = Category::model()->findByPk(34);
			$allcat = $category->descendants()->findAll();
			$ids[] = $category->id;
			foreach($allcat as $cat)
				$ids[] = $cat->id;
	        $criteria = new CDbCriteria();
	        $criteria->addInCondition('`t`.`category_id`', $ids, 'OR');
	        $criteria->mergeWith($product->getRelatedCriteria($ids), 'OR');

			$criteria->addCondition('price = 0');

			$bonusProducts = Product::model()->findAll($criteria);
			?>

			<?php foreach($bonusProducts as $bonusProduct) {?>
			<li>
				<div class="list-bl-wrapp">
					<div class="list-bl-wrapp-item">
						<?php if ($bonusProduct->height > 0 || $bonusProduct->diameter > 0 || $bonusProduct->sale_value): ?>
							<div class="action_block">
								<?php if ($bonusProduct->height > 0): ?>
									<div class="action_block__elem">
										<p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $bonusProduct->height ?></p>
									</div>
								<?php endif ?>
								<?php if ($bonusProduct->diameter > 0): ?>
									<div class="action_block__elem">
										<p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $bonusProduct->diameter ?></p>
									</div>
								<?php endif ?>
								<?php if ($bonusProduct->sale_value): ?>
									<div class="action_block__elem action_block__elem__active">
										<p>-<span><?= $bonusProduct->sale_value ?></span>%</p>
									</div>
								<?php endif ?>
							</div>
						<?php endif ?>
						<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($bonusProduct->mainImg,'?',true),270,240,3600) . '" alt="" />', array('shop/product', 'id'=>$bonusProduct->id), array('class'=>'slider-hed-img')) ?>
						<?php echo CHtml::link($bonusProduct->title, array('shop/product', 'id'=>$bonusProduct->id), array('class'=>'slider-hed-name')) ?>
						<div class="slider-priceje_bottom">
							<div class="slider-priceje_left">
								<div class="slider-priceje"><span><?php echo $bonusProduct->price; ?></span>.-</div>
							</div>
							<div class="slider-priceje_right">
								<?php if (!$bonusProduct->notexist): ?>
									<noindex><a rel="nofollow" class="goobasket-btn goorder-button " href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$bonusProduct->id)) ?>"><span>Купить</span><img src="<?php echo $this->template; ?>/img/icon_bck.png" alt="" /></a></noindex>
								<?php endif; ?>
							</div>
						</div>

					</div>
				</div>
			</li>
			<?php }?>
		</ul>
	</div>
<?php endif; ?>

<?php
$relatedProducts = [];

foreach ($product->relatedProducts as $relatedProduct) {
	if ($related = Product::model()->findByPk($relatedProduct->related_id)) {
		$relatedProducts[] = $related;
	}
}
?>

<?php if ($relatedProducts): ?>
	<h2 class="h2">
		<span>Сопутствующие товары</span>
	</h2>
	 <div class="slider-block">
		<ul class="slider-block-list">
			<?php foreach($relatedProducts as $related) {?>
			<?php
			// $related = Product::model()->findByPk($relatedProduct->related_id);
			?>
			<li>
				<div class="list-bl-wrapp">
					<div class="list-bl-wrapp-item">
						<div class="action_block">
							<?php if ($related->height > 0): ?>
								<div class="action_block__elem">
									<p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $related->height ?></p>
								</div>
							<?php endif ?>
							<?php if ($related->diameter > 0): ?>
								<div class="action_block__elem">
									<p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $related->diameter ?></p>
								</div>
							<?php endif ?>
							<?php if ($related->sale_value): ?>
								<div class="action_block__elem action_block__elem__active">
									<p>-<span><?= $related->sale_value ?></span>%</p>
								</div>
							<?php endif ?>
						</div>
						<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($related->mainImg,'?',true),270,240,3600) . '" alt="" />', array('shop/product', 'id'=>$related->id), array('class'=>'slider-hed-img')) ?>
						<?php echo CHtml::link($related->title, array('shop/product', 'id'=>$related->id), array('class'=>'slider-hed-name sks')) ?>
						<div class="slider-priceje_bottom">
							<div class="slider-priceje_left">
								<div class="slider-priceje"><span><?php echo $related->price; ?></span>.-</div>
							</div>
							<div class="slider-priceje_right">
								<?php if (!$related->notexist): ?>
									<noindex><a rel="nofollow" class="goobasket-btn goorder-button " href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$related->id)) ?>"><span>Купить</span><img src="<?php echo $this->template; ?>/img/icon_bck.png" alt="" /></a></noindex>
								<?php endif; ?>
							</div>
						</div>

					</div>
				</div>
			</li>
			<?php }?>
		</ul>
	</div>
<?php endif ?>

<h2 class="h2">
	<span>Похожие товары</span>
</h2>
 <div class="slider-block">
	<ul class="slider-block-list">
		<?php
		$criteria = new CDbCriteria();
		$criteria->addCondition('id != ' . $product->id);
		$criteria->limit = 4;
		$criteria->addCondition('category_id = ' . $product->category_id);
		$criteria->order = 'RAND()';
		?>
		<?php foreach(Product::model()->findAll($criteria) as $related) {?>
		<li>
			<div class="list-bl-wrapp">
				<div class="list-bl-wrapp-item">
					<div class="action_block">
						<?php if ($related->height > 0): ?>
							<div class="action_block__elem">
								<p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $related->height ?></p>
							</div>
						<?php endif ?>
						<?php if ($related->diameter > 0): ?>
							<div class="action_block__elem">
								<p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $related->diameter ?></p>
							</div>
						<?php endif ?>
						<?php if ($related->sale_value): ?>
							<div class="action_block__elem action_block__elem__active">
								<p>-<span><?= $related->sale_value ?></span>%</p>
							</div>
						<?php endif ?>
					</div>
					<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($related->mainImg,'?',true),270,240,3600) . '" alt="" />', array('shop/product', 'id'=>$related->id), array('class'=>'slider-hed-img')) ?>
					<?php echo CHtml::link($related->title, array('shop/product', 'id'=>$related->id), array('class'=>'slider-hed-name')) ?>
					<div class="slider-priceje_bottom">
						<div class="slider-priceje_left">
							<div class="slider-priceje"><span><?php echo $related->price; ?></span>.-</div>
						</div>
						<div class="slider-priceje_right">
							<?php if (!$related->notexist): ?>
								<noindex><a rel="nofollow" class="goobasket-btn goorder-button " href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$related->id)) ?>"><span>Купить</span><img src="<?php echo $this->template; ?>/img/icon_bck.png" alt="" /></a></noindex>
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</li>
		<?php }?>
	</ul>
</div>

<?php $this->renderPartial('_category_js'); ?>

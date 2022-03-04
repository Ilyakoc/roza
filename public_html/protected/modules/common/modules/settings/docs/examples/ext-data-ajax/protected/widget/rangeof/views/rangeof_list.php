<? 
if($dataProvider->totalItemCount): 
	?><div class="site-bar-list row"><?
		foreach($dataProvider->getData() as $data): 
			$item=$data['item'];
			$item->image=$item->imageBehavior->getAttributeValueForcy();
			?><div class="site-bar-item col-sm-3">
				<a href="<?=$this->owner->createUrl('shop/filter', ['filter'=>['rangeof'=>$item->code]])?>">				
					<div class="sv-images"><?= $item->imageBehavior->img(260, 115, true, ['class'=>'img-responsive']); ?></div>
					<span class="sb-text-block">
						<span class="sb-text-row">
							<p><?= $item->title; ?></p>
						</span>
					</span>
				</a>
			</div><? 
		endforeach; 
	?></div><? 
endif; 
?>	
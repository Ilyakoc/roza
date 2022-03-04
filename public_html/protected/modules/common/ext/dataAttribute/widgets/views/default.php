<?php
/** @var DataAttributeWidget $this */  
use common\components\helpers\HArray as A;

?>
<?= CHtml::openTag('div', A::m($this->wrapperOptions, ['class'=>'daw-wrapper', 'data-item'=>$this->attribute])); ?>
	<table class="daw-template" style="display: none !important;"><tbody><?= $this->generateRow(null); ?></tbody></table>
	<table class="daw-table table table-bordered table-striped" border=0 cellpadding="0" cellspacing="0">
		<thead>
			<tr class="info">
				<? if(!$this->hideActive): ?><th class="daw-thead-visible" title="Отображать на сайте">Акт.</th><? endif; ?>
				<? foreach($this->header as $title=>$params):
					if(is_array($params)) {
						$title=A::get($params, 'title', $title);
					}
					else {
						$title=(string)$params;
						$params=[];
					}
					$htmlOptions=A::get($params, 'htmlOptions', []);
					echo \CHtml::tag('th', $htmlOptions, $title);
				endforeach; ?>
				<? if(!$this->hideDeleteButton):?><th class="daw-thead-remove">Удалить</th><? endif; ?>
			</tr>
		</thead>
		<? if(!$this->hideAddButton): ?>
		<tfoot>
			<tr>
				<td colspan="<?= count($this->header)+2; ?>">
					<button class="btn btn-primary btn-sm daw-btn-add" data-attribute="<?=$this->attribute?>">Добавить</button>
				</td>
			</tr>
		</tfoot>
		<? endif; ?>
		<tbody>
			<? 
			$index=0; 
			foreach($this->getData() as $data) echo $this->generateRow($index++, $data);
			?>
		</tbody>
	</table>
</div>

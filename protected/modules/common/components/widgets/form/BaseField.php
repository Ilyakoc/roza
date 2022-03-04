<? 
/**
 * Основной класс для виджетов полей формы
 *
 */
namespace common\components\widgets\form;

class BaseField extends \common\components\base\Widget
{
	/**
	 * @var \CActiveForm объект формы.
	 */
	public $form;
	
	/**
	 * @var \CModel объект модели
	 */
	public $model;
	
	/**
	 * @var string имя атрибута.
	 */
	public $attribute;
	
	/**
	 * @var string имя шаблона представления. 
	 */
	public $view;
	
	/**
	 * @var array дополнительные HTML-атрибуты для основного элемента формы.
	 */
	public $htmlOptions=['class'=>'form-control'];
	
	/**
	 * @var string имя тэга обертки.
	 * Может быть передано пустое значение, тогда тэг отображаться не будет.
	 */
	public $tag='div';
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента обертки.
	 */
	public $tagOptions=['class'=>'row'];
	
	/**
	 * @var bool не отображать метку атрибута. 
	 */
	public $hideLabel=false;
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента метки.
	 */
	public $labelOptions=[];
	
	/**
	 * @var bool|NULL не отображать блок ошибки.
	 * Так как, \CActiveForm::$enableClientValidation не работает, 
	 * если у поля нет элемента ошибки, поэтому сокрытие следует делать,
	 * через обертку со стилем "display:none" (напр. <div style="display:none">...</div>). 
	 * Может быть передано NULL. В этом случае следует принудительно не выводить элемент.
	 */
	public $hideError=false;

	/**
	 * @var string тэг контейнера скрытия ошибки. По умолчанию 'div';
	 */
	public $hideErrorTag='div';
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента ошибки.
	 */
	public $errorOptions=[];
	
	/**
	 * @var string текст комментария к полю. По умолчанию (false) не задан.
	 */
	public $note=false;
	
	/**
	 * @var string местоположение комментария. По умолчанию "right".
	 */
	public $notePlacement = 'left';
	
	/**
	 * @var string заголовок комментария. По умолчанию (false) не задан.
	 */
	public $noteTitle = false;
	
	/**
	 * @var string имя тэга элемента комментария к полю. По умолчанию "p".
	 */
	public $noteTag='p';
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента комментария. 
	 */
	public $noteOptions=['class'=>'small alert alert-info', 'style'=>'padding:5px;'];
	
	/**
	 * @var array шаблон отображения комментария. По умолчанию "popover".
	 * Доступны шаблоны: popover, tooltip.
	 */
	public $noteView = 'popover';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->render($this->view, $this->params);
	}
	
	/**
	 * Получить html-тэг открытия обертки.
	 * @return string
	 */
	public function openTag()
	{
		if(!empty($this->tag)) {
			return \CHtml::openTag($this->tag, $this->tagOptions);
		}
		
		return '';
	}
	
	/**
	 * Получить html-тэг закрытия обертки.
	 * @return string
	 */
	public function closeTag()
	{
		if(!empty($this->tag)) {
			return \CHtml::closeTag($this->tag);
		}
		
		return ''; 
	}
	
	/**
	 * Получить html-тэг метки.
	 * @return string
	 */
	public function labelTag()
	{
		if(!$this->hideLabel) {
			return $this->form->labelEx($this->model, $this->attribute, $this->labelOptions);
		}
		
		return '';
	}
	
	/**
	 * Получить html-тэг ошибки.
	 * @return string
	 */
	public function errorTag()
	{
		$html='';
	
		if($this->hideError !== null) {
			if($this->hideError) $html.=\CHtml::openTag($this->hideErrorTag, ['style'=>'display:none']);
			$html.=$this->form->error($this->model, $this->attribute, $this->errorOptions);
			if($this->hideError) $html.=\CHtml::closeTag($this->hideErrorTag); 
		}
		
		return $html;
	}
}
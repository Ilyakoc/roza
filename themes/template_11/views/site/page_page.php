<?$this->widget('widget.breadcrumbs.BreadcrumbsPageWidget', array('model'=>$page))?>
<?if(!$this->isIndex() && ($page->id!=18)):?>
<h1><?=$page->title?></h1>
<?endif?>

<?=$page->text?>

<?if($page->blog && $page->blog->id) {
	$url=$this->createUrl('site/blog', array('id'=>$page->blog->id));
	echo HtmlHelper::linkBack('Назад', $url, $url);
}?>

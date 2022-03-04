<?php $this->beginContent('//layouts/main'); ?>
<div class="sect-right-side pull-right">
               <iframe src='/inwidget/index.php?width=250&view=20' scrolling='no' frameborder='no' style='border:none;width:255px;height:425px;overflow:hidden;'></iframe>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?127"></script>

<!-- VK Widget -->
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 4, wide: 1, width: "255", height: "500", color1: 'FFFFFF', color2: '000000', color3: '5E81A8'}, 119714659);
</script>
            </div>
<div class="sect-left-side">
                    <div class="index-top-slider">
                        <div class="pull-right i-t-s-right">
                            <div class="i-t-s-right-item">
                                <div class="lent">В день рождения!</div>
                                <a href="/shop/category/58"><img src="/themes/template_11/img/cvet.jpg" alt="цветы на день рождения"/></a>
                            </div>
                            <div class="i-t-s-right-item">
                                <div class="lent">На День Победы!</div>
                                <a href="/shop/category/37"><img src="/themes/template_11/img/may.jpg" alt="цветы на день победы"/></a>
                            </div>
                        </div>
                        <?php if($this->id=='site' && $this->action->id == 'index'): ?>
							<?php $this->widget('widget.SiteSlider.SiteSlider', array('type'=>2)); ?>
							
                        <?php endif; ?>
                    </div>
                    
                    <?php echo $content; ?>
                
            </div>
<?php $this->endContent(); ?>

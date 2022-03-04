<?php
/* @var int $pages */
/* @var CImage[] $images */

$count_in_page = 1;
$i = 1;
$all_count = 1;
?>
<div id="images-gallery" class="images-gallery">
    <?php if ($pages > 1) { ?><div class="page clearfix"><?php } ?>
    <?php foreach($images as $img): ?>
        <div class="gallery-item">
            <a href="/images/<?php echo $img->model.'/'.$img->filename; ?>" rel="images-gallery"
               title="<?php echo $img->description; ?>"><img src="/images/<?php echo $img->model.'/tmb_'.$img->filename; ?>" alt="<?php echo $img->description; ?>" /></a>
        </div>

        <?php if ($i % 3 == 0 && count($images) > 3) {
            // echo '<div class="clr"></div>';
        }

        if ($pages > 1) {
            if ($count_in_page == $this->countPerPage) {
                echo '</div>';
                if ($i<count($images))
                    echo '<div class="page clearfix">';
                $count_in_page = 0;
                $i = 0;
            }

            if ($all_count==count($images)) {
                echo '</div>';
            }

            $count_in_page++;
        } $i++; $all_count++; ?>
    <?php endforeach; ?>
</div>

<?php if ($pages > 1): ?>
<ul class="pager" id="image-gallery-pages">
    <?php for($i=1;$i<=$pages;$i++): ?>
    <li><a show-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
</ul>

<style type="text/css">
    ul.pager {}
    ul.pager li {display: inline-block; margin-right: 5px;}
    ul.pager li a {cursor: pointer;}
    ul.pager li a:hover {text-decoration: underline;}
    ul.pager li.active {font-weight: bold;}
</style>
<?php endif; ?>
    
<script type="text/javascript">
    $(function(){
        $("#images-gallery a").fancybox({
            'autoDimensions':  false,
            'autoScale'     :  false,
            'transitionIn'	: 'elastic',
		    'transitionOut'	: 'elastic',
            'titlePosition' : 'over'
        });

        var pages = $('#images-gallery .page');
        var links = $('#image-gallery-pages li');

        $(links).each(function(index, item) {
            if (index == 0) {
                $(item).addClass('active');
                if (pages[index] != undefined)
                    $(pages[index]).addClass('show');

            }
            $(item).click(function() {
                $(links).removeClass('active');
                $(this).addClass('active');

                if (pages[index] !== undefined) {
                    $(pages).removeClass('show');
                    $(pages[index]).addClass('show');
                }
            });
        });
    });
</script>

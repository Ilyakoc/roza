<div id="menu-types-wrap">
    <dl id="menu-types">
        <dt><a class="dashed"><?php echo $types[$this->owner->id]; ?></a></dt>

        <?php foreach($types as $id=>$type): ?>
            <?php if ($id == $this->owner->id) continue?>
            <dd class="hide"><?php echo CHtml::link($type, array($id .'/create'), array('class'=>'dashed')); ?></dd>
        <?php endforeach; ?>
    </dl>

    <script type="text/javascript">
        $(function() {
            $('#menu-types dt a').click(function() {
                $('#menu-types dd').toggleClass('hide');
            });
        });
    </script>
</div>
<div class="clr"></div>

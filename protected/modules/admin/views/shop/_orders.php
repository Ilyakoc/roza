<div id="orders-list-module">
    <h3>Заказы</h3>

    <?php if (count($orders)): ?>
    <ol id="order-list" class="order-list">
        <?php foreach($orders as $o) :?>
        <li>
            <a class="js-link"><?php echo $o->name; ?></a>, <span class="info"><?php printf('%s руб., %s', $o->summaryPrice, $o->date); ?></span>
            <div class="full-info hidden">
                <p><?php printf('<a href="mailto:%s">%s</a>', $o->email, $o->email); ?>, тел.<?php echo $o->phone; ?></p>
                <table>
                    <thead>
                    <tr>
                        <th>Название</th>
                        <th>Кол-во</th>
                        <th>Цена</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($o->products as $p): ?>
                    <tr>
                        <td><?php echo $p->title .' / '. $p->code; ?></td>
                        <td><?php echo $p->count; ?></td>
                        <td><?php echo $p->order_price; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
    <script type="text/javascript">
        $(function() {
            $('#order-list a.js-link').click(function() {
                $(this).parent().find('.full-info').toggleClass('hidden');
            });
        });
    </script>
    <?php else: ?>
    <p>Нет заказов</p>
    <?php endif; ?>
</div>

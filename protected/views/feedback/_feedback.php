<?php
/**
 * File: _feedback.php / 05.06.13, 17:20
 * @author Mobyman
 */
?>
<div class="item">
	<span class="username"><?php echo $data->username; ?> / <?php echo DateTime::createFromFormat('Y-m-d H:i:s', $data->created)->format('d.m.Y'); ?></span>
	<a class="question"><?php echo $data->question; ?></a>
	<div class="answer show"><?php echo $data->answer ?></div>
</div>
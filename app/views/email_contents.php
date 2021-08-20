<div class="container">

<pre>
<?php foreach ($email_contents as $lang => $sections) : ?>
<strong>[<?php echo $lang; ?>]</strong>
<?php   foreach ($sections as $sec => $contents) : ?>
    <b><?php echo $sec; ?>:</b>
<form method="post" action="/email/update">
    <textarea cols=100 rows=5 name="contents"><?php echo $contents["val"]; ?></textarea>
    <input type="hidden" name="prod_plat" value="<?php echo $production . "_" . $platform; ?>">
    <input type="hidden" name="id" value="<?php echo $contents["id"]; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="submit" value="Update">
</form>
<?php   endforeach ?>
</form>
<?php endforeach ?>
<?php print_r($email_contents); ?>
</pre>

</div>

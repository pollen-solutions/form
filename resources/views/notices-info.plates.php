<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var string[] $messages
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($messages = $this->get('messages')) : ?>
<ol class="Notice-items FormNotice-items FormNotice-items--info">
    <?php foreach ($messages as $message) : ?>
        <li class="Notice-item FormNotice-item FormNotice-item--info"><?php echo $message; ?></li>
    <?php endforeach; ?>
</ol>
<?php endif;
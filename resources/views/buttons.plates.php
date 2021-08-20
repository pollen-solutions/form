<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\ButtonDriverInterface[] $buttons
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($buttons = $this->get('buttons', [])) : ?>
    <div class="FormButtons">
        <?php foreach ($buttons as $button) : ?>
            <?php $this->insert('button', compact('button', 'form')); ?>
        <?php endforeach; ?>
    </div>
<?php endif;
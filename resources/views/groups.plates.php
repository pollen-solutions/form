<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FieldGroupDriverInterface $group
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($groups = $this->get('groups')) : ?>
    <?php foreach ($groups as $group) : ?>
        <?php $this->insert('group', compact('group', 'form')); ?>
    <?php endforeach; ?>
<?php endif;

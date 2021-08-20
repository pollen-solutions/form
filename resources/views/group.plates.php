<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FieldGroupDriverInterface $group
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php echo $group->before(); ?>
<div <?php echo $group->getAttrs(); ?>>
    <?php foreach ($group->getFormFields() as $field) : ?>
        <?php $this->insert('field', compact('field', 'form')); ?>
    <?php endforeach; ?>
</div>
<?php echo $group->after();
<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\Factory\FormFieldsFactoryInterface $fields
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($fields->count()) : ?>
    <div class="FormRows">
        <?php if ($form->hasGroup()) : ?>
            <?php $this->insert('groups', $this->all()); ?>
        <?php else : ?>
            <?php foreach($form->formFields() as $field) : ?>
                <?php $this->insert('field',compact('field', 'form')); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

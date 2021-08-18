<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FieldGroupDriverInterface $group
 */
?>
<?php if ($groups = $this->get('groups')) : ?>
    <?php foreach ($groups as $group) : ?>
        <?php $this->insert('group', compact('group')); ?>
    <?php endforeach; ?>
<?php endif;

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Form\Example1Form $example1
 */
$stimulusLink = $this->Html->link(__('Stimulus'), 'https://stimulusjs.org', ['target' => 'stimulus']);
?>
<?= $this->Form->create($example1, ['valueSources' => 'context']) ?>
<nav class="example1 large-3 medium-4 columns" id="actions-sidebar">
    <!-- ul class="side-nav">
        <li class="heading"><?= __('Actions') ?>
        <li><?= $this->Html->link(__('Example 2'), ['controller' => 'Example2', 'action' => 'index']) ?>
    </ul -->
    <div class="panel"><p><?= __('Example demonstrates partial page rendering with {0}. An event can trigger a Stimulus component to perform an ajax call to load a server-side generated html page fragment. The fragment appends or replaces a part of the page. The fragment can contain other stimulus components. Entered data can be preserved. Form submittion still works as normal.', $stimulusLink) ?></div>
    <div class="panel"><p><?= __('In the form, if the number of prices is changed, the section below it will be replaced with a new html fragment.') ?></div>
    <div class="panel"><p><?= __('In the table with participants a new row for a new participant can be added via the + button. The new row is a server-side generated html fragment.') ?></div>
</nav>
<div class="example1 contest form large-9 medium-8 columns content">
<h1><?= __('Demo CakePHP with {0}', $stimulusLink) ?></h1> 
    <fieldset>
        <legend><?= __('Edit contest') ?></legend>
        <?php
          echo $this->Form->control('contest.name', [
            'required' => true,
            'label' => __('Contest name')
          ]);
          echo $this->Form->control('contest.number_of_prices', [
            'required' => true,
            'type' => 'select', 
            'label' => __('Number of prices'),
            'options' => [0,1,2,3]
          ]);
          echo $this->element('Example1/Price');
        ?>
        <table class="participants" cellpadding="0" cellspacing="0">
            <caption><?= __('Participants') ?></caption>
            <thead>
                <tr>
                   <th scope="col" class="col_delete">
                   <th scope="col"><?= __('Name') ?>
                   <th scope="col"><?= __('Email') ?>
                   <th scope="col" class="col_date_of_birth"><?= __('Date of birth') ?>
                </tr>
            </thead>
            <tbody>
                <?php
                  echo $this->element('Example1/ContestParticipants', [
                    "participants" => $example1->getData('contest.participants'),
                    "participants_offset" => 0,
                  ]);
                ?>
             </tbody>
             <tfoot>
                <tr class="plusline">
                   <td>
                   <td><?= $this->Form->button(__('+'), ['name' => 'action', 'value' => 'addParticipant', 'class' => 'button plus']) ?>
                   <td>
                </tr>
            </tfoot>
        </table>
        <?= $this->Form->button(__('Submit'), ['name' => 'action', 'value' => 'update', 'class' => 'button round']) ?>
    </fieldset>
</div>
<?= $this->Form->end() ?>

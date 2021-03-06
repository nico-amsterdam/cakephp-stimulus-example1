<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Form\Example1Form $example1
 * @var string $dateType
 */


$this->assign('script', $this->Html->script('stimulus_v1_0'));
$this->assign('title', __('Example 1'));
$this->assign('description', __('Demo of partial page rendering in a CakePHP application using Stimulus.'));
$stimulusLink = $this->Html->link(__('Stimulus'), 'https://stimulusjs.org', ['target' => 'stimulus', 'rel' => 'noopener']);
$cakephpLink  = $this->Html->link(__('CakePHP'),  'https://cakephp.org',    ['target' => 'cakephp' , 'rel' => 'noopener']);
$updatePrizeRegionUrl = $this->Url->build('/example1/prize_snippet',           []);
$addParticipantUrl    = $this->Url->build('/example1/add_participant_snippet', []);

?>
<?= $this->Form->create($example1) ?>
<nav class="example1 large-3 medium-4 columns" id="actions-sidebar">
    <!-- ul class="side-nav">
        <li class="heading"><?= __('Actions') ?>
        <li><?= $this->Html->link(__('Example 2'), ['controller' => 'Example2', 'action' => 'index']) ?>
    </ul -->
    <div class="panel"><p><?= __('Example demonstrates partial page rendering with {0}. An event triggers a Stimulus component to perform an ajax call to load a server-side generated html page fragment. The fragment appends or replaces a part of the page. The fragment can contain other Stimulus components with dynamic behavior. Entered data can be preserved. Form submission still works as normal.', $stimulusLink) ?></div>
    <div class="panel"><p><?= __('In the form, if the number of prizes is changed, the section below it will be replaced with a new html fragment.') ?></div>
    <div class="panel"><p><?= __('In the table with participants a new row for a new participant can be added via the + button. The new row is a server-side generated html fragment.') ?></div>
</nav>
<div class="example1 contest form large-9 medium-8 columns content">
<h1><?= __('Demo {0} with {1}', $cakephpLink, $stimulusLink) ?></h1> 
    <fieldset>
        <legend><?= __('Edit contest') ?></legend>
        <?php
          echo $this->Form->control('contest.name', [
          'required' => true,
          'label' => __('Contest name'),
        ]);?>
        <div id="prize-panel" data-controller="common--loader" data-common--loader-url1="<?= $updatePrizeRegionUrl ?>"> 
          <?php
            echo $this->Form->control('contest.number_of_prizes', [
              'required' => true,
              'type' => 'select', 
              'label' => __('Number of prizes'),
              'options' => [0,1,2,3],
              'size' => 4,
              'data-action' => 'change->common--loader#update',
            ]); ?>
            <div data-target="common--loader.output1">
                <?= $this->element('Example1/Prize'); ?>
            </div>
        </div>
        <?= $this->Form->button(__('Submit'), ['name' => 'example1action', 'value' => 'update', 'class' => 'invisible', 'id' => 'default_button']) ?>
        <table class="participants" id="participant-table" data-controller="common--loader" data-common--loader-url1="<?= $addParticipantUrl ?>" data-common--loader-append1> 
            <caption><?= __('Participants') ?></caption>
            <thead>
                <tr>
                   <th scope="col" class="col_name"><?= __('Name') ?>
                   <th scope="col" class="col_email"><?= __('Email') ?>
                   <th scope="col" class="col_date_of_birth"><?= __('Date of birth') ?>
                   <th scope="col" class="col_delete">
                </tr>
            </thead>
            <tbody id="tbody" data-target="common--loader.output1">
                <?php
                  echo $this->element('Example1/ContestParticipants', [
                    "participants" => $example1->getData('contest.participants'),
                    "participants_offset" => 0,
                  ]);
                ?>
            </tbody>
            <tfoot>
                <tr class="plusline">
                   <td><?= $this->Form->button(__('+'), ['name' => 'example1action', 'value' => 'addParticipant', 'class' => 'button plus', 'id' => 'add', 'data-action' => 'click->common--loader#update']) ?>
                   <td>
                   <td>
                   <td>
                </tr>
            </tfoot>
        </table>
        <?= $this->Form->button(__('Submit'), ['name' => 'example1action', 'value' => 'update', 'class' => 'button round', 'id' => 'submit']) ?>
    </fieldset>
</div>
<?= $this->Form->end() ?>

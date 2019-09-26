<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contest $contest
 * @var string $dateType
 */

?>
 <div class="container" data-controller="example1--prize" id="prize-panel-div">
  <?php $number_of_prizes = $contest['number_of_prizes']; ?>
  <div class="formpanel prizes total<?= h($number_of_prizes) ?>prizes"> 
    <?php
       echo $this->Form->control('prize1', $number_of_prizes >= 1 ? ['required' => true, 'data-action' => 'keyup->example1--prize#update', 'label' => __('First prize')]  : ['type' => 'hidden']);
       echo $this->Form->control('prize2', $number_of_prizes >= 2 ? ['required' => true, 'data-action' => 'keyup->example1--prize#update','label' => __('Second prize')] : ['type' => 'hidden']);
       echo $this->Form->control('prize3', $number_of_prizes >= 3 ? ['required' => true, 'data-action' => 'keyup->example1--prize#update', 'label' => __('Third prize')]  : ['type' => 'hidden']);
    ?>
  </div>
  <?php if ($number_of_prizes >= 3): ?>
  <div class="row honor_podium">
    <div id="prize.out2" class="prizebox"    data-target="example1--prize.out2"><?= h($contest['prize2']) ?></div>
    <div id="prize.out1" class="prizebox up" data-target="example1--prize.out1"><?= h($contest['prize1']) ?></div>
    <div id="prize.out3" class="prizebox"    data-target="example1--prize.out3"><?= h($contest['prize3']) ?></div>
  </div>
  <?php elseif (1 == $number_of_prizes or 2 == $number_of_prizes): ?>
  <div class="row awards">
    <div class="small-5 small-offset-3 columns"><?php echo $this->Html->image('trophy.svg', ['class' => 'trophy1', 'alt' => __('First prize')]); ?></div>
    <div class="small-4 columns"><?php if (2 == $number_of_prizes) echo $this->Html->image('trophy.svg', ['class' => 'trophy2', 'alt' => __('Second prize')]); ?></div>
  </div>
  <div class="row prizetext">
    <div id="prize.out1" class="small-4 small-offset-3 columns prizecaption" data-target="example1--prize.out1"><?= h($contest['prize1']) ?></div>
    <div id="prize.out2" class="small-4 columns prizecaption" data-target="example1--prize.out2"><?= (2 == $number_of_prizes) ? h($contest['prize2']) : '' ?></div>
  </div>
  <?php endif; ?>
 </div>

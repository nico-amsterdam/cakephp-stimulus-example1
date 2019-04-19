<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Form\Example1Form $example1
 * @var string $dateType
 */

$contest = $example1->getData('contest')

?>
 <div class="container" data-controller="example1--price" id="price-panel-div">
  <?php $number_of_prices = $contest['number_of_prices']; ?>
  <div class="formpanel prices total<?= h($number_of_prices) ?>prices"> 
    <?php
       echo $this->Form->control('contest.price1', $number_of_prices >= 1 ? ['required' => true, 'data-action' => 'keyup->example1--price#update', 'label' => __('First price')]  : ['type' => 'hidden']);
       echo $this->Form->control('contest.price2', $number_of_prices >= 2 ? ['required' => true, 'data-action' => 'keyup->example1--price#update','label' => __('Second price')] : ['type' => 'hidden']);
       echo $this->Form->control('contest.price3', $number_of_prices >= 3 ? ['required' => true, 'data-action' => 'keyup->example1--price#update', 'label' => __('Third price')]  : ['type' => 'hidden']);
    ?>
  </div>
  <?php if ($number_of_prices >= 3): ?>
  <div class="row honor_podium">
    <div id="price.out2" class="pricebox"    data-target="example1--price.out2"><?= h($contest['price2']) ?></div>
    <div id="price.out1" class="pricebox up" data-target="example1--price.out1"><?= h($contest['price1']) ?></div>
    <div id="price.out3" class="pricebox"    data-target="example1--price.out3"><?= h($contest['price3']) ?></div>
  </div>
  <?php elseif (1 == $number_of_prices or 2 == $number_of_prices): ?>
  <div class="row awards">
    <div class="small-5 small-offset-3 columns"><?php echo $this->Html->image('trophy.svg', ['class' => 'trophy1', 'alt' => __('First price')]); ?></div>
    <div class="small-4 columns"><?php if (2 == $number_of_prices) echo $this->Html->image('trophy.svg', ['class' => 'trophy2', 'alt' => __('Second price')]); ?></div>
  </div>
  <div class="row pricetext">
    <div id="price.out1" class="small-4 small-offset-3 columns pricecaption" data-target="example1--price.out1"><?= h($contest['price1']) ?></div>
    <div id="price.out2" class="small-4 columns pricecaption" data-target="example1--price.out2"><?= (2 == $number_of_prices) ? h($contest['price2']) : '' ?></div>
  </div>
  <?php endif; ?>
 </div>

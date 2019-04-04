<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Form\Example1Form $example1
 */

$contest = $example1->getData('contest')

?>
 <div class="container" data-controller="price" id="price-panel-div">
  <div class="formpanel prices">
    <?php
       $number_of_prices = $contest['number_of_prices'];
       echo $this->Form->control('contest.price1', $number_of_prices >= 1 ? ['required' => true, 'label' => __('First price')]  : ['type' => 'hidden']);
       echo $this->Form->control('contest.price2', $number_of_prices >= 2 ? ['required' => true,'label' => __('Second price')] : ['type' => 'hidden']);
       echo $this->Form->control('contest.price3', $number_of_prices >= 3 ? ['required' => true,'label' => __('Third price')]  : ['type' => 'hidden']);
    ?>
  </div>
  <?php if ($number_of_prices >= 3): ?>
  <div>
    <div id="price2" class="pricebox"    data-target="price.out2"><?= h($contest['price2']) ?></div>
    <div id="price1" class="pricebox up" data-target="price.out1"><?= h($contest['price1']) ?></div>
    <div id="price3" class="pricebox"    data-target="price.out3"><?= h($contest['price3']) ?></div>
  </div>
  <?php elseif (1 == $number_of_prices or 2 == $number_of_prices): ?>
  <div class="row">
    <div class="small-5 small-offset-3 columns"><?php echo $this->Html->image('trophy.svg', ['class' => 'trophy1', 'alt' => __('First price')]); ?></div>
    <div class="small-4 columns"><?php if (2 == $number_of_prices) echo $this->Html->image('trophy.svg', ['class' => 'trophy2', 'alt' => __('Second price')]); ?></div>
  </div>
  <div class="row">
    <div id="price1.out" class="small-4 small-offset-3 columns pricecaption" data-target="price.out1"><?= h($contest['price1']) ?></div>
    <div id="price2.out" class="small-4 columns pricecaption" data-target="price.out2"><?= (2 == $number_of_prices) ? h($contest['price2']) : '' ?></div>
  </div>
  <?php endif; ?>
 </div>

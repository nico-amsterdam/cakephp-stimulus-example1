<?php
/**
 * @var \App\View\AppView $this
 * @var int $participants_offset
 * @var array $participants
 * @var string $dateType
 * @var string $autofocusIndex
 */
      $dateMin = date('Y') - 120;
      $dateMax = date('Y') - 4;
      $dateMinMax = $dateType === 'date' ?  ['minYear' => $dateMin, 'maxYear' => $dateMax] : ['min' => $dateMin . '-01-01', 'max' => $dateMax . '-12-31'];
      for ($i = $participants_offset, $length = count($participants); $i < $length; $i++): ?>
    <tr>
        <?php
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.name', ['label' => ['text' => __('Participant name'), 'class' => 'show-for-sr'], 'autofocus' => ($i == $autofocusIndex) ]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.email', ['label' => ['text' => __('Participant email address'), 'class' => 'show-for-sr']]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.date_of_birth', $dateMinMax + ['type' => $dateType, 'empty' => true, 'label' => ['text' => __('Participant date of birth'), 'class' => 'show-for-sr']]);
            echo '<td class="trashbin">';
            echo $this->Form->control('contest.participants.' . $i . '.mark_for_deletion', ['type' => 'checkbox', 'title' => __('Delete participant'), 'label' => false]);
            echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'hidden']);
        ?>
    </tr>
<?php endfor; ?>

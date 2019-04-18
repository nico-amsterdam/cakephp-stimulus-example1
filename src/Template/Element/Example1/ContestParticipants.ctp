<?php
/**
 * @var \App\View\AppView $this
 * @var int $participants_offset
 * @var array $participants
 * @var string $dateType
 * @var string $autofocusIndex
 */
        for ($i = $participants_offset, $length = count($participants); $i < $length; $i++): ?>
    <tr>
        <?php
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.name', ['label' => false, 'autofocus' => ($i == $autofocusIndex) ]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.email', ['label' => false]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.date_of_birth', ['type' => $dateType,  'label' => false]);
            echo '<td class="trashbin">';
            echo $this->Form->control('contest.participants.' . $i . '.mark_for_deletion', ['type' => 'checkbox', 'title' => __('Delete participant'), 'label' => false]);
            // echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'checkbox', 'title' => __('t'), 'label' => false]);
            echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'hidden']);
        ?>
    </tr>
    <?php endfor; ?>

<?php
/**
 * @var \App\View\AppView $this
 * @var int $participants_offset
 * @var array $participants
 * @var string $dateType
 */
        for ($i = $participants_offset, $length = $participants_offset + count($participants); $i < $length; $i++): ?>
    <tr>
        <?php
            echo '<td class="trashbin">';
            echo $this->Form->control('contest.participants.' . $i . '.mark_for_deletion', ['type' => 'checkbox', 'title' => __('Delete participant'), 'label' => false]);
            echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'hidden']);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.name', ['label' => false, 'required' => false]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.email', ['label' => false, 'required' => true]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.date_of_birth', ['type' => $dateType,  'label' => false, 'required' => true]);
        ?>
    </tr>
    <?php endfor; ?>
